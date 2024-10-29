<?php
/**
 * Additional Payment Method Fee Config Provider
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

namespace Perficient\Payment\Model\Config;

use Perficient\Payment\Helper\Data;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Psr\Log\LoggerInterface;

/**
 * Class ConfigProvider
 *
 * @package Perficient\Payment\Model\Config
 */
class PaymentMethodFeeConfigProvider implements ConfigProviderInterface
{

    /**
     * PaymentMethodFeeConfigProvider constructor.
     *
     * @param Data $configHelper
     * @param PaymentConfig $paymentConfig
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        private readonly Data $configHelper,
        private readonly PaymentConfig $paymentConfig,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Fix for WENDOVER-454
     * To show custom message below payment methods during checkout.
     * @return array
     */
    public function getConfig()
    {
        $methodsFeeMessage = [];
        try {
            $paymentMethods = $this->getPaymentMethods();
            if (isset($paymentMethods) && !empty($paymentMethods)) {
                foreach ($paymentMethods as $paymentCode => $paymentModel) {
                    $configPaymentMessage = $this->configHelper->getConfigMessage($paymentCode);
                    $paymentMethodFeeMessage = (isset($configPaymentMessage) && !empty($configPaymentMessage))
                        ? $configPaymentMessage : null;
                    $methodsFeeMessage[$paymentCode] = $paymentMethodFeeMessage;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return [
            'payment_fee_message' => $methodsFeeMessage,
        ];
    }

    /**
     * @return mixed
     */
    public function getPaymentMethods()
    {
        return $this->paymentConfig->getActiveMethods();
    }
}
