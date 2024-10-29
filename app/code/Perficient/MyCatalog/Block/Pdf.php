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
use Magento\Framework\View\Result\PageFactory;
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\MyCatalog\Api\Data\MyCatalogInterfaceFactory;

/**
 * Class Pdf
 * @package Perficient\MyCatalog\Block
 */
class Pdf extends Template
{
    /**
     * @var
     */
    private $catalogId;

    private array $catalog = [];

    /**
     * MyCatalog constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param MyCatalogInterfaceFactory $myCatalogFactory
     */
    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly Data $helper,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly Json $json,
        private readonly MyCatalogInterfaceFactory $myCatalogFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * CatalogId setter.
     *
     * @param int
     */
    public function setCatalogId($catalogId)
    {
        $this->catalogId = $catalogId;
    }

    /**
     * CatalogId getter.
     *
     * @return int
     */
    public function getCatalogId()
    {
        return $this->catalogId;
    }

    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * Load all required catalog data into catalog property.
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadCatalog()
    {
        if ($this->getCatalogId()) {
            $catalogId = $this->getCatalogId();
            $catalogData = $this->myCatalogRepository->getById($catalogId);
            $pageData = $this->helper->getPageData($catalogId);

            $myCatalog = $this->myCatalogFactory->create();

            $logo = $this->helper->getMediaUrl() . Data::CATALOG_LOGO_PATH . $catalogData->getLogoImage();
            $this->catalog = [
                'title'             => $catalogData->getCatalogTitle(),
                'logo'              => $logo,
                'logo_image'        => $catalogData->getLogoImage(),
                'pages'             => $pageData,
                'name'              => $catalogData->getName(),
                'phone'             => $catalogData->getPhoneNumber(),
                'url'               => $catalogData->getWebsiteUrl(),
                'company'           => $catalogData->getCompanyName(),
                'catalogImages'     => $myCatalog->getGalleryImages($catalogId),
                'additional_info_1' => $catalogData->getData('additional_info_1'),
                'additional_info_2' => $catalogData->getData('additional_info_2')
            ];
        }

        return $this->catalog;
    }

    /**
     * Front page HTML.
     *
     * @return string
     */
    public function getFrontPage()
    {

        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(
            \Perficient\MyCatalog\Block\MyPages::class,
            'mycatalog.pdf.frontpage'
        );

        $block->setData('catalogLogoImage', $this->catalog['logo_image']);
        $block->setData('catalogImage', $this->catalog['logo']);
        $block->setData('catalogTitle', $this->catalog['title']);
        $block->setData('catalogName', $this->catalog['name']);
        $block->setData('additional_info_1', $this->catalog['additional_info_1']);
        $block->setData('additional_info_2', $this->catalog['additional_info_2']);
        $block->setTemplate('pdf_templates/frontpage.phtml');

        return $block->toHtml();
    }

    /**
     * Back page HTML.
     *
     * @return string
     */
    public function getBackPage()
    {
        $resultPage = $this->resultPageFactory->create();
        $block = $resultPage->getLayout()->createBlock(
            \Perficient\MyCatalog\Block\MyPages::class,
            'mycatalog.pdf.backpage'
        );

        $block->setData('catalogName', $this->catalog['name']);
        $block->setData('catalogPhone', $this->catalog['phone']);
        $block->setData('catalogUrl', $this->catalog['url']);
        $block->setData('catalogCompany', $this->catalog['company']);
        $block->setTemplate('pdf_templates/backpage.phtml');

        return $block->toHtml();
    }

    /**
     * HTML data for complete catalog.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHtmlData()
    {
        $this->setCatalogId($this->getData('catalogId'));
        $catalog = $this->loadCatalog();

        $output = '';
//        $output .= $this->getFrontPage();
//
//        $templateData = [];
//        $templateData['images'] = $this->catalog['catalogImages'];
//
//        $resultPage = $this->resultPageFactory->create();
//        foreach ($catalog['pages'] as $key => $page) {
//            if ($page['page_position'] == null || $page['page_position'] == 0) continue;
//            $templateData['page'] = $page['page_position'];
//            $templateData['data'] = $this->json->unserialize($page['drop_spot_config']);
//            $block = $resultPage->getLayout()->createBlock(
//                \Perficient\MyCatalog\Block\MyPages::class,
//                'mycatalog.pdf.templatepage' . $key
//            );
//            $block->setData('templateData', $templateData);
//            $block->setTemplate('pdf_templates/template' . $page['page_template_id'] . '.phtml');
//            $output .= $block->toHtml();
//        }
//
//        $output .= $this->getBackPage();
        return $output;
    }
}
