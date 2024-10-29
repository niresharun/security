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
use Perficient\Rabbitmq\Api\BaseCostRepositoryInterface;
use Perficient\Rabbitmq\Api\Data\BaseCostInterface;
use Perficient\Rabbitmq\Api\Data\BaseCostInterfaceFactory;
use Perficient\Rabbitmq\Model\ResourceModel\BaseCost as ResourceBaseCost;
use Perficient\Rabbitmq\Model\ResourceModel\BaseCost\CollectionFactory;

/**
 * Class BaseCostRepository
 * @package Perficient\Rabbitmq\Model
 */
class BaseCostRepository implements BaseCostRepositoryInterface
{
    /**
     * BaseCostRepository constructor.
     * @param BaseCostInterfaceFactory $baseCostFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceBaseCost $resource,
        private readonly BaseCostInterfaceFactory $baseCostFactory,
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $baseCostModel = $this->baseCostFactory->create();
        $this->resource->load($baseCostModel, $id);
        if (!$baseCostModel->getBaseCostId()) {
            throw new NoSuchEntityException(__('Unable to find Base Cost with ID %s', $id));
        }
        return $baseCostModel;
    }

    /**
     * @inheritdoc
     */
    public function save(BaseCostInterface $baseCost)
    {
        $this->resource->save($baseCost);
        return $baseCost;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->baseCostFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}