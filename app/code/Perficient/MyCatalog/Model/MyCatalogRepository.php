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
use Perficient\MyCatalog\Api\Data\MyCatalogInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogSearchResultsInterfaceFactory;
use Perficient\MyCatalog\Api\Data\MyCatalogSearchResultsInterface;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogInterfaceFactory;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalog as ResourceMyCatalog;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalog\CollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Wishlist;

/**
 * Class MyCatalog
 * @package Perficient\MyCatalog\Model
 */
class MyCatalogRepository implements MyCatalogRepositoryInterface
{
    /**
     * Constant for wishlist table.
     */
    const WISHLIST_TABLE = 'wishlist';

    /**
     * MyCatalogRepository constructor.
     *
     * @param MyCatalogSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     * @param Wishlist $wishlist
     * @param MyCatalogInterfaceFactory $myCatalogFactory
     */
    public function __construct(
        private readonly MyCatalogSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly CollectionFactory $collectionFactory,
        private readonly ResourceMyCatalog $resource,
        private readonly Wishlist $wishlist,
        private readonly MyCatalogInterfaceFactory $myCatalogFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $myCatalogModel = $this->myCatalogFactory->create();
        $this->resource->load($myCatalogModel, $id);
        if (!$myCatalogModel->getCatalogId()) {
            throw new NoSuchEntityException(__('Unable to find my-catalog with ID %s', $id));
        }

        return $myCatalogModel;
    }

    /**
     * @inheritdoc
     */
    public function delete(MyCatalogInterface $catalog)
    {
        try {
            $myCatalogModel = $this->myCatalogFactory->create();
            $this->resource->load($myCatalogModel, $catalog->getCatalogId());
            $this->resource->delete($myCatalogModel);
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

        $collection->getSelect()->joinInner(
            ['wishlist' => $this->wishlist->getTable(self::WISHLIST_TABLE)],
            'main_table.wishlist_id = wishlist.wishlist_id',
            ['wishlist.name AS project_name']
        );

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }


    /**
     * @inheritdoc
     */
    public function save(MyCatalogInterface $myCatalog)
    {
        $this->resource->save($myCatalog);
        return $myCatalog;
    }

    /**
     * @inheritdoc
     */
    public function getSharedCatalogList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $collection->getSelect()->joinInner(
            ['catalog_share' => $this->wishlist->getTable('perficient_customer_catalog_share')],
            'main_table.catalog_id = catalog_share.catalog_id',
            []
        );

        $collection->getSelect()->joinInner(
            ['customer' => $this->wishlist->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['CONCAT(customer.firstname, " ", customer.lastname) AS shared_by_email']
        );
        $collection->getSelect()->joinInner(
            ['wishlist' => $this->wishlist->getTable(self::WISHLIST_TABLE)],
            'main_table.wishlist_id = wishlist.wishlist_id',
            ['wishlist.name AS project_name']
        );

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function markAsSharedCatalog($customerId, $catalogId, $priceMultiplier)
    {
        return $this->resource->markAsSharedCatalog($customerId, $catalogId, $priceMultiplier);
    }


    /**
     * @inheritdoc
     */
    public function isSharedCatalog($catalogId, $customerId) {
        return $this->resource->isSharedCatalog($catalogId, $customerId);
    }
}
