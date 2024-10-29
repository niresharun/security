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

namespace Perficient\Order\CustomerData;

use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Order\Helper\Data as PerficientOrderHelper;
use Magento\Checkout\Model\SessionFactory;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;

class CustomSection implements SectionSourceInterface
{
    /**
     * @var CurrencyFactory
     */
    private $currencyCode;

    /**
     * CustomSection constructor.
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeConfig
     * @param CurrencyFactory $currencyFactory
     * @param SessionFactory $checkoutSessionObj
     */
    public function __construct(
        private readonly ProductRepository $productRepository,
        protected StoreManagerInterface $storeConfig,
        CurrencyFactory $currencyFactory,
        private readonly PerficientOrderHelper $perficientOrderHelper,
        private readonly SessionFactory $checkoutSessionObj,
        private readonly ProductimizeHelper $productimizeHelper
    ) {
        $this->currencyCode = $currencyFactory->create();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSectionData()
    {
        $isDiscSurchargeLoggerEnabled = false;
        $isDiscSurchargeLoggerEnabled = $this->productimizeHelper->isDiscSurchargeLoggerEnabled();
        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('-----in PRFT Order surcharge section-----');
        }

        $checkoutSession = $this->checkoutSessionObj->create();
        $quoteObj = $checkoutSession->getQuote();
        $items = $quoteObj->getAllItems();
        $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
        $minimumOrderAmount = $this->perficientOrderHelper->getMinimumOrderAmount();
        $product = $this->productRepository->get($surchargeProductSku);
        $surchargeProductId = $product->getId();
        $subtotal = '';
        $subtotalArray = [];
        if ($items) {
            foreach ($items as $item) {
                if ($item->getProductId() == $surchargeProductId) {
                    $subtotal = $item->getRowTotal();
                } else {
                    $subtotalArray[] = $item->getRowTotal();
                }
            }
        }
        $banner_message = '';
        $quoteObj->collectTotals()->save();

        if ($isDiscSurchargeLoggerEnabled) {
            $this->productimizeHelper->logDiscSurchargeMessage('getSectionData: subtotal1 - ' .  $subtotal);
        }

        if (!empty($subtotal)) {
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('getSectionData: array_sum(subtotalArray) - ' .  array_sum($subtotalArray));
            }
            $subtotal = $minimumOrderAmount - array_sum($subtotalArray);
            if ($isDiscSurchargeLoggerEnabled) {
                $this->productimizeHelper->logDiscSurchargeMessage('getSectionData: subtotal2 - ' .  $subtotal);
            }

            $currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();
            $currency = $this->currencyCode->load($currentCurrency);
            $parameters = ['%current_currency%', '%amount_left_to_avoid_surcharge%'];
            $parametersValues = [$currency->getCurrencySymbol(), sprintf("%.2f", $subtotal)];
            $CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
            if ($CurrentUserSurchargeStatus != false) {
                $banner_message = str_replace($parameters, $parametersValues, (string) $product->getShortDescription());
				$banner_message =  '<div data-bind="attr: {
            class: \'message-\' + message.type + \' \' + message.type + \' message\',
            \'data-ui-id\': \'message-\' + message.type
        }" class="message-notice notice message" data-ui-id="message-notice">
            <div data-bind="html: $parent.prepareMessageForHtml(message.text)">'.$banner_message.'</div>
        </div>';
            }
        }
        return [
            'customdata' => $banner_message,
        ];
    }
}
