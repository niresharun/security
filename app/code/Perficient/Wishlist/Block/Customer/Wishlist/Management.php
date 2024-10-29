<?php
/**
 * Override block for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Block\Customer\Wishlist;

use Magento\Framework\View\Element\Template\Context;
use Magento\MultipleWishlist\Helper\Data as MultiWishlistHelper;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Perficient\Wishlist\Helper\Data;

class Management extends \Magento\MultipleWishlist\Block\Customer\Wishlist\Management
{
    /**
     * Management constructor.
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        Context               $context,
        MultiWishlistHelper   $wishlistData,
        CurrentCustomer       $currentCustomer,
        private readonly Data $helper,
        array                 $data = []
    )
    {
        parent::__construct($context, $wishlistData, $currentCustomer, $data);
    }

    /**
     * Retrieve wishlist collection
     *
     * @return Collection
     */
    public function getWishlists()
    {
        return $this->helper->getCustomerCollaborationWishlists($this->_getCustomerId());
    }


    /**
     * Retrieve currently selected wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getCurrentWishlist()
    {
        if (!$this->_current) {
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_current = $this->getWishlists()->getItemById($wishlistId);
            } else {
                $this->_current = $this->getDefaultWishlist();
            }
        }
        return $this->_current;
    }
}
