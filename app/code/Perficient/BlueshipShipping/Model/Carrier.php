<?php
/**
 * Blueship Shipping Model
 *
 * @keywords: Blueship Shipping
 */
declare(strict_types=1);

namespace Perficient\BlueshipShipping\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Model\Session;
use Wendover\Catalog\Setup\Patch\Data\CreateCategoriesAttributeSets;
use Wendover\FreightEstimator\Helper\Data as FreightHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Checkout\Model\Cart;

class Carrier extends AbstractCarrier implements CarrierInterface
{
    const CODE = 'blueship';
    /**
     * PALLET_COUNT will always be 1
     * @var int
     */
    const PALLET_COUNT = 1;
    /**
     * PALLET_LENGTH Length of the handling unit for this line item
     * @var int
     */
    const PALLET_LENGTH = 40;
    /**
     * HANDLING_QTY is usually the number of pallets or skids
     * $var int
     */
    const HANDLING_QTY = 1;
    /**
     * HANDLING_UNIT is unit of product that will be moved together
     */
    const HANDLING_UNIT = 1;
    /**
     * ADDITION_BY to calculate pallet width and pallet height default 6 inches
     * $var int
     */
    const ADDITION_BY = 6;
    /**
     * MIN_PALLET_WIDTH should be 48 if after calculate less than 48
     * $var int
     */
    const MIN_PALLET_WIDTH = 48;
    /**
     * MAX_PALLET_WIDTH should be 96 if after calculate greater than 96
     */
    const MAX_PALLET_WIDTH = 96;
    /**
     * WEIGHT_UOM of measure for the products in this line item lbs
     * $var string
     */
    const WEIGHT_UOM = 'lbs';
    /**
     * DIM_UNIT Unit of dimensions inch
     * $var string
     */
    const DIM_UNIT = 'IN';
    /**
     * BLUESHIP_CLASS Freight class of the item
     * $var int
     */
    const BLUESHIP_CLASS = 300;
    const DESCRIPTION = 'Artwork';
    /**
     * $var string
     */
    const ADDRESS_LOCATION_BUSINESS = 'Business';
    /**
     * $var string
     */
    const ACCESSORIALS_LIFTGATE_TEXT = 'LiftGate';
    /**
     * $var string
     */
    const ACCESSORIALS_APPOINTMENT_TEXT = 'Appointment';
    /**
     * $var string
     */
    const ACCESSORIALS_NOTIFY_TEXT = 'NotifyConsignee';
    /**
     * $var string
     */
    const DELIVERY_APPOINMENT_REQUIRED_VALUE = 'Yes';
    /**
     * $var string
     */
    const DELIVERY_APPOINMENT_REQUIRED_FIELD = "delivery_appointment";
    /**
     * $var string
     */
    const LOADING_DOCK_AVAILABLE_FIELD = "loading_dock_available";
    /**
     * $var string
     */
    const LOADING_DOCK_VALUE = 'No';
    /**
     * @var string
     */
    protected $_code = self::CODE;
    /**
     * @var
     */
    private $productRequestList;
    /**
     * Rate result data
     *
     * @var Result|null
     */
    private $result = null;
    private array $shippingResponseArr = [];
    /**
     * Array of quotes
     *
     * @var array
     */
    private static $quotesCache = [];
    /**
     * @var array
     */
    private $palletDimensions;
    /**
     * $var string
     */
    const LOADING_DOCK_ATTRIBUTE = 'loading_dock_available';

    private $mirrorAttributeSetId = null;

    /**
     * Carrier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $debugger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param CountryFactory $countryFactory
     * @param StoreManagerInterface $storeManager
     * @param RegionFactory $regionFactory
     * @param Curl $curlClient
     * @param SerializerInterface $serializer
     * @param Session $customerSession
     * @param CartRepositoryInterface $cartRepository
     * @param Config $config
     * @param ErrorFactory $rateErrorFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        private readonly LoggerInterface $debugger,
        protected ResultFactory $rateResultFactory,
        protected MethodFactory $rateMethodFactory,
        private readonly DataObjectFactory $dataObjectFactory,
        private readonly CountryFactory $countryFactory,
        private readonly CheckoutSession $checkoutSession,
        private readonly StoreManagerInterface $storeManager,
        private readonly RegionFactory $regionFactory,
        private readonly Curl $curlClient,
        private readonly SerializerInterface $serializer,
        private readonly Session $customerSession,
        protected FreightHelper $freightHelper,
        CartRepositoryInterface $cartRepository,
        private readonly Config $config,
        protected ErrorFactory $rateErrorFactory,
        protected Cart                 $cart,
        array $data = []
    ) {
        $this->cartRepository = $cartRepository;
        $this->itemWeight = 0;
        $this->liftGate = false;
        parent::__construct($scopeConfig, $rateErrorFactory, $debugger, $data);
    }

    /**
     * Generates list of allowed carrier`s shipping methods
     * Displays on cart price rules page
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|DataObject|null|Result
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function collectRates(RateRequest $request)
    {
        /**
         * Make sure that Shipping method is enabled
         */
        if (!$this->isActive()) {
            return false;
        }

        $this->setRequest($request);
        $this->getQuotes();
        return $this->getResult();
    }

    /**
     * set api service data into object
     *
     * @param RateRequest $request
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setRequest(RateRequest $request)
    {
        $this->request = $request;
        $rawRequest = $this->dataObjectFactory->create();

        $serviceMode = ($this->getConfigData('service_mode') == 'live') ? true : false;
        $apiUrl =  $this->getConfigData('api_url');
        $apiClientId = $this->getConfigData('client_id');
        $apiSecretKey = $this->getConfigData('secret_key');
        $apiMode = $this->getConfigData('mode');
        $businessUnit = $this->getConfigData('business_unit');

        $rawRequest->setServiceModeLive($serviceMode);
        $rawRequest->setApiUrl($apiUrl);
        $rawRequest->setUserId($apiClientId);
        $rawRequest->setPassword($apiSecretKey);
        $rawRequest->setMode($apiMode);
        $rawRequest->setBusinessUnit($businessUnit);

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStoreId()
            );
        }
        $rawRequest->setOrigCountry($this->countryFactory->create()->load($origCountry)->getData('iso2_code'));

        if ($request->getOrigPostcode()) {
            $rawRequest->setOrigPostal($request->getOrigPostcode());
        } else {
            $rawRequest->setOrigPostal(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }

        if ($request->getOrigRegionCode()) {
            $origRegionCode = $request->getOrigRegionCode();
        } else {
            $origRegionCode = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $request->getStoreId()
            );
        }
        if (is_numeric($origRegionCode)) {
            $origRegionCode = $this->regionFactory->create()->load($origRegionCode)->getCode();
        }
        $rawRequest->setOrigRegionCode($origRegionCode);

        if ($request->getOrigCity()) {
            $rawRequest->setOrigCity($request->getOrigCity());
        } else {
            $rawRequest->setOrigCity(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );
        }

        if ($request->getDestCity()) {
            $rawRequest->setDestCity($request->getDestCity());
            $this->checkoutSession->setDestCity($request->getDestCity());
        } else {
            $rawRequest->setDestCity($this->checkoutSession->getDestCity());
        }
        if ($request->getDestRegionCode()) {
            $rawRequest->setDestRegionCode($request->getDestRegionCode());
        }
        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $rawRequest->setDestCountry($this->countryFactory->create()->load($destCountry)->getData('iso2_code'));

        if ($request->getDestPostcode()) {
            $rawRequest->setDestPostcode($request->getDestPostcode());
        }

        $rawRequest->setCurrencyCode($this->storeManager->getStore()->getCurrentCurrency()->getCode());
        $this->rawRequest = $rawRequest;
    }

    /**
     * @return null|Result
     */
    public function getResult()
    {
        if (!$this->result) {
            $this->result = $this->rateResultFactory->create();
        }
        return $this->result;
    }

    /**
     * @return null|Result
     */
    private function getQuotes()
    {
        if ($this->preValidation()) {
            $this->productRequestList[] = $this->prepareProductDetailsList();

            // Make request for each facility id
            if (!empty($this->productRequestList)) {
                $response = $this->doRatesRequest();
                if (!$response) {
                    $rateResult = $this->getResult();
                    $rate = $this->freightHelper->flatRateShippingMethod();
                    $rateResult->append($rate);
                    //return $rate;
                } else {
                    $result = $this->validateResponse($response);
                    if ($result->hasErrors()) {
                        return;
                    }
                    $this->prepareRateResponse($result);
                }
            }
        }

        if (!empty($this->shippingResponseArr)) {
            $this->result = $this->rateResultFactory->create();

            $rate = $this->rateMethodFactory->create();
            /* Set carrier's method data */
            $rate->setCarrier($this->getCarrierCode());
            $rate->setCarrierTitle('');

            /** Displayed as shipping method under Carrier */
            $rate->setMethod($this->getCarrierCode());

            if ($this->shippingResponseArr['shipping_rate'] > 0) {
                $carrierTitleArr = array_unique($this->shippingResponseArr['carrier_title']);
                $rate->setMethodTitle($carrierTitleArr[0]);
                $shippingAmount = $this->shippingResponseArr['shipping_rate'];

                // Apply fixed admin configured handling charges
                $handlingCharges = $this->getHandlingCharges();
                $shippingAmount += $handlingCharges;

                // save handling charges to quote
                //$this->updateQuoteItems($handlingCharges); //@todo: can be used for markup
            } else {
                $rate->setMethodTitle($this->shippingResponseArr['carrier_title']);
                $shippingAmount = $this->shippingResponseArr['shipping_rate'];

                // save handling charges to quote
                // $this->updateQuoteItems(); //@todo: can be used for markup
            }
            $thresholdAmount = $this->request->getThresholdAmount();
            if (isset($thresholdAmount) && !empty($thresholdAmount) && $thresholdAmount > 0) {
                $price = $thresholdAmount;
            } else {
                $price = $shippingAmount;
            }
            $rate->setPrice($price);
            $rate->setCost($price);
            $this->result->append($rate);
        }

        return $this->result;
    }

    /**
     * Returns cache key for some request to carrier quotes service
     *
     * @return string
     */
    private function getQuotesCacheKey(string|array $requestParams)
    {
        if (is_array($requestParams)) {
            $requestParams = implode(
                ',',
                array_merge([$this->getCarrierCode()], array_keys($requestParams), $requestParams)
            );
        }
        return crc32($requestParams);
    }

    /**
     * Checks whether some request to rates have already been done, so we have cache for it
     * Used to reduce number of same requests done to carrier service during one session
     *
     * Returns cached response or null
     *
     * @param string|array $requestParams
     * @return null|string
     */
    private function getCachedQuotes(string|array $requestParams)
    {
        $cache_ttl = $this->freightHelper->getAdminConfigValue(FreightHelper::XML_PATH_CACHE_TTL);
        $key = $this->getQuotesCacheKey($requestParams);
        self::$quotesCache = $this->customerSession->getQuotesCache();
        if (isset(self::$quotesCache[$key])) {
            $cache = self::$quotesCache[$key];
            if ($cache['timestamp'] < strtotime('-' . $cache_ttl . ' minutes')) {
                unset(self::$quotesCache[$key]);
            }
            return self::$quotesCache[$key] ?? null;
        }

        return null;
    }

    /**
     * Sets received carrier quotes to cache
     *
     * @param string $response
     * @return $this
     */
    private function setCachedQuotes(string|array $requestParams, $response)
    {
        if ($response != null) {
            $key = $this->getQuotesCacheKey($requestParams);
            self::$quotesCache[$key] = $response;
            $this->customerSession->setQuotesCache(self::$quotesCache);
        }
        return $this;
    }

    /**
     * @return bool
     */
    private function preValidation()
    {
        $result = true;
        if ($this->rawRequest->getDestPostcode() == '') {
            $result = false;
        }
        if (!$result) {
            $this->setShippingCallForQuoteData();
        }

        return $result;
    }


    /**
     * @return array
     */
    private function getAllItems()
    {
        $request = $this->request;
        return $request->getAllItems();
//        $items = [];
//        if ($request->getAllItems()) {
//            foreach ($request->getAllItems() as $item) {
//                /* @var $item \Magento\Quote\Model\Quote\Item */
//                if ($item->getProduct()->isVirtual()
//                    || $item->getParentItem()
//                    || $item->getProduct()->getFreeShipping()
//                ) {
//                    // Don't process children here - we will process (or already have processed) them below
//                    continue;
//                }
//                if ($item->getHasChildren()) {
//                    foreach ($item->getChildren() as $child) {
//                        if (!$child->getFreeShipping() && !$child->getProduct()->isVirtual()
//                            && !$child->getProduct()->getFreeShipping()
//                        ) {
//                            $items[] = $child;
//                        }
//                    }
//                } else {
//                    $items[] = $item;
//                }
//            }
//        }
//        return $items;
    }

    /**
     * @return array
     */
    private function prepareProductDetailsList()
    {
        $specification = null;
        $rateResult = $this->getResult();
        $rawRequest = $this->rawRequest;
        $this->palletDimensions = $this->getPalletDimensions();
        $palletWidth = $this->palletDimensions['width'];
        $palletHeight = $this->palletDimensions['height'];
        $this->liftGateAvailable();
        $this->freightHelper->logFrieghtMessage(" === liftGate ====".$this->liftGate);
        $palletWeight = ($this->liftGate) ? $this->itemWeight : $this->palletDimensions['weight'];

        try {
            if (($palletWidth > 0) && ($palletHeight > 0) && ($palletWeight > 0)) {
                $specification = [
                    "description" => self::DESCRIPTION,
                    "class" => self::BLUESHIP_CLASS,
                    "weight" => $palletWeight,
                    "weightUnits" => self::WEIGHT_UOM,
                    "handlingQty" => self::HANDLING_QTY,
                    "handlingUnits" => self::HANDLING_UNIT,
                    "dimensions" => [
                        'length' => $this->palletDimensions['length'],
                        'width' => ceil($palletWidth),
                        'height' => ceil($palletHeight),
                        'dimUnits' => self::DIM_UNIT
                    ]
                ];
            } else {
                $rate = $this->freightHelper->flatRateShippingMethod();
                $rateResult->append($rate);
                //return $rate;
            }
        } catch (\Exception $e) {
            $this->debugger->log($e->getMessage());
            $rate = $this->freightHelper->flatRateShippingMethod();
            $rateResult->append($rate);
            //return $rate;
        }

        return $specification;
    }

    /**
     * Calculate Pallet Dimensions as per flow
     *
     * @return array
     */
    private function getPalletDimensions()
    {
        $rateResult = $this->getResult();
        $this->palletDimensions = [];
        $totalItemWeight = 0;
        $productWeight = 0;
        $longSide = $shortSide = [];
        $items = $this->getAllItems();
        foreach ($items as $item) {
            $this->freightHelper->logFrieghtMessage('*************** Bluegrace Box calculation **********');
            $productId = $item->getProductId();
            $buyRequest = $this->serializer->unserialize($item->getOptionByCode('info_buyRequest')->getValue(), true);
            if (!array_key_exists('pz_cart_properties', $buyRequest)) {
                continue;
            }
            if (empty($buyRequest['pz_cart_properties'])) {
                continue;
            }
            $productData = $item->getProduct();
            $getAttributeSetId = $productData->getAttributeSetId();
            $pz_cart_properties = $this->serializer->unserialize($buyRequest['pz_cart_properties'], true);
            $width = $pz_cart_properties['Item Width'] ?? 0;
            $height = $pz_cart_properties['Item Height'] ?? 0;
            $glassWidth = $pz_cart_properties['Glass Width'] ?? 0;
            $glassHeight = $pz_cart_properties['Glass Height'] ?? 0;

            if ($width == 0 || $height == 0) {
                $this->freightHelper->logFrieghtMessage('----Product Width and Height are not available-----');
                $rate = $this->freightHelper->flatRateShippingMethod();
                $rateResult->append($rate);
            }

            $longSide[] = max($width, $height);
            $shortSide[] = min($width, $height);

            $itemData = [
                'productId' => $productId,
                'itemWidth' => $width,
                'itemHeight' => $height,
                'glassWidth' => $glassWidth,
                'glassHeight' => $glassHeight
            ];
            if ($this->mirrorAttributeSetId === null) {
                $this->mirrorAttributeSetId = $this->freightHelper
                    ->getAttributeSetIdByName(CreateCategoriesAttributeSets::MIRROR_ATTRIBUTESET_NAME);
            }
            if ((int)$getAttributeSetId === $this->mirrorAttributeSetId) {
                if ($item['parent_item_id']) {
                    continue;
                }
                $itemWeight = (float)$productData->getWeight();
            } else {
                $itemWeight = $this->freightHelper->calculateItemWeight($itemData);
            }

            $partialWeight = $itemWeight;
            $finalWeight = $itemWeight * $item->getQty();
            $totalItemWeight += $finalWeight;
            $this->freightHelper->logFrieghtMessage("qty - " .$item->getQty());
            $this->freightHelper->logFrieghtMessage("---Item Data--->".json_encode($itemData, 1));
            $this->freightHelper->logFrieghtMessage("Individula Item Weight - " .$partialWeight);
            $this->freightHelper->logFrieghtMessage("Item Weight - " .$finalWeight);
        }
        $this->freightHelper->logFrieghtMessage("Total Item Weight -*******> " .$totalItemWeight);

        //Calculate Pallet Dimensions
        try {
            $sumPalletWidth = max($shortSide) + self::ADDITION_BY;
            if ($sumPalletWidth < self::MIN_PALLET_WIDTH) {
                $palletWidth = self::MIN_PALLET_WIDTH;
            } else if ($sumPalletWidth > self::MAX_PALLET_WIDTH) {
                $palletWidth = self::MAX_PALLET_WIDTH;
            } else {
                $palletWidth = $sumPalletWidth;
            }
            $palletHeight = max($longSide) + self::ADDITION_BY + self::ADDITION_BY;
            $palletWeight = $this->freightHelper->getPalletWeight($totalItemWeight , $palletWidth);
            $this->itemWeight = $totalItemWeight;
            $this->palletDimensions = [
                'length' => self::PALLET_LENGTH,
                'width' => $palletWidth,
                'height' => $palletHeight,
                'dimUnits' => self::DIM_UNIT,
                'weight' => $palletWeight
            ];
            $this->freightHelper->logFrieghtMessage("Pallet Dimension - " .json_encode($this->palletDimensions, 1));
        } catch (\Exception $e) {
            $this->debugger->debug($e->getMessage());
            $rate = $this->freightHelper->flatRateShippingMethod();
            $rateResult->append($rate);
        }

        return $this->palletDimensions;
    }

    /**
     * @return array
     */
    private function getReferenceDetails()
    {
        $referenceData = [];
        $referenceData['name'] = $this->getConfigData('ref_name');
        $referenceData['value'] = $this->getConfigData('ref_val');
        return $referenceData;
    }

    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @return array
     */
    private function formRateRequest()
    {
        $rawRequest = $this->rawRequest;
        $ratesRequest = [
            'mode' => $rawRequest->getMode(),
            'origin' => [
                'city' => $rawRequest->getOrigCity(),
                'stateProvince' => $rawRequest->getOrigRegionCode(),
                'country' => $rawRequest->getOrigCountry(),
                'postalCode' => $rawRequest->getOrigPostal()
            ],
            "destination" => $this->getDestination(),
            "items" => $this->productRequestList,
            "references" => [],
            'businessUnit' => $rawRequest->getBusinessUnit(),
            'accountNumber' => $this->getConfigData('account_number')
        ];
        $ratesRequest = $this->serializer->serialize($ratesRequest);
        $this->debugger->info("Request", ['Request', $this->serializer->unserialize($ratesRequest)]);
        $this->freightHelper->logFrieghtMessage("====Blueship Request====");
        $this->freightHelper->logFrieghtMessage($ratesRequest);
        return $ratesRequest;
    }

    /**
     * Makes remote request to the carrier and returns a response
     *
     * @return mixed
     */
    private function doRatesRequest()
    {
        $ratesRequest = $this->formRateRequest();
        $response = $this->getCachedQuotes($ratesRequest);
        $debugData = ['request' => $ratesRequest];
        if ($response === null) {
            try {
                $rawRequest = $this->rawRequest;

                $this->curlClient->addHeader("Content-Type", "application/json");
                $this->curlClient->addHeader("Accept", "application/json");
                $this->curlClient->setCredentials($rawRequest->getUserId(), $rawRequest->getPassword());
                $this->curlClient->post($rawRequest->getApiUrl(), $ratesRequest);

                $responseBody = $this->curlClient->getBody();
                $response = $this->parseJsonResponse($responseBody);

                $debugData['result'] = $response;
                $this->setCachedQuotes($ratesRequest, $response);
            } catch (\Exception $e) {
                $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                $response = '';
            }
        } else {
            $debugData['result'] = $response;
        }
        $this->_debug($debugData);
        $this->debugger->info("Response", ['Response', $response]);
        $this->freightHelper->logFrieghtMessage("====Blueship Response====".json_encode($response, 1));
        return $response;
    }

    /**
     * @return array
     */
    private function getDestination()
    {
        $rateResult = $this->getResult();
        $rawRequest = $this->rawRequest;
        $this->palletDimensions = $this->getPalletDimensions();
        $quote = $this->cartRepository->getActiveForCustomer($this->customerSession->getCustomerId());
        $shippingAddress = $quote->getShippingAddress();
        $address = $shippingAddress->getData();
        $accessorials = [];

        try {
            // Get Delivery appointment required for the selected address
            $accessorials[] = $this->validateDeliveryOptions($address[self::DELIVERY_APPOINMENT_REQUIRED_FIELD],
                self::DELIVERY_APPOINMENT_REQUIRED_FIELD);

            // Get Loading Dock for the selected address
            $loadingDockValue = $this->validateDeliveryOptions($address[self::LOADING_DOCK_AVAILABLE_FIELD],
                self::LOADING_DOCK_AVAILABLE_FIELD);
            $this->freightHelper->logFrieghtMessage(" === loadingDockValue====". $loadingDockValue);
            if ($loadingDockValue === FreightHelper::LIFT_GATE_LOADING_LOCK) {
                $accessorials[] = $loadingDockValue;
                // Get Lift gate the selected address
                if ($this->liftGate) {
                    $accessorials[] = self::ACCESSORIALS_LIFTGATE_TEXT;
                }
            }
        } catch (\Exception $e) {
            $this->freightHelper->logFrieghtMessage($e->getMessage());
            $rate = $this->freightHelper->flatRateShippingMethod();
            $rateResult->append($rate);
            //return $rate;
        }

        return [
            "city" => $rawRequest->getDestCity(),
            "stateProvince" => $rawRequest->getDestRegionCode(),
            "country" => $rawRequest->getDestCountry(),
            "postalCode" => $rawRequest->getDestPostcode(),
            "accessorials" => $accessorials
        ];
    }

    /**
     * method to return the call for quote data
     */
    private function setShippingCallForQuoteData()
    {
        $this->shippingResponseArr['shipping_rate'] = 0;
        $this->shippingResponseArr['carrier_title'] = $this->getConfigData('specificerrmsg');
    }

    /**
     * Method to validate data of api response
     *
     * @param $response
     * @return mixed
     */
    private function validateResponse($response)
    {
        $this->result = $this->rateResultFactory->create();
        $result = $this->dataObjectFactory->create();
        $error = false;

        if (!$response) {
            $error = true;
        } else {
            $configAllowedCarriers = $this->getConfigData('allowed_carriers');

            if ($configAllowedCarriers != '') {
                $allowedCarriers = explode(',', (string) $configAllowedCarriers);
                $carrierArr = [];
                foreach ($response as $carrier) {
                    if (is_array($carrier)) {
                        if (in_array($carrier['scac'], $allowedCarriers)
                            && strtolower((string) $carrier['serviceLevel']) == 'standard') {
                            $carrierArr['carrierName'][] = $carrier['carrierName'];
                            $carrierArr['total'][] = $carrier['normalizedTotal'];
                        }
                    }
                }
                if (!empty($carrierArr)) {
                    $carrierData = [];
                    $carrierKey = array_keys($carrierArr['total'], min($carrierArr['total']));
                    $carrierData['carrierName'] = $carrierArr['carrierName'][$carrierKey[0]];
                    $carrierData['total'] = min($carrierArr['total']);
                    $result->setCarrierData($carrierData);
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
        }
        if ($error) {
            $this->setShippingCallForQuoteData();
            $result->setErrors($this->getConfigData('specificerrmsg'));
            $this->hasError = true;
        }
        return $result;
    }

    private function prepareRateResponse($result)
    {
        if ($result) {
            $shippingRate = $this->shippingResponseArr['shipping_rate'] ?? 0;
            $carrierData = $result->getCarrierData();
            //$carrierTitle = $carrierData['carrierName'];
            $carrierTitle = $this->getConfigData('carrier_name');

            $this->shippingResponseArr['shipping_rate'] = $shippingRate + $carrierData['total'];
            $this->shippingResponseArr['carrier_title'][] = $carrierTitle;
        }
    }

    /**
     * @param $responseBody
     * @return mixed/null
     */
    private function parseJsonResponse($responseBody)
    {
        $response = $this->serializer->unserialize($responseBody);
        if (isset($response['quotes']) && !empty($response['quotes'])) {
            $response['quotes']['timestamp'] = time();
            return $response['quotes'];
        }
        return null;
    }

    /**
     * return admin configurable fixed shipping handling charges
     */
    private function getHandlingCharges(): false|string
    {
        return $this->getConfigData('handling_charges');
    }

    /**
     * @param $value
     * @param $key
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomDeliverySpecification($value, $key) {
        if ($value && str_contains((string) $value, $key)) {
            $value = trim(str_replace($key, "", (string) $value));
        }

        return $this->freightHelper->getAttributeOptionValue($value, $key);
    }

    /**
     * Gwt value for Delivery Options
     *
     * @param $deliveryAppointmentValue
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateDeliveryOptions($addressSpecificOption, $key) {
        $deliveryCheck = '';
        if ($addressSpecificOption) {
            $addressSpecificFeature = $this->getCustomDeliverySpecification($addressSpecificOption, $key);
            $this->freightHelper->logFrieghtMessage(" === Blueship Carrier value ====".$addressSpecificFeature);
            switch($key) {
                case "delivery_appointment":
                    if ($addressSpecificFeature == self::DELIVERY_APPOINMENT_REQUIRED_VALUE) {
                        $deliveryCheck = self::ACCESSORIALS_APPOINTMENT_TEXT;
                    } else {
                        $deliveryCheck = self::ACCESSORIALS_NOTIFY_TEXT;
                    }
                    break;
                case "loading_dock_available":
                    if ($addressSpecificFeature == self::LOADING_DOCK_VALUE) {
                        $deliveryCheck = FreightHelper::LIFT_GATE_LOADING_LOCK;
                    }
                    break;
            }
        }

        return $deliveryCheck;
    }

    /**
     * Get lift gate available
     *
     * @return bool
     */
    private function liftGateAvailable()  {
        $address = $this->cart->getQuote()->getShippingAddress()->getData();
        $loadingDockValue = $address['loading_dock_available'];
        if ($loadingDockValue && str_contains((string) $loadingDockValue, 'loading_dock_available')) {
            $loadingDockValue = trim(str_replace('loading_dock_available', "", (string) $loadingDockValue));
        }
        $loadingDock = $this->freightHelper->getAttributeOptionValue(
            $loadingDockValue,
            self::LOADING_DOCK_ATTRIBUTE
        );
        $this->freightHelper->logFrieghtMessage("=== loadingDock value====".$loadingDock);
        try {
            $blueshipPallet = $this->freightHelper->getAdminConfigValue('freight_estimator/us_freight/Weight_limit_for_left_gate_eligiblity');
            $this->freightHelper->logFrieghtMessage(" === itemWeight ====".$this->itemWeight);
            $this->freightHelper->logFrieghtMessage(" === blueshipPallet ====".$blueshipPallet);
            if ($this->itemWeight >= $blueshipPallet && $loadingDock == 'No' ) {
                $this->liftGate = true;
                $this->freightHelper->logFrieghtMessage(" === liftGateAvailable liftGate====". $this->liftGate);
                return true;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }
}
