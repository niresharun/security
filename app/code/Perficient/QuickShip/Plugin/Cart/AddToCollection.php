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

use Perficient\QuickShip\Helper\Data as PerficientOrderQuickShipHelper;
use Magento\Framework\App\RequestInterface;
use Perficient\Checkout\Controller\Product\AddToCollection as AddToCollectionController;

/**
 * Plugin when product is added to cart
 */
class AddToCollection
{
    /**
     * Plugin constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly PerficientOrderQuickShipHelper $perficientQuickShipHelper
    ) {
    }

    /**
     * @param AddToCollectionController $subject
     */
    public function beforeExecute($subject)
    {
        $productId = $this->request->getParam('product');
        $this->perficientQuickShipHelper->restrictCartForAddCollection($productId);
    }
}
