<?php
/**
 * Observer to set is_quick_ship to 0 in case quickship product qty become 0
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.Mude@Perficient.com>
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);

namespace Perficient\QuickShip\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Catalog\Api\ProductRepositoryInterface;

class OrderPlaceObserver implements ObserverInterface
{
    /**
     * OrderPlaceObserver constructor.
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        private readonly ProductAction $productAction,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $idArray = [];
        $quickShip = $quote->getQuickShip();
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $item)
        {
            $productId = $item->getProductId();
            $sku = $item->getSku();
            $salable = $this->getSalableQuantityDataBySku->execute($sku);
            if($quickShip && $salable[0]['qty'] < 1) {
                $idArray[] = $productId;
            }
        }
        if ($idArray) {
            $this->productAction->updateAttributes($idArray, ['is_quick_ship' => 0], 0);
        }
    }

}
