<?php

namespace Perficient\Order\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\Data\Form\FormKey;
use Perficient\Order\Helper\Data as PerficientOrderHelper;
use Magento\Quote\Model\Quote as QuoteOperations;
use Perficient\Order\Model\CurrentQuote;
use \Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\Cart as CustomerCart;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;

class CartAddToCollectionPlugin
{
    /**
     * CartPlugin constructor.
     * @param SessionFactory $checkoutSessionObj
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        protected SessionFactory $checkoutSessionObj,
        protected ProductRepository $productRepository,
        protected FormKey $formKey,
        protected QuoteOperations $quoteOperations,
        private readonly PerficientOrderHelper $perficientOrderHelper,
        private readonly CurrentQuote $currentQuote,
        private readonly QuoteRepository $quoteRepository,
        private readonly CustomerCart $cart,
        private readonly ProductimizeHelper $productimizeHelper
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
        $isDiscSurchargeLoggerEnabled = false;
        $isDiscSurchargeLoggerEnabled = $this->productimizeHelper->isDiscSurchargeLoggerEnabled();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order afterAddCollection-----');
        }

        $CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
        if ($CurrentUserSurchargeStatus == false) {
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage(
                    '-----in PRFT Order afterAddCollection Returning-----'
                );
            }
            return $result;
        }

        $flatSurchargeAmount = $this->perficientOrderHelper->getFlatSurchargeAmount();
        $minimumOrderAmount = $this->perficientOrderHelper->getMinimumOrderAmount();
        $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();

        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage(
                'AfterExecute: flatSurchargeAmount - ' . $flatSurchargeAmount);
            $this->productimizeHelper->logDiscSurchargeMessage(
                'AfterExecute: minimumOrderAmount - ' . $minimumOrderAmount);
            $this->productimizeHelper->logDiscSurchargeMessage(
                'AfterExecute: surchargeProductSku - ' . $surchargeProductSku);
        }

        $checkoutSession = $this->checkoutSessionObj->create();
        $quoteObj = $checkoutSession->getQuote();
        $items = $quoteObj->getAllItems();
        $product = $this->productRepository->get($surchargeProductSku);
        $surchargeProductId = $product->getId();
        if ($items) {
            foreach ($items as $item) {
                if ($item->getProductId() == $surchargeProductId) {
                    $quoteObj->removeItem($item->getItemId())->save();
                    if ($isDiscSurchargeLoggerEnabled) {
                        $this->productimizeHelper->logDiscSurchargeMessage(
                            'AfterExecute: surcharge product removed'
                        );
                    }
                }
            }
            $quoteObj->collectTotals()->save();
        }

        $quote = $this->quoteRepository->get($quoteObj->getId());
        $currentSubtotal = $quote->getSubtotal();
        $priceForSurchargeProduct = $flatSurchargeAmount;
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage(
                'AfterExecute: currentSubtotal1 - ' .  $currentSubtotal
            );
        }

        if ($currentSubtotal < $minimumOrderAmount) {
            $flatDiscountUpTo = $minimumOrderAmount - $flatSurchargeAmount;
            if ($currentSubtotal > $flatDiscountUpTo) {
                $priceForSurchargeProduct = $minimumOrderAmount - $currentSubtotal;
                $quoteObj->collectTotals()->save();
                if ($isDiscSurchargeLoggerEnabled) {
                    $this->productimizeHelper->logDiscSurchargeMessage(
                        'AfterExecute: priceForSurchargeProduct1 - ' .  $priceForSurchargeProduct
                    );
                }
            }
            $surchargeProductId = $product->getId();
            //$quote = $this->quoteRepository->get($currentQuoteObj->getQuote()->getId());
            $quote->addProduct($product, $this->perficientOrderHelper->makeAddRequest($product, $surchargeProductSku, 1));
            $this->quoteRepository->save($quote);
            $quoteObj->collectTotals()->save();
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('AfterExecute: surcharge product added');
            }
            $updatedTotal = $quoteObj->getBaseSubtotal();
            $differenceAmount = $updatedTotal - $priceForSurchargeProduct;
            $updatedSurChargePrice = $minimumOrderAmount - $differenceAmount;
            if ($updatedTotal > 0) {
                $items = $quote->getAllItems();
                if ($items) {
                    foreach ($items as $item) {
                        if($updatedSurChargePrice > $flatSurchargeAmount ){
                            $updatedSurChargePrice = $flatSurchargeAmount;
                            if ($isDiscSurchargeLoggerEnabled) {
                                $this->productimizeHelper->logDiscSurchargeMessage(
                                    'AfterExecute: priceForSurchargeProduct2 - ' .  $updatedSurChargePrice);
                            }
                        }
                        if ($item->getProductId() == $surchargeProductId) {
                            $item->setPrice($priceForSurchargeProduct);
                            $item->setCustomPrice($priceForSurchargeProduct);
                            $item->setOriginalCustomPrice($priceForSurchargeProduct);
                            $item->setRowTotal($priceForSurchargeProduct);
                            $item->getProduct()->setIsSuperMode(true);
                            $this->quoteRepository->save($quote);
                            $currentSubtotal = $currentSubtotal + $priceForSurchargeProduct;
                            $quoteObj->setSubtotal($currentSubtotal);
                            $quoteObj->collectTotals()->save();
                        }
                    }
                    $quoteObj->collectTotals()->save();
                }
            }
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage(
                    'AfterExecute: currentSubtotal2 - ' .  $currentSubtotal
                );
            }
            $quoteObj->collectTotals()->save();
        }
    }
}
