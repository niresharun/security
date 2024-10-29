<?php
/**
 * Add product to cart and keep in wishlist as well
 * @category: Magento
 * @package: Perficient/WishlistSet
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_WishlistSet
 */
declare(strict_types=1);

namespace Perficient\WishlistSet\Plugin;

use Magento\Wishlist\Model\Item;

class ItemPlugin
{
    /**
     * @param Item $subject
     * @param bool $delete
     * @return array
     */
    public function beforeAddToCart(Item $subject, \Magento\Checkout\Model\Cart $cart, $delete = false)
    {
        $delete = false;
        return [$cart, $delete];
    }

}