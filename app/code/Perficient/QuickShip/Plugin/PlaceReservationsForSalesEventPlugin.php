<?php
/**
 * Inventory for Quick Ship
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude <Sandeep.mude@perficient.com>
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);

namespace Perficient\QuickShip\Plugin;

use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderRepository;

class PlaceReservationsForSalesEventPlugin
{
    public function __construct(
        private readonly Session $checkoutSession,
        private readonly OrderRepository $orderRepository
    ) {
    }

    /**
     * @param PlaceReservationsForSalesEventInterface $subject
     * @param SalesChannelInterface $salesChannel
     * @param SalesEventInterface $salesEvent
     * @return null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundExecute(
        PlaceReservationsForSalesEventInterface $subject,
        callable $proceed,
        array $items,
        SalesChannelInterface $salesChannel,
        SalesEventInterface $salesEvent
    ) {
        if( $salesEvent->getType() == 'order_placed') {
            if (!$this->checkoutSession->getQuote()->getQuickShip()) {
                return null;
            }
            return $proceed($items, $salesChannel, $salesEvent);
        } else {
            $orderId = $salesEvent->getObjectId();
            if ($orderId) {
                $order = $this->orderRepository->get($orderId);
                if (!$order->getQuickShip()) {
                    return null;
                }
                return $proceed($items, $salesChannel, $salesEvent);
            }
            return null;
        }
    }

}
