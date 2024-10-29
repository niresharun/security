<?php

declare(strict_types=1);

namespace Wendover\FreightEstimator\Plugin\Quote\Address\RateResult;

use Magento\Checkout\Model\Cart;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Eav\Model\Config;
use Perficient\BlueshipShipping\Model\Carrier as Blueship;
use Wendover\FreightEstimator\Helper\Data;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Model\QuoteFactory;

class Method
{
    const CANADA_CENTRAL_REGION = 'freight_estimator/canada_freight/canada_central_region';

    const CANADA_EASTERN_REGION = 'freight_estimator/canada_freight/canada_eastern_region';

    const CANADA_WESTERN_REGION = 'freight_estimator/canada_freight/canada_western_region';

    const FLATE_RATE_PRICE = 'carriers/flatrate/price';

    protected $canadaConfigData = [
        self::CANADA_CENTRAL_REGION,
        self::CANADA_EASTERN_REGION,
        self::CANADA_WESTERN_REGION
    ];
    private $freeFreight;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $eavConfig
     * @param Cart $cart
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected Config $eavConfig,
        protected Cart $cart,
        protected LoggerInterface $logger,
        protected Data $helperData,
        protected CustomerSession $customerSession,
        protected QuoteFactory $quoteFactory
    ) {
    }



    /**
     * beforeSetPrice method
     */
    public function beforeSetPrice($subject, $result)
    {
        $rate = $subject->getData();
        $price = (isset($rate['cost']) && $rate['cost'] != null? $rate['cost']: $result);
        $originalPrice = $price;
        $this->helperData->logFrieghtMessage('Shipping cost from API -->'.$price);
        $getCustomerId = $this->customerSession->getCustomerId();
        $this->freeFreight = $this->helperData->getCustomerFreeFreight($getCustomerId);
        $this->helperData->logFrieghtMessage('Customer applicable for free freight -->'.$this->freeFreight);
        $countryCode = $this->cart->getQuote()->getShippingAddress()->getCountryId();
        $regionCode = $this->cart->getQuote()->getShippingAddress()->getRegionCode();
        $customerType = $this->customerSession->getCustomerData()->getCustomAttribute('company_branch');
        $totals = $this->cart->getQuote()->getTotals();
        $subtotal = $totals['subtotal']['value'];
        $flateRatePrice = $this->helperData->getAdminConfigValue(self::FLATE_RATE_PRICE);
        $customerTypeValue = '';
        if (!empty($customerType)) {
            $customerTypeValue = $customerType->getValue();
        }

        $company_branch = 'commercial';
        if (!empty($customerTypeValue) && $customerTypeValue === '101') {
            $company_branch = 'residential';
        }
        $LTL_OrderThreshold = $this->helperData->getAdminConfigValue('freight_estimator/us_freight/LTL_'.$company_branch.'_order_threshold');
        $LTL_OrderuptoThreshold = $this->helperData->getAdminConfigValue('freight_estimator/us_freight/LTL_'.$company_branch.'_upcharge_factor_upToThreshold');
        $LTL_OrderoverThreshold = $this->helperData->getAdminConfigValue('freight_estimator/us_freight/LTL_'.$company_branch.'_upcharge_factor_OverThreshold');

        $parcel_costThreshold = $this->helperData->getAdminConfigValue('freight_estimator/us_freight/parcel_'.$company_branch.'_costThreshold');
        $parcel_OrderUptoThreshold = $this->helperData->getAdminConfigValue('freight_estimator/us_freight/parcel_'.$company_branch.'_upcharge_factor_upToThreshold');


        if (in_array($countryCode, $this->helperData->freightManualCountry())) {
            if ($countryCode === 'CA') {
                $this->helperData->logFrieghtMessage('-->Perform Canadian shipping calcilation');
                try {
                    if ($company_branch === "commercial") {
                        //Flat rate
                        $this->helperData->logFrieghtMessage('-------commercial-------');
                        return $flateRatePrice;
                    }
                    $shippingValue = [];
                    $configRegion = '';
                    foreach ($this->canadaConfigData as $key => $value) {
                        $configValue = $this->helperData->getAdminConfigValue($value);
                        $shippingValue[$value] = $configValue;
                        if (!empty($configValue)) {
                            $configRegion = $this->checkRegion($configValue, $regionCode, $value);
                            if ($configRegion)
                                break;
                        }
                    }
                    if ($configRegion === '') {
                        //Non config canada region
                        $this->helperData->logFrieghtMessage('-------commercial-------');
                        return $flateRatePrice;
                    }
                    $freeFrightLimit = 'canada_' . $configRegion . '_regional_free_freight_limit';
                    $freeFrightLimitConfig = $this->helperData->getAdminConfigValue('freight_estimator/canada_freight/' . $freeFrightLimit);

                    if ((int)$subtotal >= (int)$freeFrightLimitConfig) {
                        if (!empty($this->freeFreight)) {
                            //Condition b) : free shipping
                            return 0;
                        } else {
                            $this->helperData->logFrieghtMessage('-------flate Rate applies-------' . $flateRatePrice);
                            //Condition d) : Flat rate
                            return $flateRatePrice;
                        }
                    } else {
                        $freeFrightUpChargeConfig = $this->helperData->getAdminConfigValue('freight_estimator/canada_freight/canada_freight_upcharge_order_threshold');
                        $price = $this->helperData->getAdminConfigValue('freight_estimator/canada_freight/canada_' . $configRegion . '_flat_rate_upToThreshold');
                        if ((int)$subtotal >= (int)$freeFrightUpChargeConfig) {
                            $price = $this->helperData->getAdminConfigValue('freight_estimator/canada_freight/canada_' . $configRegion . '_flat_rate_overThreshold');
                        }
                    }
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            } else {
                if ($rate['carrier'] == 'blueship') {
                    $this->actualShippingPrice($price);
                    $this->helperData->logFrieghtMessage('Shipping cost from Bluegrace =' . $price);
                    if ((int)$subtotal <= $LTL_OrderThreshold) {
                        if ($LTL_OrderuptoThreshold > 0) {
                            $price = ($price / $LTL_OrderuptoThreshold);
                            $this->helperData->logFrieghtMessage('Conversion Factor =' . $LTL_OrderuptoThreshold);
                        }
                    } else {
                        if ($LTL_OrderoverThreshold > 0) {
                            $price = ($price / $LTL_OrderoverThreshold);
                            $this->helperData->logFrieghtMessage('Conversion Factor =' . $LTL_OrderoverThreshold);
                        }
                    }
                } elseif ($rate['carrier'] === 'fedex') {
                    $this->actualShippingPrice($price);
                    $this->helperData->logFrieghtMessage('Shipping cost from FedEx =' . $price);
                    if ($price <= $parcel_costThreshold) {
                        if ($parcel_OrderUptoThreshold > 0) {
                            $price = ($price / $parcel_OrderUptoThreshold);
                            $this->helperData->logFrieghtMessage('Conversion Factor =' . $parcel_OrderUptoThreshold);
                        }
                    } else {
                        if ($LTL_OrderoverThreshold > 0) {
                            $price = ($price / $LTL_OrderoverThreshold);
                            $this->helperData->logFrieghtMessage('Conversion Factor =' . $LTL_OrderoverThreshold);
                        }
                    }
                } elseif ($rate['carrier'] === 'flatrate') {
                    $this->helperData->logFrieghtMessage('FlatRate');
                    $price = $flateRatePrice;
                }
            }
        }

        $this->helperData->logFrieghtMessage("Markup price --->".$price);

        return $price;
    }

    public function checkRegion($configRegion, $shippingRegion, $value)
    {
        $configRegionCode = [];
        if (str_contains($configRegion, ',')) {
            $configRegionCode = preg_split("/\s*,\s*/", $configRegion, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $configRegionCode = [$configRegion];
        }

        if (in_array($shippingRegion, $configRegionCode)) {
            $t = explode('/', $value);
            return $this->getRegionType($t[2]);
        }

        return '';
    }

    public function getRegionType($type)
    {
        if (!empty($type)) {
            $r = explode('_', $type);
            if ($r[0] === 'canada' && $r[2] === 'region') {
                return $r[1];
            }
            if (($r[0] === 'us' || $r[0] != 'canada') && $r[2] === 'region') {
                return $r[1];
            }
        }

        return '';
    }

    public function actualShippingPrice($originalPrice)
    {
        $quoteId = $this->cart->getQuote()->getId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $quote->setShippingAmountWithoutDiscount($originalPrice);
        $quote->save();
    }
}
