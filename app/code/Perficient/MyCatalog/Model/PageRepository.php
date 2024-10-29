<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey Pali <Kartikey.Pali@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\MyCatalog\Api\Data\PageSearchResultsInterfaceFactory;
use Perficient\MyCatalog\Api\PageRepositoryInterface;
use Perficient\MyCatalog\Api\Data\PageInterfaceFactory;
use Perficient\MyCatalog\Api\Data\PageInterface;
use Perficient\MyCatalog\Model\ResourceModel\Page as ResourcePage;
use Perficient\MyCatalog\Model\ResourceModel\Page\CollectionFactory;

/**
 * Class PageRepository
 * @package Perficient\MyCatalog\Model
 */
class PageRepository implements PageRepositoryInterface
{
    /**
     * PageRepository constructor.
     *
     * @param PageSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     * @param PageInterfaceFactory $pageFactory
     */
    public function __construct(
        private readonly PageSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly CollectionFactory $collectionFactory,
        private readonly ResourcePage $resource,
        private readonly PageInterfaceFactory $pageFactory
    ){
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $pageModel = $this->pageFactory->create();
        $this->resource->load($pageModel, $id);
        if (!$pageModel->getId()) {
            throw new NoSuchEntityException(__('Unable to find page with ID %s', $id));
        }

        return $pageModel;
    }

    /**
     * @inheritdoc
     */
    public function delete(PageInterface $page)
    {
        try {
            $pageModel = $this->pageFactory->create();
            $this->resource->load($pageModel, $page->getPageId());
            $this->resource->delete($pageModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the page: %1',
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
    public function save(PageInterface $page)
    {
        $this->resource->save($page);
        return $page;
    }

    /**
     * @inheritdoc
     */
    public function getCatalogPageID($catalogId, $pagePosition)
    {
        /** @var Page $pageModel */
        $pageModel = $this->pageFactory->create();
        $pageId = $pageModel->getCatalogPageID($catalogId, $pagePosition);
        return $pageId;
    }
}
