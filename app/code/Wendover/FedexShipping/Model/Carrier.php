<?php
/**
 * FedEx Shipping Model
 */
declare(strict_types=1);

namespace Wendover\FedexShipping\Model;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Measure\Length;
use Magento\Framework\Measure\Weight;
use Magento\Framework\Module\Dir;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Soap\ClientFactory;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Rate\Result;
use Wendover\FreightEstimator\Helper\Data as FreightHelper;
use Magento\Fedex\Model\Carrier as Fedex;
use Magento\Checkout\Model\Cart;
use Wendover\FreightEstimator\Model\BoxCalculationsFactory;
/**
 * Fedex shipping implementation
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Carrier extends Fedex
{

    /**
     * Version of tracking service
     * @var int
     */
    private static $trackServiceVersion = 10;

    const RESIDENTIAL_ADDRESS_TYPE = "Residential";

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Dir\Reader $configReader
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     * @param Json|null $serializer
     * @param ClientFactory|null $soapClientFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        protected FreightHelper $freightHelper,
        protected Cart $cart,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        private BoxCalculationsFactory      $boxCalculations,
        array $data = [],
        $serializer = null,
        ClientFactory $soapClientFactory = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $storeManager,
            $configReader,
            $productCollectionFactory,
            $data,
            $serializer,
            $soapClientFactory
        );
        $wsdlBasePath = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Magento_Fedex') . '/wsdl/';
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v10.wsdl';
        $this->_rateServiceWsdl = $wsdlBasePath . 'RateService_v10.wsdl';
        $this->_trackServiceWsdl = $wsdlBasePath . 'TrackService_v' . self::$trackServiceVersion . '.wsdl';
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->soapClientFactory = $soapClientFactory ?: ObjectManager::getInstance()->get(ClientFactory::class);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareRateResponse($response)
    {
        $result = $this->_rateFactory->create();
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/freightLog.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $costArr = [];
        $priceArr = [];
        $errorTitle = 'For some reason we can\'t retrieve tracking info right now.';
        $logger->info('================= FedEx API START ======================');

        try {
            if (is_object($response)) {
                $logger->info('================= HighestSeverity ======================'.$response->HighestSeverity);

                if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                    if (is_array($response->Notifications)) {
                        $notification = array_pop($response->Notifications);
                        $errorTitle = (string)$notification->Message;
                    } else {
                        $errorTitle = (string)$response->Notifications->Message;
                    }
                } elseif (isset($response->RateReplyDetails)) {
                    $logger->info('================= RateReplyDetails ======================');
                    $logger->info('================= allow methods ======================'.$this->getConfigData('allowed_methods'));
                    $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                    if (is_array($response->RateReplyDetails)) {
                        foreach ($response->RateReplyDetails as $rate) {
                            $serviceName = (string)$rate->ServiceType;
                            $logger->info('================= serviceName ======================'.$serviceName);

                            if (in_array($serviceName, $allowedMethods)) {
                                $amount = $this->_getRateAmountOriginBased($rate);
                                $costArr[$serviceName] = $amount;
                                $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                            }
                        }
                        $logger->info('================= fedex methods ======================'.json_encode($priceArr, 1));
                        asort($priceArr);
                    } else {
                        $rate = $response->RateReplyDetails;
                        $serviceName = (string)$rate->ServiceType;
                        $logger->info('================= serviceName ======================'.$serviceName);

                        if (in_array($serviceName, $allowedMethods)) {
                            $amount = $this->_getRateAmountOriginBased($rate);
                            $costArr[$serviceName] = $amount;
                            $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info('================= FedEx API  error ======================'.$e->getMessage());
            $this->_logger->critical($e);
        }

        $shippingApplied = false;
        if (empty($priceArr)) {
            $logger->info('Applicable shipping method ' .$this->getConfigData('title'));
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            // $shippingApplied = true;
        } else {
            $logger->info('FedEx Residential delivery calculation');
            foreach ($priceArr as $method => $price) {
                $method_title = $this->getCode('method', $method);
                $logger->info('method_title'.$method_title);
                $addressType = $this->checkResidentialDelivery();
                $logger->info('address type ==='.$addressType);
                if (
                    ($addressType === true && $method_title == 'Home Delivery') ||
                    ($addressType == false && ($method_title == 'Ground'))
                ) {
                    $rate = $this->_rateMethodFactory->create();
                    $rate->setCarrier($this->_code);
                    $rate->setCarrierTitle($this->getConfigData('title'));
                    $rate->setMethod($method);
                    $rate->setMethodTitle($this->getCode('method', $method));
                    $rate->setCost($costArr[$method]);
                    $rate->setPrice($price);
                    $result->append($rate);
                    $shippingApplied = true;
                    break;
                }
            }
        }

        if (!$shippingApplied) {
            $result = $this->_rateFactory->create();
            $rate = $this->freightHelper->flatRateShippingMethod();
            $result->append($rate);
        }

        return $result;
    }

    /**
     * Used to check whether address is belongs to a residential address type
     * @return void
     */
    protected function checkResidentialDelivery() {
        $address = $this->cart->getQuote()->getShippingAddress()->getData();
        $addressType = $address['location'];
        if ($addressType && str_contains((string) $addressType, 'location')) {
            $addressType = trim(str_replace('location', "", (string) $addressType));
        }
        $location = $this->freightHelper->getAttributeOptionValue(
            $addressType,
            "location"
        );

        return ($location === self::RESIDENTIAL_ADDRESS_TYPE) ? true : false;
    }

    /**
     * Forming request for rate estimation depending to the purpose
     *
     * @param string $purpose
     * @return array
     */
    protected function _formRateRequest($purpose)
    {
        $r = $this->_rawRequest;

        $ratesRequest = [
            'WebAuthenticationDetail' => [
                'UserCredential' => ['Key' => $r->getKey(), 'Password' => $r->getPassword()],
            ],
            'ClientDetail' => ['AccountNumber' => $r->getAccount(), 'MeterNumber' => $r->getMeterNumber()],
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => [
                'DropoffType' => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'Shipper' => [
                    'Address' => ['PostalCode' => $r->getOrigPostal(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'Recipient' => [
                    'Address' => [
                        'PostalCode' => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        'Residential' => (bool) $this->checkResidentialDelivery(),
                    ],
                ],
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => ['AccountNumber' => $r->getAccount(), 'CountryCode' => $r->getOrigCountry()],
                ],
                'CustomsClearanceDetail' => [
                    'CustomsValue' => ['Amount' => $r->getValue(), 'Currency' => $this->getCurrencyCode()],
                ],
                'RateRequestTypes' => 'LIST',
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
            ],
        ];

        //--------------- Custom Box Package data
        $boxDetails = $this->checkoutSession->create();
        $packages = $boxDetails->getBoxDetails();
        $packageCount = 0;
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/freightLog.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("===== FedEx Shipping Request Starts=========");
        $logger->info("===== Residential Flag :".(bool) $this->checkResidentialDelivery());
        $logger->info("===== Packages :".json_encode($packages, 1));
        foreach ($packages as $key => $package) {
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][$packageCount]['GroupPackageCount'] = 1;
            $packageWeight = (double) ceil($package['box_weight']);
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][$packageCount]['Weight']['Value']
                = $packageWeight;
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][$packageCount]['Weight']['Units']
                = $this->getConfigData('unit_of_measure');
            $length = $package['box_length'];
            $width = $package['box_width'];
            $height = $package['box_height'];
            $row = $key+1;
            $logger->info("===== Package Weight for ".($row)."----->".$packageWeight);
            $logger->info("===== Package Dimention ".($row)."----->".$length. " x ". $width. " x ".$height);

            if ($length || $width || $height) {
                $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][$packageCount]['Dimensions'] = [
                    'Length' => $length,
                    'Width' => $width,
                    'Height' => $height,
                    'Units' => 'IN',
                ];
            }

            $packageCount++;
        }
        $logger->info("===== Total Boxes:".$packageCount);

        $ratesRequest['RequestedShipment']['PackageCount'] = $packageCount;

        if ($r->getDestCity()) {
            $ratesRequest['RequestedShipment']['Recipient']['Address']['City'] = $r->getDestCity();
        }

        if ($purpose == self::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = [
                'Indicia' => (double)$r->getWeight() >= 1 ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid'),
            ];
        }

        $logger->info("===== FedEx Shipping Request Ends =========");
        return $ratesRequest;
    }
}
