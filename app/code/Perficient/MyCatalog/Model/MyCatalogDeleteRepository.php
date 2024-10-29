<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteSearchResultsInterfaceFactory;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteSearchResultsInterface;
use Perficient\MyCatalog\Api\MyCatalogDeleteRepositoryInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterfaceFactory;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterface;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalogDelete as ResourceMyCatalog;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalogDelete\CollectionFactory;

/**
 * Class MyCatalog
 * @package Perficient\MyCatalog\Model
 */
class MyCatalogDeleteRepository implements MyCatalogDeleteRepositoryInterface
{
    /**
     * Constant for wishlist table.
     */
    const WISHLIST_TABLE = 'wishlist';

    /**
     * MyCatalogRepository constructor.
     *
     * @param MyCatalogDeleteSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     * @param MyCatalogDeleteInterfaceFactory $myCatalogDeleteFactory
     */
    public function __construct(
        private readonly MyCatalogDeleteSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly CollectionFactory $collectionFactory,
        private readonly ResourceMyCatalog $resource,
        private readonly MyCatalogDeleteInterfaceFactory $myCatalogDeleteFactory
    ){
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $myCatalogDeleteModel = $this->myCatalogDeleteFactory->create();
        $this->resource->load($myCatalogDeleteModel, $id);
        if (!$myCatalogDeleteModel->getDeletionEventId()) {
            throw new NoSuchEntityException(__('Unable to find my-catalog with ID %s', $id));
        }

        return $myCatalogDeleteModel;
    }

    /**
     * @inheritdoc
     */
    public function delete(MyCatalogDeleteInterface $catalogDelete)
    {
        try {
            $myCatalogDeleteModel = $this->myCatalogDeleteFactory->create();
            $this->resource->load($myCatalogDeleteModel, $catalogDelete->getDeletionEventId());
            $this->resource->delete($myCatalogDeleteModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the catalog: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }


    /**
     * @inheritdoc
     */
    public function save(MyCatalogDeleteInterface $myCatalogDelete)
    {
        $this->resource->save($myCatalogDelete);
        return $myCatalogDelete;
    }
}
