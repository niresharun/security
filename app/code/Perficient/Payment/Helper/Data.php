<?php
/**
 * Calculate payment processing fee
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

namespace Perficient\Payment\Helper;

use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{
    const AUTHNETCIM_CODE = 'authnetcim';
    /**
     * payment config path
     */
    const CONFIG_PATH_MODULE_PAYMENT = 'payment/';

    const PAYMENT_FEE_FIELD = '/payment_method_fee';

    /**
     * Payment method fee label config path
     */
    const PAYMENT_FEE_LABEL_PATH = 'sales/general/payment_method_fee_label';

    /**
     * Payment method fee debugging config path
     */
    const PAYMENT_FEE_DEBUGGING_PATH = 'sales/general/payment_method_fee_debugging';

    /**
     * Payment method fee default label
     */
    const PAYMENT_FEE_DEFAULT_LABEL = 'Payment Processing Fee';

    /**
     * Payment method fee code
     */
    const PAYMENT_FEE_CODE = 'payment_method_fee';

    /**
     * Payment method fee Message
     */
    const PAYMENT_FEE_MESSAGE_FIELD = '/payment_method_fee_message';

     /**
      * Data constructor.
      */
    public function __construct(
        Context $context,
        private readonly PriceCurrency $priceCurrency,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    /**
     * Retrieve Store Config
     * @param string $method
     * @param null $cardType
     * @return mixed|null
     */
    public function getConfig($method = '', $cardType = null)
    {
        if ($method) {
            if ($method == self::AUTHNETCIM_CODE && !empty($cardType)) {
                $cardType = strtolower($cardType);
                return $this->scopeConfig->getValue(
                    self::CONFIG_PATH_MODULE_PAYMENT . $method . self::PAYMENT_FEE_FIELD . '_' . $cardType,
                    ScopeInterface::SCOPE_STORE
                );
            }

            return $this->scopeConfig->getValue(
                self::CONFIG_PATH_MODULE_PAYMENT . $method . self::PAYMENT_FEE_FIELD,
                ScopeInterface::SCOPE_STORE
            );
        }
        return null;
    }

    /**
     * @return mixed|string
     */
    public function getConfigLabel()
    {
        $paymentFeeLabel = $this->scopeConfig->getValue(
            self::PAYMENT_FEE_LABEL_PATH,
            ScopeInterface::SCOPE_STORE
        );

        if (isset($paymentFeeLabel) && !empty($paymentFeeLabel)) {
            return $paymentFeeLabel;
        }

        return self::PAYMENT_FEE_DEFAULT_LABEL;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return float|int
     */
    public function getPaymentMethodFee(Quote $quote, Total $total)
    {
        $fee = 0;
        try {
            $quotePayment = $quote->getPayment();
            if ($quotePayment) {
                $method  = $quotePayment->getMethod();
                if (isset($method) && !empty($method) && $method == 'authnetcim') {
                    $cardType = $quotePayment->getCcType();
                    $configFee = $this->getConfig($method, $cardType);
                    $this->logPaymentFeeMessage('=====Percentage of Selected Payment Method=====');
                    $this->logPaymentFeeMessage($method.' : '.$configFee);
                    //Do not change this condition from isset() && !is_null() && !empty()
                    if (!empty($configFee)) {
                        $this->logPaymentFeeMessage('=====All Total Amounts for Quote Id: '.$quote->getId().'=====');
                        $this->logPaymentFeeMessage($total->getAllTotalAmounts());

                        $grandTotal = array_sum($total->getAllTotalAmounts());
                        $this->logPaymentFeeMessage('Grand Total without Fee : '.$grandTotal);
                        $fee = $grandTotal * ($configFee / 100);
                        $this->logPaymentFeeMessage('Calculated Fee Amount without Round: '.$fee);
                        $fee = $this->priceCurrency->round($fee);
                        $this->logPaymentFeeMessage('Rounded Fee Amount : '.$fee);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $fee;
    }

    /**
     * Check configuration if Payment Method Fee Debugging is enabled or not
     * @return bool
     */
    public function isPaymentFeeDebugging(): bool
    {
        $paymentFeeDebugging = $this->scopeConfig->getValue(
            self::PAYMENT_FEE_DEBUGGING_PATH,
            ScopeInterface::SCOPE_STORE
        );

        return (bool) $paymentFeeDebugging;
    }

    /**
     * @param $message
     */
    public function logPaymentFeeMessage($message)
    {
        $debuggingEnabled = $this->isPaymentFeeDebugging();
        if ($debuggingEnabled) {
            if (is_array($message)) {
                $this->logger->info(json_encode($message));
            } else {
                $this->logger->info($message);
            }
        }
    }

    /**
     * Fix for WENDOVER-454
     * Function to get payment method fee message from config.
     * @param string $method
     * @return mixed|null
     */
    public function getConfigMessage($method)
    {
        if ($method) {
            $message = $this->scopeConfig->getValue(
                self::CONFIG_PATH_MODULE_PAYMENT . $method . self::PAYMENT_FEE_MESSAGE_FIELD,
                ScopeInterface::SCOPE_STORE
            );

            return $message;
        }

        return null;
    }
}
