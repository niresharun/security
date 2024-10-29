<?php
/**
 * Set payment method fee during checkout
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

namespace Perficient\Payment\Model\Quote\Address\Total;

use Perficient\Payment\Helper\Data;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Psr\Log\LoggerInterface;

class PaymentMethodFee extends AbstractTotal
{
    /**
     * Payment Fee constructor.
     */
    public function __construct(
        private readonly Data $helperData,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Collect totals process.
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        /*Fee calculation was happening twice for both shipping and billing address
        Below condition check if there are any shipped items available*/
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }

        try {
            $fee = $this->helperData->getPaymentMethodFee($quote, $total);
            if (!empty($fee)) {
                $total->setPaymentMethodFee($fee);

                $total->setTotalAmount('payment_method_fee', $fee);
                $total->setBaseTotalAmount('payment_method_fee', $fee);

                $total->setGrandTotal($total->getGrandTotal());
                $total->setBaseGrandTotal($total->getBaseGrandTotal());

                // Make sure that quote is also updated
                $quote->setPaymentMethodFee($fee);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(
        Quote $quote,
        Total $total
    ) {
        $result =[];
        $quotePayment = $quote->getPayment();
        $method  = $quotePayment->getMethod();
        if($method == 'authnetcim'){
            $result = [
                'code' => $this->getCode(),
                'title' => $this->getLabel(),
                'value' => $total->getPaymentMethodFee()
            ];
        }
        return $result;
    }

    /**
     * Get Payment Fee label
     *
     * @return Phrase
     */
    public function getLabel()
    {
        $paymentFeeLabel = $this->helperData->getConfigLabel();
        return __($paymentFeeLabel);
    }
}
