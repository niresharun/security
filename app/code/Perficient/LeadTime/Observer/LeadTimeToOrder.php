<?php
/**
 * This file is used to enable the lead time notifications module.
 *
 * @category: Magento
 * @package: Perficient/LeadTime
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suraj Jaiswal <suraj.jaiswal@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_LeadTime LeadTime CMS Block
 */
declare(strict_types=1);

namespace Perficient\LeadTime\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

/**
 * Class LeadTimeToOrder
 * @package Perficient\LeadTime\Observer
 */
class LeadTimeToOrder implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        /* @var Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $order->setData('lead_time', $quote->getData('lead_time'));
    }
}

