<?php
/**
 * Controller for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\CustomerData;

class MultipleWishlist extends \Magento\MultipleWishlist\CustomerData\MultipleWishlist
{
    /**
     * MultipleWishlist constructor.
     */
    public function __construct(
        \Magento\MultipleWishlist\Helper\Data             $wishlistHelper,
        \Magento\Customer\Helper\Session\CurrentCustomer  $currentCustomer,
        private readonly \Perficient\Wishlist\Helper\Data $helper
    )
    {
        parent::__construct($wishlistHelper, $currentCustomer);
    }

    /**
     * @return array
     */
    protected function getWishlistShortList()
    {
        $wishlistData = [];
        foreach ($this->wishlistHelper->getCustomerWishlists() as $wishlist) {
            $wishlistData[] = ['id' => $wishlist->getId(), 'name' => $wishlist->getName()];
        }

        foreach ($this->helper->getCustomerCollaborationWishlists() as $collabWishlist) {
            $wishlistData[] = ['id' => $collabWishlist->getId(), 'name' => $collabWishlist->getName(), 'page_type' => 'collaboration'];
        }
        return $wishlistData;
    }
}
