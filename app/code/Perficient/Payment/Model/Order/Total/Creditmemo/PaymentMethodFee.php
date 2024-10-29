<?php
/**
 * Set payment method fee while generating creditmemo
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

namespace Perficient\Payment\Model\Order\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Psr\Log\LoggerInterface;

class PaymentMethodFee extends AbstractTotal
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Credit Memo Fee constructor.
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        LoggerInterface $loggerInterface
    ) {
        $this->logger = $loggerInterface;
    }

    /**
     * @param Creditmemo $creditMemo
     * @return $this
     */
    public function collect(Creditmemo $creditMemo)
    {
        try {
            $order = $creditMemo->getOrder();
            $feeAmountInvoiced = $order->getPaymentMethodFeeInvoiced();

            if ($order && $order->getId() &&
                isset($feeAmountInvoiced) && !empty($feeAmountInvoiced)) {
                $creditMemo->setGrandTotal($creditMemo->getGrandTotal() + $feeAmountInvoiced);
                $creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() + $feeAmountInvoiced);
                $creditMemo->setPaymentMethodFee($feeAmountInvoiced);

                // Set fee amount refunded into order
                $order->setPaymentMethodFeeRefunded($feeAmountInvoiced);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
