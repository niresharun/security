<?php
/**
 * Custom Table Data Management
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\Rabbitmq\Api\Data\MediaInterface;
use Perficient\Rabbitmq\Api\Data\MediaInterfaceFactory;
use Perficient\Rabbitmq\Api\MediaRepositoryInterface;
use Perficient\Rabbitmq\Model\ResourceModel\Media as ResourceMedia;
use Perficient\Rabbitmq\Model\ResourceModel\Media\CollectionFactory;

/**
 * Class MediaRepository
 * @package Perficient\Rabbitmq\Model
 */
class MediaRepository implements MediaRepositoryInterface
{
    /**
     * MediaRepository constructor.
     * @param MediaInterfaceFactory $mediaFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceMedia $resource,
        private readonly MediaInterfaceFactory $mediaFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $mediaModel = $this->mediaFactory->create();
        $this->resource->load($mediaModel, $id);
        if (!$mediaModel->getMediaId()) {
            throw new NoSuchEntityException(__('Unable to find Media with ID %s', $id));
        }
        return $mediaModel;
    }

    /**
     * @inheritdoc
     */
    public function save(MediaInterface $media)
    {
        $this->resource->save($media);
        return $media;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->mediaFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}