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
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterface;
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\FrameTreatmentRepositoryInterface;
use Perficient\Rabbitmq\Model\ResourceModel\FrameTreatment as ResourceFrameTreatment;
use Perficient\Rabbitmq\Model\ResourceModel\FrameTreatment\CollectionFactory;

/**
 * Class FrameTreatmentRepository
 * @package Perficient\Rabbitmq\Model
 */
class FrameTreatmentRepository implements FrameTreatmentRepositoryInterface
{
    /**
     * FrameTreatmentRepository constructor.
     * @param FrameTreatmentInterfaceFactory $frameTreatmentFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceFrameTreatment $resource,
        private readonly FrameTreatmentInterfaceFactory $frameTreatmentFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {

        $frameTreatmentModel = $this->frameTreatmentFactory->create();
        $this->resource->load($frameTreatmentModel, $id);
        if (!$frameTreatmentModel->getFrameTreatmentId()) {
            throw new NoSuchEntityException(__('Unable to find Frame Treatment with ID %s', $id));
        }
        return $frameTreatmentModel;
    }

    /**
     * @inheritdoc
     */
    public function save(FrameTreatmentInterface $frameTreatment)
    {
        $this->resource->save($frameTreatment);
        return $frameTreatment;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->frameTreatmentFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}