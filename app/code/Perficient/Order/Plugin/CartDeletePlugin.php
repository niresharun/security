<?php
/**
 * Added to handle product surcharge if minimum order amount not met by customer
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */
declare(strict_types=1);

namespace Perficient\Order\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\Data\Form\FormKey;
use Perficient\Order\Helper\Data as PerficientOrderHelper;
use Magento\Quote\Model\Quote as QuoteOperations;
use Perficient\Order\Model\CurrentQuote;
use \Magento\Quote\Model\QuoteRepository;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;

class CartDeletePlugin
{
    /**
     * CartDeletePlugin constructor.
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
        private readonly ProductimizeHelper $productimizeHelper
    ) {
    }

    public function afterRemoveItem($subject, $result)
    {
        $isDiscSurchargeLoggerEnabled = false;
        $isDiscSurchargeLoggerEnabled = $this->productimizeHelper->isDiscSurchargeLoggerEnabled();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order afterRemoveItem-----');
        }

        $CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
        if ($CurrentUserSurchargeStatus == false) {
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order afterRemoveItem returning-----');
            }
            return $result;
        }
        $flatSurchargeAmount = $this->perficientOrderHelper->getFlatSurchargeAmount();
        $minimumOrderAmount = $this->perficientOrderHelper->getMinimumOrderAmount();
        $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
        $checkoutSession = $this->checkoutSessionObj->create();
        $quoteObj = $checkoutSession->getQuote();
        $product = $this->productRepository->get($surchargeProductSku);
        $surchargeProductId = $product->getId();
        $cartItems = $quoteObj->getAllItems();
        foreach ($cartItems as $item) {
            if ($item->getProductId() == $surchargeProductId) {
                $quoteObj->removeItem($item->getItemId())->save();
                if ($isDiscSurchargeLoggerEnabled) {
                    $this->productimizeHelper->logDiscSurchargeMessage('afterRemoveItem: surcharge product removed' . $item->getItemId());
                }
            }
        }
        $quoteObj->collectTotals()->save();
        $cartItems = $quoteObj->getAllItems();
        if (!$cartItems) {
            return $result;
        }
        $currentSubtotal = $quoteObj->getSubtotal();

        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('afterRemoveItem: currentSubtotal1 =' . $currentSubtotal);
        }

        if ($currentSubtotal < $minimumOrderAmount) {
            $priceForSurchargeProduct = $flatSurchargeAmount;
            $flatDiscountUpTo = $minimumOrderAmount - $flatSurchargeAmount;
            if ($currentSubtotal > $flatDiscountUpTo) {
                $priceForSurchargeProduct = $minimumOrderAmount - $currentSubtotal;
                if ($isDiscSurchargeLoggerEnabled) {
                    $this->productimizeHelper->logDiscSurchargeMessage('afterRemoveItem: priceForSurchargeProduct1 =' . $priceForSurchargeProduct);
                }
            }
            $surchargeProductId = $product->getId();
            $quote = $this->quoteRepository->get($quoteObj->getId());
            $quote->addProduct($product, $this->perficientOrderHelper->makeAddRequest($product, $surchargeProductSku, 1));
            $this->quoteRepository->save($quote);
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('afterRemoveItem: surcharge product added');
            }

            $quoteObj->collectTotals()->save();
            $items = $quoteObj->getAllItems();
            if ($items) {
                foreach ($items as $item) {
                    if($priceForSurchargeProduct > $flatSurchargeAmount ){
                        $priceForSurchargeProduct = $flatSurchargeAmount;
                        if ($isDiscSurchargeLoggerEnabled) {
                            $this->productimizeHelper->logDiscSurchargeMessage('afterRemoveItem: priceForSurchargeProduct2 = ' . $priceForSurchargeProduct);
                        }
                        $quoteObj->collectTotals()->save();
                    }
                    if ($item->getProductId() == $surchargeProductId) {
                        $item->setPrice($priceForSurchargeProduct);
                        $item->setCustomPrice($priceForSurchargeProduct);
                        $item->setOriginalCustomPrice($priceForSurchargeProduct);
                        $item->setRowTotal($priceForSurchargeProduct);
                        $item->getProduct()->setIsSuperMode(true);
                        $currentSubtotal = $currentSubtotal + $priceForSurchargeProduct;
                        $quoteObj->setSubtotal($currentSubtotal);
                        $quoteObj->collectTotals()->save();
                    }
                }
            }
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('afterRemoveItem: currentSubtotal2 = ' . $currentSubtotal);
            }
        }
        return $result;
    }
}
