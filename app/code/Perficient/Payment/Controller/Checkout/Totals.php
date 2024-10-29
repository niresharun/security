<?php
/**
 * Collect totals on change payment method during checkout
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

namespace Perficient\Payment\Controller\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Json\Helper\Data;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;
use Perficient\Payment\Helper\Data as PaymentFeeHelper;
use Magento\Payment\Model\Config;

class Totals implements ActionInterface
{

    public function __construct(
        private readonly Session $checkoutSession,
        private readonly Data $helper,
        private readonly JsonFactory $resultJson,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly LoggerInterface $logger,
        private readonly PaymentFeeHelper $paymentFeeHelper,
        private readonly Config $paymentConfig,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Trigger to re-calculate the collect Totals
     *
     * @return Json
     */
    public function execute()
    {
        $response = [
            'error' => false,
            'message' => ''
        ];

        try {
            $content = $this->request->getContent();

            //Trigger to re-calculate totals
            if (isset($content) && !empty($content)) {
                $quote = $this->checkoutSession->getQuote();
                $payment = $this->helper->jsonDecode($content);

                //Debugging Log Start
                $this->paymentFeeHelper->logPaymentFeeMessage('-------------Payment_method_fee_log-------------');
                $this->paymentFeeHelper->logPaymentFeeMessage(
                    '=====Selected Payment Method Data for Quote Id: '.$quote->getId().'====='
                );
                $this->paymentFeeHelper->logPaymentFeeMessage($content);
                //Debugging Log END

                if ($quote && is_array($payment) &&
                    isset($payment['payment']) && !empty($payment['payment'])) {
                    $quotePayment = $quote->getPayment();
                    if (isset($quotePayment) && !empty($quotePayment)) {
                        $paymentMethodData = explode('::', trim((string)$payment['payment']));
                        if (is_array($paymentMethodData) && !empty($paymentMethodData)) {
                            $paymentMethod = $paymentMethodData[0];
                            $ccTitle = isset($paymentMethodData[1]) && !empty($paymentMethodData[1])
                                ? $paymentMethodData[1] : null;
                            $quotePayment->setMethod($paymentMethod);
                            if ($paymentMethod == PaymentFeeHelper::AUTHNETCIM_CODE && !empty($ccTitle)) {
                                $availableCcTypes = $this->paymentConfig->getCcTypes();
                                if (is_array($availableCcTypes) && !empty($availableCcTypes)) {
                                    $key = array_search($ccTitle, $availableCcTypes);
                                    if (false !== $key) {
                                        $quotePayment->setCcType($key);
                                    } else {
                                        $quotePayment->setCcType(null);
                                    }
                                }
                            } else {
                                $quotePayment->setCcType(null);
                            }
                            $quote->collectTotals();
                            $this->quoteRepository->save($quote);

                            $response = [
                                'error' => false,
                                'message' => ''
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage()
            ];
            $this->logger->error($e->getMessage());
        }

        /** @var Raw $resultRaw */
        $resultJson = $this->resultJson->create();
        return $resultJson->setData($response);
    }
}
