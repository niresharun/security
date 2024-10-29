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
use Perficient\Rabbitmq\Api\Data\MediaTreatmentInterface;
use Perficient\Rabbitmq\Api\Data\MediaTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\MediaTreatmentRepositoryInterface;
use Perficient\Rabbitmq\Model\ResourceModel\MediaTreatment as ResourceMediaTreatment;
use Perficient\Rabbitmq\Model\ResourceModel\MediaTreatment\CollectionFactory;

/**
 * Class MediaTreatmentRepository
 * @package Perficient\Rabbitmq\Model
 */
class MediaTreatmentRepository implements MediaTreatmentRepositoryInterface
{
    /**
     * MediaTreatmentRepository constructor.
     * @param MediaTreatmentInterfaceFactory $mediaTreatmentFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceMediaTreatment $resource,
        private readonly MediaTreatmentInterfaceFactory $mediaTreatmentFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $mediaTreatmentModel = $this->mediaTreatmentFactory->create();
        $this->resource->load($mediaTreatmentModel, $id);
        if (!$mediaTreatmentModel->getMediaTreatmentId()) {
            throw new NoSuchEntityException(__('Unable to find Media Treatment with ID %s', $id));
        }
        return $mediaTreatmentModel;
    }

    /**
     * @inheritdoc
     */
    public function save(MediaTreatmentInterface $mediaTreatment)
    {
        $this->resource->save($mediaTreatment);
        return $mediaTreatment;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->mediaTreatmentFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}