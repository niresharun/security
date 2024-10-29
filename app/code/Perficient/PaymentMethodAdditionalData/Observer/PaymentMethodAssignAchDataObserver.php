<?php
/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */

namespace Perficient\PaymentMethodAdditionalData\Observer;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;

/**
 * PaymentMethodAssignAchDataObserver Class
 */
class PaymentMethodAssignAchDataObserver implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * PaymentMethodAssignAchDataObserver constructor.
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        private readonly CheckoutSession $checkoutSession
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $data = $observer->getData('data');
        if ($data->hasData('additional_data')) {
            $receivedParams = $data->getData('additional_data');
            if (isset($receivedParams['bankowner']) && !empty(['bankowner'])) {
                $quote = $this->quoteRepository->get($this->checkoutSession->getQuote()->getId());
                $paymentQuote = $quote->getPayment();
                $paymentQuote->setData('customer_po_number', $receivedParams['bankowner']);
            }
        }
    }
}
