<?php
/**
 * Add sets to My Projects (Wishlist)
 * @category: Magento
 * @package: Perficient/WishlistSet
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_WishlistSet
 */

declare(strict_types=1);

namespace Perficient\WishlistSet\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Perficient\Company\Helper\Data as CompanyHelper;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistCollectionFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Data extends AbstractHelper
{
    const DEFAULT_WISHLIST_NAME = 'MY FAVORITES';

    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(
        Context                                    $context,
        private readonly CompanyHelper             $companyHelper,
        private readonly WishlistCollectionFactory $_wishlistCollectionFactory,
        private readonly PriceHelper               $priceHelper,
        private readonly Session                   $customerSession
    )
    {
        parent::__construct($context);
    }

    public function actionStatus()
    {
        $currentUserRole = $this->companyHelper->getCurrentUserRole();
        $currentUserRole = html_entity_decode((string)$currentUserRole, ENT_QUOTES);
        $status = TRUE;
        if ($currentUserRole == CompanyHelper::CUSTOMER_CUSTOMER) {
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @param $wishlistId
     * @return Collection
     */
    public function getWishlistByCustomerId($wishlistId)
    {
        return $this->_wishlistCollectionFactory->create()
            ->addFieldToFilter('wishlist_id', ['eq' => $wishlistId]);
    }

    /**
     * @param $price
     */
    public function getFormattedPrice($price): float|string
    {
        return $this->priceHelper->currency($price, true, false);
    }

    public function getCustomerSession()
    {
        return $this->customerSession;
    }
}
