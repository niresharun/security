<?php
/**
 * Observer used to save the lead-time notification message against the quote item.
 *
 * @package: Perficient/LeadTime
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_LeadTime LeadTime
 */
declare(strict_types=1);

namespace Perficient\LeadTime\Observer;

use Magento\Checkout\Helper\Cart;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\LeadTime\Helper\Data;
use Perficient\QuickShip\Helper\Data as QuickShipHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Header;

/**
 * Class AddLeadTime
 * @package Perficient\LeadTime\Observer
 */
class AddLeadTime implements ObserverInterface
{
    /**
     * Constant for standard lead time.
     */
    final const STANDARD_LEAD_TIME = 'standard';

    /**
     * AddLeadTime constructor.
     * @param Json $serializer
     * @param Cart $cartHelper
     * @param QuoteRepository $quoteRepository
     * @param RequestInterface $request
     * @param Header $header
     */
    public function __construct(
        private readonly Data             $leadTimeHelper,
        private readonly Json             $serializer,
        private readonly QuickShipHelper  $quickshipHelper,
        private readonly Cart             $cartHelper,
        private readonly CheckoutSession  $checkoutSession,
        private readonly QuoteRepository  $quoteRepository,
        private readonly RequestInterface $request,
        private readonly Header           $header
    )
    {
    }

    /**
     * Applies events to product collection
     *
     * @return $this
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $this->checkoutSession->getQuote();
        $product = $observer->getEvent()->getProduct();

        if ($product->getSku() != $this->quickshipHelper->getSurchargeSKU()) {
            $httpReferer = $this->header->getHttpReferer();

            $query_str = parse_url((string)$httpReferer, PHP_URL_QUERY);
            if ($query_str) {
                parse_str($query_str, $query_params);
            }

            $isQuickShip = $this->request->getParam('quick_ship_product');
            $fromQuickShip = 0;
            if(isset($isQuickShip) && $isQuickShip) {
                $fromQuickShip = 1;
            }

            $isQuickShipProduct = $product->getIsQuickShip();

            if ($fromQuickShip && $isQuickShipProduct) {
                $leadTimeMessage = strip_tags((string)$this->leadTimeHelper->getQuickShipLeadTimeMessage());
            } else {
                $leadTimeMessage = strip_tags((string)$this->leadTimeHelper->getStandardLeadTimeMessage());
            }

            if (!empty($leadTimeMessage)) {
                $quote->setLeadTime($leadTimeMessage);
                $this->quoteRepository->save($quote);
            }
        }

        return $this;
    }
}

