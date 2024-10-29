<?php
/**
 * Set payment method fee in order
 * @category: Magento
 * @package: Perficient/Payment
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar <Amin.Akhtar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Payment
 */
declare(strict_types=1);

namespace Perficient\Payment\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class AddPaymentFeeToOrderObserver implements ObserverInterface
{
    /**
     * AddPaymentFeeToOrderObserver constructor.
     */
    public function __construct(
        private readonly LoggerInterface $loggerInterface
    ) {
    }

    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $feeAmount = $quote->getPaymentMethodFee();
            $quotePayment = $quote->getPayment();
            $method  = $quotePayment->getMethod();
            if ($quote && $order && isset($feeAmount) && !empty($feeAmount) && $method == 'authnetcim') {
                //Set fee data to orde
                $order->setData('payment_method_fee', $feeAmount);
            }
        } catch (\Exception $e) {
            $this->loggerInterface->error($e->getMessage());
        }

        return $this;
    }
}
