<?php
/**
 * Set payment method fee while generating invoice
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

namespace Perficient\Payment\Model\Order\Total\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Psr\Log\LoggerInterface;

class PaymentMethodFee extends AbstractTotal
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Invoice Fee constructor.
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        LoggerInterface $loggerInterface
    ) {
        $this->logger = $loggerInterface;
    }

    /**
     * Collect invoice subtotal
     *
     * @param Invoice $invoice
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function collect(Invoice $invoice)
    {
        try {
            $order = $invoice->getOrder();
            $feeAmount = $order->getPaymentMethodFee();

            if ($order && $order->getId() &&
                isset($feeAmount) && !empty($feeAmount)) {
                $invoice->setPaymentMethodFee($feeAmount);
                $invoice->setGrandTotal($invoice->getGrandTotal() + $feeAmount);
                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $feeAmount);

                $order->setPaymentMethodFeeInvoiced($feeAmount);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
