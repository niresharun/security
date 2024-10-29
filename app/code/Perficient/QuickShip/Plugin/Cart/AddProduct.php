<?php
/**
 * Inventory for Quick Ship
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);
namespace Perficient\QuickShip\Plugin\Cart;

use Magento\Checkout\Model\Cart;
use Magento\Framework\DataObject;
use Perficient\QuickShip\Helper\Data as PerficientOrderQuickShipHelper;
use Magento\Framework\App\RequestInterface;

/**
 * Plugin when product is added to cart
 */
class AddProduct
{
    /**
     * Plugin constructor.
     */
    public function __construct(
        private readonly PerficientOrderQuickShipHelper $perficientQuickShipHelper
    ) {
    }

    /**
     * @param Cart $subject
     * @param $productInfo
     * @param DataObject|int|array|null $requestInfo
     * @return array
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        $this->perficientQuickShipHelper->restrictCart($productInfo, $requestInfo);
        return [$productInfo, $requestInfo];
    }
}
