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
use Perficient\Rabbitmq\Api\Data\TreatmentInterface;
use Perficient\Rabbitmq\Api\Data\TreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\TreatmentRepositoryInterface;
use Perficient\Rabbitmq\Model\ResourceModel\Treatment as ResourceTreatment;
use Perficient\Rabbitmq\Model\ResourceModel\Treatment\CollectionFactory;

/**
 * Class TreatmentRepository
 * @package Perficient\Rabbitmq\Model
 */
class TreatmentRepository implements TreatmentRepositoryInterface
{
    /**
     * TreatmentRepository constructor.
     * @param TreatmentInterfaceFactory $treatmentFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceTreatment $resource,
        private readonly TreatmentInterfaceFactory $treatmentFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {

        $treatmentModel = $this->treatmentFactory->create();
        $this->resource->load($treatmentModel, $id);
        if (!$treatmentModel->getTreatmentId()) {
            throw new NoSuchEntityException(__('Unable to find Treatment with ID %s', $id));
        }
        return $treatmentModel;
    }

    /**
     * @inheritdoc
     */
    public function save(TreatmentInterface $treatment)
    {
        $this->resource->save($treatment);
        return $treatment;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->treatmentFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}