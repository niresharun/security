<?php
/**
 * This module is used to create custom artwork catalogs
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

namespace Perficient\MyCatalog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Api\Data\MyCatalogInterfaceFactory;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class MyPages
 * @package Perficient\MyCatalog\Block
 */
class MyPages extends Template
{
    /**
     * @var
     */
    protected $pageData = [];

    /**
     * MyPages constructor.
     * @param Context $context
     * @param MyCatalogInterfaceFactory $myCatalogFactory
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        private readonly Data $helper,
        private readonly MyCatalogInterfaceFactory $myCatalogFactory,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        protected PageRepositoryInterface $pageRepositoryInterface,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly LoggerInterface $logger,
        array $data = []
    ){
        parent::__construct($context, $data);
    }

    /**
     * Set the page title.
     *
     * @return AbstractBlock
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Create Catalog Pages'));
        return AbstractBlock::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getMyCatalogs()
    {
        return $this->myCatalogFactory->create()->getCollection();
    }

    /**
     * Method used to get the gallery name lists.
     *
     * @return array
     */
    public function getGalleryNamesLists()
    {
        return $this->helper->getGalleryNamesLists();
    }

    /**
     * Method used to get the formatted date.
     *
     * @param $date
     * @return string
     */
    public function getFormattedDate($date)
    {
        return $this->helper->getFormattedDate($date);
    }

    /**
     * Method used to get the media url.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->helper->getMediaUrl();
    }

    /**
     * Method used to return page number of template of particular catalog page.
     *
     * @return int
     */
    public function getPageNumber()
    {
        if (!$this->pageData) {
            $this->getPageData();
        }
        return count($this->pageData) ?: 1;
    }

    /**
     * Method used to get the page data for the current catalog.
     * @return void
     */
    public function getPageData()
    {
        $catalogId = $this->getRequest()->getParam('catalog_id');
        $this->pageData = $this->helper->getPageData($catalogId);
    }

    /**
     * Method used to return selected template for first page.
     *
     * @param int $pageId
     * @return int
     */
    public function getPageTemplate($pageId = 1)
    {
        if (!$this->pageData) {
            $this->getPageData();
        }
        $template = 1;
        if (isset($this->pageData) && is_array($this->pageData)) {
            foreach ($this->pageData as $pageData) {
                if (isset($pageData['page_position']) && $pageData['page_position'] == $pageId) {
                    $template = $pageData['page_template_id'];
                }
            }
        }
        return $template;
    }

    /**
     * Returns the list of all images from particular gallery/wishlist.
     *
     * @return array
     */
    public function getGalleryImages()
    {
        $catalogId = $this->getRequest()->getParam('catalog_id');
        $myCatalog = $this->myCatalogFactory->create();
        return $myCatalog->getGalleryImages($catalogId);
    }

    /**
     * @param $p
     * @param $images
     * @param bool $pdf
     * @return bool
     */
    public function getImage($key, $images, $pdf = false)
    {
        if (!isset($images[$key])) {
            return false;
        }

        $url = $images[$key]['url'];

        if ($pdf) {
            return $url;
        }

        return '<img src="' . $url . '"
            alt="' . $key . '"
            class="wendover_thumb draggable ' . $key . '"
            id="' . uniqid() . '"
            width="158px"
            tabindex="0"
            moved="true"
            aria-grabbed="false"
            draggable="true"
            parent="dropspot_' . ltrim((string) $key, 'p') . '"
        />';
    }

    /**
     * Method used to get the catalog data.
     *
     * @return \Perficient\MyCatalog\Api\Data\MyCatalogInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCatalogData()
    {
        $catalogId = $this->getRequest()->getParam('catalog_id');
        return $this->myCatalogRepository->getById($catalogId);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLogoImagePath()
    {
        return $this->helper->getMediaUrl() . Data::CATALOG_LOGO_PATH . '/';
    }

    /**
     * @param $filePath
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFileContents($filePath)
    {
        $module = 'Perficient_MyCatalog';
        return $this->helper->getFileContents($module, $filePath);
    }

    /**
     * Method used to get current currency symbol.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencySymbol()
    {
        return $this->helper->getCurrencySymbol();
    }

    /**
     * @param $urlKey
     * @return string
     */
    public function getCmsPageDetails($urlKey)
    {
        if (!empty($urlKey)) {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $urlKey, 'eq')->create();
            $pages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
            return $pages;
        } else {
            return 'Page URL Key is invalid';
        }
    }

    /**
     * Returns the name of the actual catalog.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCatalogName()
    {
        $catalog = $this->getCatalogData();
        return $catalog->getCatalogTitle();
    }
}
