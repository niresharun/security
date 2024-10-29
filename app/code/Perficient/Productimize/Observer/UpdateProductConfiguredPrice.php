<?php
/**
 * Observer update Product Configured Selling Price in cart/checkout.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Observer;

use Magento\Framework\Event\ObserverInterface;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;
use Perficient\Productimize\Model\ProductConfiguredPrice;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Model\Session as CustomerSession;
use DCKAP\Productimize\Helper\Data as DCKAPhelper;

/**
 * Class UpdateProductConfiguredPrice
 * @package Perficient\Productimize\Observer
 */
class UpdateProductConfiguredPrice implements ObserverInterface
{
    const EDIT_ID = 'edit_id';

    /**
     * @var ProductimizeHelper
     */
    private ProductimizeHelper $productimizeHelper;


    /**
     * @var ProductConfiguredPrice
     */
    private \Perficient\Productimize\Model\ProductConfiguredPrice $productConfiguredPrice;

    /**
     * @var Json
     */
    private \Magento\Framework\Serialize\Serializer\Json $json;


    /**
     * @var DCKAPhelper
     */
    private DCKAPhelper $productimizeHelperData;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * UpdateProductConfiguredPrice constructor.
     * @param ProductimizeHelper $productimizeHelper
     * @param ProductConfiguredPrice $productConfiguredPrice
     * @param Json $json
     * @param DCKAPhelper $productimizeHelperData
     * @param CustomerSession $customerSession
     */
    public function __construct(
        ProductimizeHelper $productimizeHelper,
        ProductConfiguredPrice $productConfiguredPrice,
        Json $json,
        DCKAPhelper $productimizeHelperData,
        CustomerSession $customerSession
    ) {
        $this->productimizeHelper = $productimizeHelper;
        $this->productConfiguredPrice = $productConfiguredPrice;
        $this->json = $json;
        $this->productimizeHelperData = $productimizeHelperData;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isDiscSurchargeLoggerEnabled = false;
        $isDiscSurchargeLoggerEnabled = $this->productimizeHelper->isDiscSurchargeLoggerEnabled();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order execute-----');
        }

        $event = $observer->getEvent();
        $item = $event->getQuoteItem();
        $artProduct = $item->getProduct();

        if ($artProduct->getId() && $this->productimizeHelper->isProductimizePricingIsEnabled()) {
            $itemData = $item->getBuyRequest()->getData();
            $discountType = $this->customerSession->getDiscountType();

            if (isset($itemData['pz_cart_properties']) && isset($itemData['edit_id']) && $itemData['edit_id'] == 1) {
                //Customized product price
                $changedPriceParams = $this->productimizeHelperData->getPriceParam($itemData['pz_cart_properties'], $artProduct->getId(), "");
                $configuredPrice = $this->productConfiguredPrice->getCheckoutPrice($artProduct->getId(), $changedPriceParams);

                if ($isDiscSurchargeLoggerEnabled) {
                    $this->productimizeHelper->logDiscSurchargeMessage('inside edit');
                    $this->productimizeHelper->logDiscSurchargeMessage('$product->getId() = '.$artProduct->getId());
                    $this->productimizeHelper->logDiscSurchargeMessage('$configuredPrice = '.$configuredPrice);
                    $this->productimizeHelper->logDiscSurchargeMessage('$discountType = '.$discountType);
                }
                if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                    $this->productConfiguredPrice->logDetailedPricingMessage("Cart Selling Price W/O Discount > PID" . $artProduct->getId() . ' - ' . $configuredPrice);
                }

                if ($discountType != ProductimizeHelper::DISCOUNT_TYPE_POST_DISCOUNTED) {
                    $configuredPrice = $this->productConfiguredPrice->applyCompanyDiscount($configuredPrice, true);
                }
            } else {
                //Default product price
                $configuredPrice = $artProduct->getPrice();

                if ($isDiscSurchargeLoggerEnabled) {
                    $this->productimizeHelper->logDiscSurchargeMessage('inside non edit');
                    $this->productimizeHelper->logDiscSurchargeMessage('$product->getId() = '.$artProduct->getId());
                    $this->productimizeHelper->logDiscSurchargeMessage('$configuredPrice = '.$configuredPrice);
                    $this->productimizeHelper->logDiscSurchargeMessage('$discountType = '.$discountType);
                }
                if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                    $this->productConfiguredPrice->logDetailedPricingMessage("Cart Selling Price W/O Discount > PID" . $artProduct->getId() . ' - ' . $configuredPrice);
                }

                $configuredPrice = $this->productConfiguredPrice->applyCompanyDiscount($configuredPrice, true);
            }
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('$configuredPrice = '.$configuredPrice);
            }
            if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                $this->productConfiguredPrice->logDetailedPricingMessage("Cart Selling Price W Discount > PID" . $artProduct->getId() . ' - ' . $configuredPrice);
            }

            $item->setOriginalCustomPrice($configuredPrice);
            $item->getProduct()->setIsSuperMode(true);
            $item->save();
        }
    }
}
