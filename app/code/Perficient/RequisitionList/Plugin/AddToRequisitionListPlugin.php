<?php

namespace Perficient\RequisitionList\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Model\Quote as QuoteOperations;
use Magento\Quote\Model\QuoteRepository;
use Perficient\Order\Helper\Data as PerficientOrderHelper;
use Perficient\Order\Model\CurrentQuote;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;

class AddToRequisitionListPlugin
{
    /**
     * AddToRequisitionListPlugin constructor.
     * @param Session $checkoutSession
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param CurrentQuote $currentQuote
     * @param QuoteRepository $quoteRepository
     * @param RedirectInterface $redirect
     * @param Redirect $resultRedirectFactory
     */
    public function __construct(
        protected Session $checkoutSession,
        protected ProductRepository $productRepository,
        protected FormKey $formKey,
        protected QuoteOperations $quoteOperations,
        private readonly PerficientOrderHelper $perficientOrderHelper,
        private readonly CurrentQuote $currentQuote,
        private readonly QuoteRepository $quoteRepository,
        private readonly RedirectInterface $redirect,
        private readonly Redirect $resultRedirectFactory
    ) {
    }

    /**
     * @param $subject
     * @param $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterExecute($subject, $result)
    {
        $CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
        if ($CurrentUserSurchargeStatus == false) {
            return true;
        }
        $flatSurchargeAmount = $this->perficientOrderHelper->getFlatSurchargeAmount();
        $minimumOrderAmount = $this->perficientOrderHelper->getMinimumOrderAmount();
        $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
        $quoteOperations = $this->quoteOperations;
        $currentQuoteObj = $this->currentQuote;
        $items = $currentQuoteObj->getQuote()->getAllItems();
        $product = $this->productRepository->get($surchargeProductSku);
        $surchargeProductId = $product->getId();
        if ($items) {
            foreach ($items as $item) {
                if ($item->getProductId() == $surchargeProductId) {
                    $currentQuoteObj->getQuote()->removeItem($item->getItemId())->save();
                    $currentQuoteObj->getQuote()->collectTotals()->save();
                }
            }
        }
        $currentQuoteObj->getQuote()->collectTotals();
        $currentSubtotal = $currentQuoteObj->getQuote()->getSubtotal();
        $priceForSurchargeProduct = $flatSurchargeAmount;
        if ($currentSubtotal < $minimumOrderAmount) {
            $flatDiscountUpTo = $minimumOrderAmount - $flatSurchargeAmount;
            if ($currentSubtotal > $flatDiscountUpTo) {
                $priceForSurchargeProduct = $minimumOrderAmount - $currentSubtotal;
            }
            $surchargeProductId = $product->getId();
            $quote = $this->quoteRepository->get($currentQuoteObj->getQuote()->getId());
            $quote->addProduct($product, $this->perficientOrderHelper->makeAddRequest($product, $surchargeProductSku, 1));
            $this->quoteRepository->save($quote);
            $currentQuoteObj->getQuote()->collectTotals()->save();
            $updatedTotal = $currentQuoteObj->getQuote()->getBaseSubtotal();
            $differenceAmount = $updatedTotal - $priceForSurchargeProduct;
            $updatedSurChargePrice = $minimumOrderAmount - $differenceAmount;
            if ($updatedTotal > 0) {
                $items = $currentQuoteObj->getQuote()->getAllItems();
                if ($items) {
                    foreach ($items as $item) {
                        if ($updatedSurChargePrice > $flatSurchargeAmount) {
                            $updatedSurChargePrice = $flatSurchargeAmount;
                        }
                        if ($item->getProductId() == $surchargeProductId) {
                            $item->setPrice($priceForSurchargeProduct);
                            $item->setCustomPrice($priceForSurchargeProduct);
                            $item->setOriginalCustomPrice($priceForSurchargeProduct);
                            $item->setRowTotal($priceForSurchargeProduct);
                            $item->getProduct()->setIsSuperMode(true);
                        }
                    }
                }
            }
            $currentQuoteObj->getQuote()->collectTotals()->save();
        }
        $redirectUrl = $this->redirect->getRefererUrl();
        $resultRedirect = $this->resultRedirectFactory->setUrl($redirectUrl);
        return $resultRedirect;
    }
}