<?php
/**
 * Helper for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends \Magento\MultipleWishlist\Helper\Data
{
    /**
     * Data constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context                            $context,
        \Magento\Framework\Registry                                      $coreRegistry,
        \Magento\Customer\Model\Session                                  $customerSession,
        \Magento\Wishlist\Model\WishlistFactory                          $wishlistFactory,
        \Magento\Store\Model\StoreManagerInterface                       $storeManager,
        \Magento\Framework\Data\Helper\PostHelper                        $postDataHelper,
        \Magento\Customer\Helper\View                                    $customerViewHelper,
        \Magento\Wishlist\Controller\WishlistProviderInterface           $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface                  $productRepository,
        \Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory     $itemCollectionFactory,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $_wishlistCollectionFactory,
        protected SearchCriteriaBuilder                                  $searchCriteriaBuilder,
        protected CustomerRepositoryInterface                            $customerRepository,
        private readonly SerializerInterface                             $serializer
    )
    {
        $this->wishlistProvider = $wishlistProvider;
        parent::__construct(
            $context,
            $coreRegistry,
            $customerSession,
            $wishlistFactory,
            $storeManager,
            $postDataHelper,
            $customerViewHelper,
            $wishlistProvider,
            $productRepository,
            $itemCollectionFactory,
            $_wishlistCollectionFactory
        );
    }

    /**
     * @param null $customerId
     * @return mixed
     */
    public function getCustomerCollaborationWishlists($customerId = null)
    {
        $wishlistsByCustomer = [];
        if (!$customerId && $this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
        }

        /**
         * If customer does not exists then do not check for wishlist and return blank.
         * This also solve the browser page unresponsive issue.
         */
        if (!$customerId) {
            return [];
        }

        /** @var \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection $collection */
        $collection = $this->_wishlistCollectionFactory->create();
        if ($customerId) {
            $collection->getSelect()->where("FIND_IN_SET($customerId,collaboration_ids)");
        }
        $wishlistsByCustomer[$customerId] = $collection;
        return $wishlistsByCustomer[$customerId];
    }

    /**
     * @param $emails
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerIdsFromEmails($emails)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('email', $emails, 'in')->create();
        $customerData = $this->customerRepository->getList($searchCriteria)->getItems();
        $customerIds = [];
        if ($customerData) {
            foreach ($customerData as $customer) {
                $customerIds[] = $customer->getId();
            }
        }
        return implode(",", $customerIds);

    }

    /**
     * @param null $customerId
     * @return mixed
     * @throws \Exception
     */
    public function getCombinedWishlist($customerId = null)
    {
        $wishlistsByCustomer = [];
        if (!$customerId && $this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
        }

        /** @var \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection $collection */
        $collection = $this->_wishlistCollectionFactory->create();
        $condition = "customer_id = $customerId or FIND_IN_SET($customerId,collaboration_ids)";
        $collection->getSelect()->where($condition);

        if ($customerId && !$collection->getItems()) {
            $wishlist = $this->addWishlist($customerId);
            $collection->addItem($wishlist);
        }
        $wishlistsByCustomer[$customerId] = $collection;
        return $wishlistsByCustomer[$customerId];

    }

    /**
     * @param null $itemID
     * @return mixed
     */
    public function getSideMark($itemID)
    {
        if (empty($itemID)) {
            return '';
        }
        $wishlist = $this->wishlistProvider->getWishlist();
        if (empty($wishlist?->getItem($itemID)?->getBuyRequest())) {
            return '';
        }

        $pzData = $wishlist->getItem($itemID)->getBuyRequest()->getData();
        if (empty($pzData['pz_cart_properties'])) {
            return '';
        }
        $pzArray = $this->serializer->unserialize($pzData['pz_cart_properties']);
        if (empty($pzArray['Side Mark'])) {
            return '';
        }
        return $pzArray['Side Mark'];
    }

}
