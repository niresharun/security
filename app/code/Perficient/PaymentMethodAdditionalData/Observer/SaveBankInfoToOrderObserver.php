<?php
/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */

namespace Perficient\PaymentMethodAdditionalData\Observer;

use Magento\Framework\App\State;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Webapi\Controller\Rest\InputParamsResolver;
use Psr\Log\LoggerInterface;

class SaveBankInfoToOrderObserver implements ObserverInterface
{
    /**
     * @var InputParamsResolver
     */
    protected $inputParamsResolver;
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var State
     */
    protected $state;

    /**
     * SaveBankInfoToOrderObserver constructor.
     */
    public function __construct(
        InputParamsResolver $inputParamsResolver,
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        State $state
    ) {
        $this->inputParamsResolver = $inputParamsResolver;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->state = $state;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function execute(EventObserver $observer)
    {
        if ($this->state->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML &&
            $this->state->getAreaCode() != \Magento\Framework\App\Area::AREA_GLOBAL) {
            $inputParams = $this->inputParamsResolver->resolve();
            foreach ($inputParams as $inputParam) {
                if ($inputParam instanceof \Magento\Quote\Model\Quote\Payment) {
                    $paymentData = $inputParam->getData('additional_data');
                    $paymentOrder = $observer->getEvent()->getPayment();
                    $order = $paymentOrder->getOrder();
                    $quote = $this->quoteRepository->get($order->getQuoteId());
                    $paymentQuote = $quote->getPayment();
                    if (isset($paymentData['bankowner'])) {
                        $paymentQuote->setData('customer_po_number', $paymentData['bankowner']);
                        $paymentOrder->setData('customer_po_number', $paymentData['bankowner']);
                    }
                }
            }
        }
    }
}
