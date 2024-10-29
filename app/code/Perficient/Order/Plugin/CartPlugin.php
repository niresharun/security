<?php

namespace Perficient\Order\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Perficient\Order\Helper\Data as PerficientOrderHelper;
use Magento\Quote\Model\Quote as QuoteOperations;
use Perficient\Order\Model\CurrentQuote;
use \Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Helper\Cart as MagentoCartHelper;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;

class CartPlugin
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
        private readonly MagentoCartHelper $cartHelper,
        private readonly ProductimizeHelper $productimizeHelper,
        protected RequestInterface $request
    ) {
    }

    /**
     * @param $subject
     * @param $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterAddProduct($subject, $result)
    {
        $isDiscSurchargeLoggerEnabled = false;
        $isDiscSurchargeLoggerEnabled = $this->productimizeHelper->isDiscSurchargeLoggerEnabled();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order afterAddProduct-----');
        }

        $CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
        if ($CurrentUserSurchargeStatus == false) {
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order afterAddProduct returning-----');
            }
            return $result;
        }

        $flatSurchargeAmount = $this->perficientOrderHelper->getFlatSurchargeAmount();
        $minimumOrderAmount = $this->perficientOrderHelper->getMinimumOrderAmount();
        $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: flatSurchargeAmount - ' . $flatSurchargeAmount);
            $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: minimumOrderAmount - ' . $minimumOrderAmount);
            $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: surchargeProductSku - ' . $surchargeProductSku);
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
                        $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: surcharge product removed');
                    }
                }
            }
            $quoteObj->collectTotals()->save();
        }
        //$quoteObj->collectTotals();
        $currentSubtotal = $quoteObj->getSubtotal();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: currentSubtotal1 - ' .  $currentSubtotal);
        }

        if ($currentSubtotal < $minimumOrderAmount) {
            $priceForSurchargeProduct = $flatSurchargeAmount;
            $flatDiscountUpTo = $minimumOrderAmount - $flatSurchargeAmount;
            if ($currentSubtotal > $flatDiscountUpTo) {
                $priceForSurchargeProduct = $minimumOrderAmount - $currentSubtotal;
                if ($isDiscSurchargeLoggerEnabled) {
                    $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: priceForSurchargeProduct1 - ' .  $priceForSurchargeProduct);
                }
            }
            //$surchargeProductId = $product->getId();
            $quote = $this->quoteRepository->get($quoteObj->getId());
            $quick_ship = $this->request->getParam('quick_ship_product');
            if(!$quick_ship){
                $quote->addProduct($product, $this->perficientOrderHelper->makeAddRequest($product, $surchargeProductSku, 1));
            }
            $this->quoteRepository->save($quote);
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: surcharge product added');
            }

            $quoteObj->collectTotals()->save();
            $updatedTotal = $quote->getBaseSubtotal();
            //$differenceAmount = $updatedTotal - $priceForSurchargeProduct;
            //$updatedSurChargePrice = $minimumOrderAmount - $differenceAmount;
            if ($updatedTotal > 0) {
            $items = $quote->getAllItems();
                if ($items) {
                    foreach ($items as $item) {
                        if($priceForSurchargeProduct > $flatSurchargeAmount ){
                            $priceForSurchargeProduct = $flatSurchargeAmount;
                            if ($isDiscSurchargeLoggerEnabled) {
                                $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: priceForSurchargeProduct2 - ' .  $priceForSurchargeProduct);
                            }
                            $quoteObj->collectTotals()->save();
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
                }
            }
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('AfterAddProduct: currentSubtotal2 - ' .  $currentSubtotal);
            }
            $quoteObj->collectTotals()->save();
        }
    }

	 /**
     * @param $subject
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeExecute($subject)
    {
        /*$CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
        if ($CurrentUserSurchargeStatus == true && $this->cartHelper->getItemsCount() > 0) {
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
                //Logic for Reorder scenario
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
                                $currentSubtotal = $currentSubtotal + $priceForSurchargeProduct;
                                $currentQuoteObj->getQuote()->setSubtotal($currentSubtotal);
                                $currentQuoteObj->getQuote()->collectTotals()->save();
                            }
                        }
                    }
                }
                $currentQuoteObj->getQuote()->collectTotals()->save();
            }
        } else {
            $logger->info('-----in PRFT Order Cart Controller Load return-----');
        }*/
    }
}
