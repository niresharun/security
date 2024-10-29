<?php
/**
 * Quickship requested qty validation on place order
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<sandeep.mude@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);

namespace Perficient\QuickShip\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

class BeforeSubmitCheckoutObserver implements ObserverInterface
{
    /**
     * BeforeSubmitCheckoutObserver constructor.
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     */
    public function __construct(
        private readonly GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $quickShip = $quote->getQuickShip();
        if ($quickShip) {
            $quoteItems = $quote->getAllItems();
            foreach ($quoteItems as $item)
            {
                $sku = $item->getSku();
                $qty = $item->getQty();
                $salable = $this->getSalableQuantityDataBySku->execute($sku);
                if($qty > $salable[0]['qty']) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The requested qty for %1 is not available.', $item->getName()));
                }
            }
        }
    }

}
