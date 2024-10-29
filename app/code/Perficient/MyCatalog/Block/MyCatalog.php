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

use Magento\Company\Api\AuthorizationInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Model\SessionFactory;
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Wishlist\Model\ResourceModel\Wishlist as WishistResourceModel;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class MyCatalog
 * @package Perficient\MyCatalog\Block
 */
class MyCatalog extends Template
{
    /**
     * Constant for authorization
     */
    const AUTH_CATALOG = 'Perficient_MyCatalog::view';

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * MyCatalog constructor.
     *
     * @param Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param FilterBuilder $filterBuilder
     * @param SessionFactory $customerSession
     * @param RequestInterface $request
     * @param CompanyManagementInterface $companyManagementInterface
     * @param AuthorizationInterface $authorization
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(
        Context $context,
        private readonly Data $helper,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SortOrderBuilder $sortOrderBuilder,
        private readonly FilterBuilder $filterBuilder,
        SessionFactory $customerSession,
        private readonly RequestInterface $request,
        private readonly CompanyManagementInterface $companyManagementInterface,
        private readonly AuthorizationInterface $authorization,
        private readonly WishlistFactory $wishlistFactory,
        private readonly WishistResourceModel $wishListResourceModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession->create();
    }

    /**
     * Set the page title.
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Catalogs'));
        return AbstractBlock::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getMyCatalogs()
    {
        // Filter by customer.
        $filters = [
            $this->filterBuilder
                ->setField('main_table.customer_id')
                ->setValue($this->customerSession->getCustomerId())
                ->setConditionType('eq')
                ->create()
        ];

        // Sort by created date.
        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection('DESC')
            ->create();

        // Prepare search criteria builder.
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters($filters)
            ->setSortOrders([$sortOrder])
            ->create();

        // Get the list.
        $myCatalogs = $this->myCatalogRepository->getList($searchCriteria);

        // Return the items.
        return $myCatalogs->getItems();
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
     * Method used to get the formatted time.
     *
     * @param $date
     * @return string
     */
    public function getFormattedTime($date)
    {
        return $this->helper->getFormattedTime($date);
    }

    /**
     * Method used to get the media url.
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->_storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
        ;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCatalogData()
    {
        $catalogId   = $this->request->getParam('catalog_id', 0);
        $catalogData = [
            'catalog_id'        => '',
            'wishlist_id'       => $this->request->getParam('wishlist_id'),
            'logo_image'        => '',
            'catalog_title'     => '',
            'additional_info_1' => '',
            'additional_info_2' => '',
            'name'              => '',
            'phone_number'      => '',
            'website_url'       => '',
            'company_name'      => '',
        ];

        if ($catalogId) {
            $catalog = $this->myCatalogRepository->getById($catalogId);
            $catalogData = [
                'catalog_id'        => $catalog->getCatalogId(),
                'wishlist_id'       => $catalog->getWishlistId(),
                'catalog_title'     => $catalog->getCatalogTitle(),
                'additional_info_1' => $catalog->getAdditionalInfo1(),
                'additional_info_2' => $catalog->getAdditionalInfo2(),
                'name'              => $catalog->getName(),
                'phone_number'      => $catalog->getPhoneNumber(),
                'website_url'       => $catalog->getWebsiteUrl(),
                'company_name'      => $catalog->getCompanyName(),
                'logo_image'        => '',
            ];
            $logoImage = $catalog->getLogoImage();
            if (null !== $logoImage && !empty($logoImage)) {
                $catalogData['logo_image'] = $logoImage;
                $catalogData['logo_image_url'] = $this->getMediaUrl() . Data::CATALOG_LOGO_PATH . $logoImage;
            }
        }

        return $catalogData;
    }

    /**
     * @return mixed
     */
    public function getMySharedCatalogs()
    {
        // Filter by customer.
        $filters = [
            $this->filterBuilder
                ->setField('catalog_share.customer_id')
                ->setValue($this->customerSession->getCustomerId())
                ->setConditionType('eq')
                ->create()
        ];

        // Sort by created date.
        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection('DESC')
            ->create();

        // Prepare search criteria builder.
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters($filters)
            ->setSortOrders([$sortOrder])
            ->create();

        // Get the list.
        $myCatalogs = $this->myCatalogRepository->getSharedCatalogList($searchCriteria);

        // Return the items.
        return $myCatalogs->getItems();
    }

    /**
     * Get Customer Name from Session
     * @return string
     */
    public function getCustomerName() {
        return $this->customerSession->getCustomer()->getName();
    }

    /**
     * Get Store URL
     * @return mixed
     */
    public function getCustomerWebsite() {
        $website = '';
        $companyData = $this->getCompanyData();
        if ($companyData) {
            $website = $companyData->getWebsiteAddress();
        }
        return $website;
    }

    /**
     * Get Customer Company Name
     * @return null|string
     */
    public function getCustomerCompanyName() {
        $companyName = '';
        $companyData = $this->getCompanyData();
        if ($companyData) {
            $companyName = $companyData->getCompanyName();
        }
        return $companyName;
    }

    /**
     * Get Customer Phone Number
     * @return string
     */
    public function getCustomerPhoneNumber() {
        $telephone = '';

        $companyData = $this->getCompanyData();
        if ($companyData) {
            $telephone = $companyData->getTelephone();
        }
        return $telephone;
    }

    public function getCompanyData() {
        $customerId = $this->customerSession->getCustomer()->getId();
        return $this->companyManagementInterface->getByCustomerId($customerId);
    }

    /**
     * Method used to check whether user has access to my catalog or not.
     */
    public function isAllowMyCatalog()
    {
        return $this->authorization->isAllowed(self::AUTH_CATALOG);
    }

    /**
     * Method used to get the default catalog title.
     *
     * @return string
     */
    public function getDefaultCatalogTitle()
    {
        $wishlistFactory = $this->wishlistFactory->create();
        $this->wishListResourceModel->load($wishlistFactory, $this->request->getParam('wishlist_id'));
        return (string)$wishlistFactory->getName();
    }

    /**
     * @return mixed|string
     */
    public function getActionData()
    {
        $params = $this->request->getParams();
        $action = '';
        if(isset($params['action'])) {
            $action = $params['action'];
        }
        return $action;
    }
}
