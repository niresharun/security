<?php
declare(strict_types=1);

namespace Wendover\FreightEstimator\Model;

use Magento\Shipping\Model\Shipping as ParentShipping;
use Wendover\FreightEstimator\Model\FreightConditions;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Rate\CarrierResultFactory;
use Magento\Shipping\Model\Rate\PackageResultFactory;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class Shipping
 * This class is used to validated the shipping methods based on freight criteria
 */
class Shipping extends ParentShipping
{
    protected $varCarriers = FreightConditions::FLAT_RATE_CODE;

    /**
     * Shipping constructor.
     * @param RateRequestFactory|null $rateRequestFactory
     * @param PackageResultFactory|null $packageResultFactory
     * @param CarrierResultFactory|null $carrierResultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig,
        \Magento\Shipping\Model\Config                       $shippingConfig,
        \Magento\Store\Model\StoreManagerInterface           $storeManager,
        \Magento\Shipping\Model\CarrierFactory               $carrierFactory,
        \Magento\Shipping\Model\Rate\ResultFactory           $rateResultFactory,
        \Magento\Shipping\Model\Shipment\RequestFactory      $shipmentRequestFactory,
        \Magento\Directory\Model\RegionFactory               $regionFactory,
        \Magento\Framework\Math\Division                     $mathDivision,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        private CheckoutSession                              $checkoutSession,
        protected FreightConditions                          $freightConditions,
        protected LoggerInterface                            $logger,
        RateRequestFactory                                   $rateRequestFactory = null,
        ?PackageResultFactory                                $packageResultFactory = null,
        ?CarrierResultFactory                                $carrierResultFactory = null
    )
    {
        $this->freightConditions = $freightConditions;
        $this->logger = $logger;
        parent::__construct(
            $scopeConfig,
            $shippingConfig,
            $storeManager,
            $carrierFactory,
            $rateResultFactory,
            $shipmentRequestFactory,
            $regionFactory,
            $mathDivision,
            $stockRegistry,
            $rateRequestFactory,
            $packageResultFactory,
            $carrierResultFactory
        );
    }

    /**
     * Retrieve all methods for supplied shipping data
     *
     * @return $this
     * @todo make it ordered
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $storeId = $request->getStoreId();
        if (!$request->getOrig()) {
            $request->setCountryId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setRegionId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_REGION_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setCity(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            )->setPostcode(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStore()
                )
            );
        }



        try {
            $this->varCarriers = FreightConditions::FLAT_RATE_CODE;
            $satisfyFreightCriteria = $this->freightConditions->satisfyFreightCriteria($request);
            if (!empty($satisfyFreightCriteria['carrierCode'])) {
                $this->varCarriers = $satisfyFreightCriteria['carrierCode'];
            }
            if (isset($satisfyFreightCriteria['carrierRate'])) {
                $request->setThresholdAmount($satisfyFreightCriteria['carrierRate']);
            }
            if (isset($satisfyFreightCriteria['box_calculations'])) {
                $request->setBoxCalculations($satisfyFreightCriteria['box_calculations']);
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $limitCarrier = $request->getLimitCarrier();
        if (!$limitCarrier) {
            $carriers = $this->_scopeConfig->getValue(
                'carriers',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            foreach ($carriers as $carrierCode => $carrierConfig) {
                if ($carrierCode == $this->varCarriers) {
                    $this->collectCarrierRates($carrierCode, $request);
                    continue;
                }
            }

        } else {
            $currentQuote = $this->checkoutSession->getQuote();
            $current_method =  $currentQuote->getShippingAddress()->getShippingMethod();

            if (!is_array($limitCarrier)) {
                $limitCarrier = [$limitCarrier];
            }
            foreach ($limitCarrier as $carrierCode) {
                $carrierConfig = $this->_scopeConfig->getValue(
                    'carriers/' . $carrierCode,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                );
                if (!$carrierConfig) {
                    continue;
                }

                if ($carrierCode == $this->varCarriers || $current_method === "flatrate_flatrate") {
                    $carrierCode = ($current_method === "flatrate_flatrate") ? 'flatrate' : $carrierCode;
                    $this->collectCarrierRates($carrierCode, $request);
                    continue;
                }
            }
        }
        return $this;
    }
}
