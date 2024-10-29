<?php

namespace DCKAP\Productimize\Plugin;

use Magento\Quote\Model\Quote as QuoteOperations;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\SessionFactory;
use DCKAP\Productimize\Helper\Data;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;
use Magento\Framework\App\RequestInterface;
use Perficient\Productimize\Model\ProductConfiguredPrice;

class CartPlugin
{
    /**
     * CartPlugin constructor.
     * @param QuoteOperations $quoteOperations
     * @param CurrentQuote $currentQuote
     */
    public function __construct(
        protected readonly QuoteOperations $quoteOperations,
        protected readonly QuoteRepository $quoteRepository,
        protected readonly RequestInterface $request,
        protected readonly Data $productimizeHelperData,
        protected readonly ProductConfiguredPrice $perficientPriceCalc,
        protected readonly SessionFactory $session,
        private readonly ProductimizeHelper $productimizeHelper
    )
    {

    }

    /**
     * @param $subject
     * @param $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterAddProduct($subject, $result)
    {
        if ($this->productimizeHelper->isDiscSurchargeLoggerEnabled()) {
            $this->productimizeHelper->logDiscSurchargeMessage('-----in DCKAP afterAddProduct (only save totals)-----');
        }
        $checkoutSession = $this->session->create();
        /*$items = $checkoutSession->getQuote()->getAllItems();
        if ($items) {
            foreach ($items as $item) {
                $itemData = $item->getBuyRequest()->getData();
                if (isset($itemData['pz_cart_properties']) && (isset($itemData['edit_id']) && $itemData['edit_id'] == 1)) {
                    $changedPriceParams = $this->productimizeHelperData->getPriceParam($itemData['pz_cart_properties'], $item->getProduct()->getId(), "");
                    $checkoutPrice = $this->perficientPriceCalc->getCheckoutPrice($item->getProduct()->getId(), $changedPriceParams);
                    if ($checkoutPrice) {
                        $customisedPrice = $checkoutPrice;
                        $item->setPrice($customisedPrice);
                        $item->setCustomPrice($customisedPrice);
                        $item->setOriginalCustomPrice($customisedPrice);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                }
            }
        }*/
        $checkoutSession->getQuote()->collectTotals()->save();
        return $result;
    }
}