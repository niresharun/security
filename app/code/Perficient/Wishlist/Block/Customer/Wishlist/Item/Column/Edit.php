<?php
/**
 * overide for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Block\Customer\Wishlist\Item\Column;


class Edit extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $product
     * @return string
     */
    public function getItemConfigureUrl($product)
    {
        return $this->getUrl(
            'wishlist/index/configure',
            [
                'id' => $product->getWishlistItemId(),
                'product_id' => $product->getProductId(),
                'page_type' => 'collaboration'
            ]
        );
    }

}
