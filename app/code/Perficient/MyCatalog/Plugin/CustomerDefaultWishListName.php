<?php
/**
 * Added to handle product surcharge if minimum order amount not met by customer
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Plugin;

/**
 * Class CustomerDefaultWishListName
 * @package Perficient\MyCatalog\Plugin
 */
class CustomerDefaultWishListName
{


    public function afterGetDefaultWishlistName()
    {
        return __('MY FAVORITES');
    }
}
