<?php
declare(strict_types=1);

namespace Wendover\FreightEstimator\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Perficient\BlueshipShipping\Model\Carrier as BlueshipShippingCarrier;
use Magento\OfflineShipping\Model\Carrier\Flatrate as FlatrateCarrier;
use Magento\OfflineShipping\Model\Carrier\Freeshipping as FreeshippingCarrier;
use Magento\Framework\Serialize\Serializer\Json;
use Wendover\Catalog\Setup\Patch\Data\CreateCategoriesAttributeSets;
use Wendover\FreightEstimator\Helper\Data as FreightHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Wendover\FedexShipping\Model\Carrier as FedexCarrier;
use Magento\Catalog\Model\Product;

/**
 * Class FreightConditions
 * This class is used to validate the freight conditions
 */
class FreightConditions extends AbstractModel
{

    const FLAT_RATE_CODE = 'flatrate';

    const FREE_SHIPPING_CODE = 'freeshipping';

    const CANADA_FLAT_RATE_CODE = 'customshippingrate';

    const CANADA_CENTRAL_REGION = 'freight_estimator/canada_freight/canada_central_region';

    const CANADA_EASTERN_REGION = 'freight_estimator/canada_freight/canada_eastern_region';

    const CANADA_WESTERN_REGION = 'freight_estimator/canada_freight/canada_western_region';

    const US_REGION_1 = 'freight_estimator/us_freight/us_region_1';

    const US_REGION_2 = 'freight_estimator/us_freight/us_region_2';

    const US_REGION_3 = 'freight_estimator/us_freight/us_region_3';

    const US_REGION_4 = 'freight_estimator/us_freight/us_region_4';

    const US_REGION_5 = 'freight_estimator/us_freight/us_region_5';

    const FEDEX_METHOD_STATUS = 'carriers/fedex/active';

    protected $canadaConfigData = [
        self::CANADA_CENTRAL_REGION,
        self::CANADA_EASTERN_REGION,
        self::CANADA_WESTERN_REGION
    ];

    protected $usConfigData = [
        self::US_REGION_1,
        self::US_REGION_2,
        self::US_REGION_3,
        self::US_REGION_4,
        self::US_REGION_5
    ];

    /**
     * @var array
     */
    private $contiguousRegionCodes = ['FL', 'GA', 'AL', 'SC', 'HI', 'AK'];
    /**
     * @var array
     */
    private $thresholdUSOne = ['FL', 'GA', 'AL', 'SC'];
    /**
     * @var array
     */
    protected $freightManualCountry = [
        'US',
        'CA'
    ];
    /**
     * @var array
     */
    protected $freightManualUS = [
        'HI',
        'AK'
    ];
    /**
     * @var array
     */
    protected $resultArr = [];
    /**
     * @var int
     */
    protected $boxCount = 0;
    /**
     * @var array
     */
    protected $finalResultArr = [];
    /**
     * @var array
     */
    protected $boxOutput = [];
    /**
     * @var CAThresholdAmount
     */
    private $caThresholdAmount = 0;
    /**
     * @var boolean
     */
    private $freeFreight;

    private $exceedMaxPrice = 0;

    /**
     * @var int|null
     */
    private $mirrorAttributeSetId = null;



    /**
     * @param CustomerSession $customerSession
     * @param BlueshipShippingCarrier $blueshipShippingCarrier
     * @param FlatrateCarrier $flatrateCarrier
     * @param FreeshippingCarrier $freeshippingCarrier
     * @param Json $jsonSerialize
     * @param FreightHelper $freightHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param Config $config
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param BoxCalculationsFactory $boxCalculations
     * @param LoggerInterface $logger
     * @param CheckoutSession $checkoutSession
     * @param FedexCarrier $fedexCarrier
     * @param Product $product
     */
    public function __construct(
        protected CustomerSession           $customerSession,
        protected BlueshipShippingCarrier   $blueshipShippingCarrier,
        protected FlatrateCarrier           $flatrateCarrier,
        protected FreeshippingCarrier       $freeshippingCarrier,
        protected Json                      $jsonSerialize,
        protected FreightHelper             $freightHelper,
        private CustomerRepositoryInterface $customerRepository,
        private Config                      $config,
        private SearchCriteriaBuilder       $searchCriteriaBuilder,
        private FilterBuilder               $filterBuilder,
        private FilterGroupBuilder          $filterGroupBuilder,
        private BoxCalculationsFactory      $boxCalculations,
        private LoggerInterface             $logger,
        private CheckoutSession             $checkoutSession,
        protected FedexCarrier              $fedexCarrier,
        private readonly Product            $product

    )
    {
    }

    /**
     * Validate Freight Criteria
     * @param $request
     * @return array
     */
    public function satisfyFreightCriteria($request)
    {
        $getCustomerId = $this->customerSession->getCustomerId();
        $this->freeFreight =$this->freightHelper->getCustomerFreeFreight($getCustomerId);
        $this->freightHelper->logFrieghtMessage('freeFreight-' . $this->freeFreight);

        $this->request = $request;
        $satisfyFreightCriteria = [];
        $satisfyFreightCriteria['carrierCode'] = self::FLAT_RATE_CODE;
        $satisfyFreightCriteria['carrierRate'] = 0;

        //Country check
        if (!in_array($request->getDestCountryId(),  $this->freightHelper->freightManualCountry())) {
            //FREIGHT MANUAL
            $this->freightHelper->logFrieghtMessage('====Country Condition: Manual====');
            return $satisfyFreightCriteria;
        }

        //Threshold check
        $varThresholdResponse = [];
        $destinationCountryId = $this->request->getDestCountryId();
        if (isset($destinationCountryId) && !empty($destinationCountryId)) {

            if (in_array($destinationCountryId,  $this->freightHelper->freightManualCountry())) {
                if ($destinationCountryId == 'US') {
                    //Returns [] means proceed with ups/blueship OTHERWISE return flat/free
                    $varThresholdResponse = $this->thresholdCheckUS();

                } else if ($destinationCountryId == 'CA') {
                    //Returns [] means proceed with ups/blueship (with or without threshold value) OTHERWISE return flat/free
                    $varThresholdResponse = $this->thresholdCheckCA();
                }
                else{
                    $varThresholdResponse = $this->thresholdCheckTerritories();
                }
            }
        }
        $this->freightHelper->logFrieghtMessage('-------varThresholdResponse-------');
        $this->freightHelper->logFrieghtMessage($varThresholdResponse);

        if (isset($varThresholdResponse['carrierCode']) &&
            isset($varThresholdResponse['carrierRate'])
        ) {
            //Either FREIGHT MANUAL or FREE
            return $satisfyFreightCriteria = $varThresholdResponse;
        }

        if ($this->exceedMaxPrice)
        {
            $this->freightHelper->logFrieghtMessage('--------------- Condition- F--------------');
            $satisfyFreightCriteria['carrierRate'] = 0;
            return $satisfyFreightCriteria = $varThresholdResponse;
        }

        //BOX Logic beings
        //Add checks for box freight
        $boxCalculationModel = $this->boxCalculations->create();
        $this->boxOutput = $boxCalculationModel->callBoxCalculations();
        if (empty($this->boxOutput)) {
            //FREIGHT MANUAL
            $this->freightHelper->logFrieghtMessage('Free freight shipping - box empty');
            $this->freightHelper->logFrieghtMessage($this->boxOutput);
            return $satisfyFreightCriteria;
        }

        //Actual checks for ups ot blueship:
        $satisfyFreightCriteria['box_calculations'] = $this->boxOutput;
        $maxCountParcelBoxesPerShipment = $this->freightHelper->getAdminConfigValue('freight_estimator/parcel_box_config/max_count_parcel_boxes_per_shipment');
        if (isset($this->boxOutput['number_of_boxes'])) {
            $this->freightHelper->logFrieghtMessage('----------------Start Condition D ---------------');

            $this->freightHelper->logFrieghtMessage('number_of_boxes - '.$this->boxOutput['number_of_boxes']);
            $this->freightHelper->logFrieghtMessage('maxCountParcelBoxesPerShipment - '.$maxCountParcelBoxesPerShipment);

            if ($this->boxOutput['number_of_boxes'] > $maxCountParcelBoxesPerShipment) {
                $this->freightHelper->logFrieghtMessage('====Condition: Blueship====');
                $satisfyFreightCriteria['carrierCode'] = BlueshipShippingCarrier::CODE;
                $this->freightHelper->logFrieghtMessage('---------------- Condition D Yes ---------------'.$satisfyFreightCriteria['carrierCode']);
            } else {
                $this->freightHelper->logFrieghtMessage('====Last Condition: Fedex====');
                $satisfyFreightCriteria['carrierCode'] = self::FLAT_RATE_CODE;
                if ($this->freightHelper->getAdminConfigValue(self::FEDEX_METHOD_STATUS)) {
                    $satisfyFreightCriteria['carrierCode'] = FedexCarrier::CODE;
                }
                $this->freightHelper->logFrieghtMessage('---------------- Condition D No ---------------'.$satisfyFreightCriteria['carrierCode']);
            }

            $this->freightHelper->logFrieghtMessage('----------------End Condition D ---------------');

            //Update threshold value if it exists else continue with blank carrierRate so that ups and blueship can return value
            if ($this->caThresholdAmount > 0) {
                $satisfyFreightCriteria['carrierRate'] = $this->caThresholdAmount;
            }
        } else {
            $this->freightHelper->logFrieghtMessage('====Condition: Exceed the max size====');
            $satisfyFreightCriteria['carrierCode'] = BlueshipShippingCarrier::CODE;
            $this->freightHelper->logFrieghtMessage('====Condition: Exceed the max size===='.$satisfyFreightCriteria['carrierCode']);
        }

        return $satisfyFreightCriteria;
    }

    /**
     * Get Total Area
     * @return string
     */
    public function getTotalArea()
    {
        return isset($this->boxOutput['total_area']) ? $this->boxOutput['total_area'] : 0;
    }

    /**
     * Validate threshold value for region of US
     * @return array
     */
    public function thresholdCheckUS()
    {
        $this->freightHelper->logFrieghtMessage('-------US Shipping calculation starts-------');

        $currentQuote = $this->checkoutSession->getQuote();
        $itemsCount = $currentQuote->getItemsCount();
        $itemsCount =$currentQuote->getItemsQty();
        $items = $currentQuote->getAllItems();
        $regionCode = $this->request->getDestRegionCode();
        $subtotalWithDiscount = (float)$currentQuote->getBaseSubtotalWithDiscount();
        $this->freightHelper->logFrieghtMessage("quote Id - " . $currentQuote->getId());
        $this->freightHelper->logFrieghtMessage(
            "subtotalWithDiscount : " . $subtotalWithDiscount
        );
        $maxCountParcelBoxesPerShipment = $this->freightHelper->getAdminConfigValue('freight_estimator/parcel_box_config/max_count_parcel_boxes_per_shipment');
        $maxItemsPerParcelBox = $this->freightHelper->getAdminConfigValue('freight_estimator/parcel_box_config/max_items_per_parcel_box');

        $resultUSThreshold = [];
        $configRegion = $this->checkRegion($this->usConfigData, $regionCode);
        $this->freightHelper->logFrieghtMessage('US Region: ' . $configRegion.', US Region Code: ' . $regionCode);
        if ($configRegion === '' || $configRegion == 4) {
            $this->freightHelper->logFrieghtMessage('threshold US - Flat Rate');
            $resultUSThreshold['carrierCode'] = self::FLAT_RATE_CODE;
            $resultUSThreshold['carrierRate'] = 0;

            return $resultUSThreshold;
        }

        $freeFrightLimit = 'us_free_freight_limit_region_' . $configRegion;
        $freeFrightLimitConfig = $this->freightHelper->getAdminConfigValue('freight_estimator/us_freight/' . $freeFrightLimit);

        if ((int)$subtotalWithDiscount >= (int)$freeFrightLimitConfig) {
            if (!empty($this->freeFreight)) {
                //Condition b) : free shipping
                $this->freightHelper->logFrieghtMessage('threshold US - Free Shipping applies');
                $resultUSThreshold['carrierCode'] = self::FREE_SHIPPING_CODE;
                $resultUSThreshold['carrierRate'] = 0;

                return $resultUSThreshold;
            } else {
                //Condition d) : Flat rate
                $this->freightHelper->logFrieghtMessage('threshold US - Flat Condition applies - condition d');
                $resultUSThreshold['carrierCode'] = self::FLAT_RATE_CODE;
                $resultUSThreshold['carrierRate'] = 0;

                return $resultUSThreshold;
            }
        }
        $this->freightHelper->logFrieghtMessage('---------------Start Condition- A------------');
        $this->freightHelper->logFrieghtMessage('maxCountParcelBoxesPerShipment- '.$maxCountParcelBoxesPerShipment);
        $this->freightHelper->logFrieghtMessage('max_items_per_parcel_box- '.$maxItemsPerParcelBox);
        $this->freightHelper->logFrieghtMessage('itemsCount- '.$itemsCount);
        $this->freightHelper->logFrieghtMessage('Order Piece Count Exceed Max Pieces for Parcel Order- '.($maxCountParcelBoxesPerShipment * $maxItemsPerParcelBox));

        if($this->isMirrorProduct($items)) {
            return $resultUSThreshold;
        }

        if (($maxCountParcelBoxesPerShipment * $maxItemsPerParcelBox) < $itemsCount) {
            $this->exceedMaxPrice = 1;
            $resultUSThreshold['carrierCode'] = BlueshipShippingCarrier::CODE;
            $this->freightHelper->logFrieghtMessage('threshold US - Exceed the max box count -- LTL applies');
            return $resultUSThreshold;
        }


        return $resultUSThreshold;
    }

    /**
     * Validate threshold value for region of US
     * @return array
     */
    public function thresholdCheckTerritories()
    {

        $destinationCountryId = $this->request->getDestCountryId();
        $this->freightHelper->logFrieghtMessage('-------In thresholdCheck '.$destinationCountryId.'-------');
        $currentQuote = $this->checkoutSession->getQuote();
        $itemsCount = $currentQuote->getItemsCount();
        $items = $currentQuote->getAllItems();

        $itemsCount =$currentQuote->getItemsQty();
        $regionCode = $this->request->getDestRegionCode();
        $subtotalWithDiscount = (float)$currentQuote->getBaseSubtotalWithDiscount();
        $this->freightHelper->logFrieghtMessage("quote Id - " . $currentQuote->getId());
        $this->freightHelper->logFrieghtMessage(
            "subtotalWithDiscount : " . $subtotalWithDiscount
        );
        $maxCountParcelBoxesPerShipment = $this->freightHelper->getAdminConfigValue('freight_estimator/parcel_box_config/max_count_parcel_boxes_per_shipment');
        $maxItemsPerParcelBox = $this->freightHelper->getAdminConfigValue('freight_estimator/parcel_box_config/max_items_per_parcel_box');

        $resultUSThreshold = [];
        $configRegion = $this->checkRegion($this->usConfigData, $destinationCountryId);
        $this->freightHelper->logFrieghtMessage($destinationCountryId.' Region: ' . $configRegion.', Region Code: '. $regionCode);

        $freeFrightLimit = 'us_free_freight_limit_region_' . $configRegion;
        $freeFrightLimitConfig = $this->freightHelper->getAdminConfigValue('freight_estimator/us_freight/' . $freeFrightLimit);

        if ((int)$subtotalWithDiscount >= (int)$freeFrightLimitConfig) {
            if (!empty($this->freeFreight)) {
                //Condition b) : free shipping
                $this->freightHelper->logFrieghtMessage('threshold '.$destinationCountryId.' - Free Shipping b');
                $resultUSThreshold['carrierCode'] = self::FREE_SHIPPING_CODE;
                $resultUSThreshold['carrierRate'] = 0;

                return $resultUSThreshold;
            } else {
                //Condition d) : Flat rate
                $this->freightHelper->logFrieghtMessage('threshold '.$destinationCountryId.' - Flat Condition d');
                $resultUSThreshold['carrierCode'] = self::FLAT_RATE_CODE;
                $resultUSThreshold['carrierRate'] = 0;

                return $resultUSThreshold;
            }
        }
        $this->freightHelper->logFrieghtMessage('---------------Start Condition- A-----------------------');
        $this->freightHelper->logFrieghtMessage('maxCountParcelBoxesPerShipment- '.$maxCountParcelBoxesPerShipment);
        $this->freightHelper->logFrieghtMessage('max_items_per_parcel_box- '.$maxItemsPerParcelBox);
        $this->freightHelper->logFrieghtMessage('itemsCount- '.$itemsCount);
        $this->freightHelper->logFrieghtMessage('Order Piece Count Exceed Max Pieces for Parcel Order- '.($maxCountParcelBoxesPerShipment * $maxItemsPerParcelBox));

        if($this->isMirrorProduct($items)) {
            return $resultUSThreshold;
        }

        if (($maxCountParcelBoxesPerShipment * $maxItemsPerParcelBox) < $itemsCount) {
            $this->freightHelper->logFrieghtMessage('threshold '.$destinationCountryId.' - Exceed the max box count');
            $resultUSThreshold['carrierCode'] = BlueshipShippingCarrier::CODE;
            $this->freightHelper->logFrieghtMessage('threshold US - Exceed the max box count -- LTL applies');
            return $resultUSThreshold;
        }

        return $resultUSThreshold;
    }
    /**
     * Validate threshold value for region of CA
     * @return array
     */
    public function thresholdCheckCA()
    {
        $this->freightHelper->logFrieghtMessage('-------In thresholdCheck CA-------');
        $regionCode = $this->request->getDestRegionCode();
        $currentQuote = $this->checkoutSession->getQuote();
        $cartTotal = (float)$currentQuote->getBaseSubtotalWithDiscount();
        $customerType = $this->customerSession->getCustomerData()->getCustomAttribute('company_branch');

        $customerTypeValue = '';
        $resultCAThreshold = [];
        if (!empty($customerType)) {
            $customerTypeValue = $customerType->getValue();
        }

        $company_branch = 'commercial';
        if (!empty($customerTypeValue) && $customerTypeValue === '101') {
            $company_branch = 'residential';
        }

        try {
            if ($company_branch === "commercial") {
                //Condition d) : Flat rate
                $this->freightHelper->logFrieghtMessage('-------commercial-------');
                $this->freightHelper->logFrieghtMessage('threshold CA - Flat rate');

                $resultCAThreshold['carrierCode'] = self::FLAT_RATE_CODE;
                $resultCAThreshold['carrierRate'] = 0;

                return $resultCAThreshold;
            }
            $this->freightHelper->logFrieghtMessage('-------residential-------');
            $configRegion = $this->checkRegion($this->canadaConfigData, $regionCode);
            if ($configRegion === '') {
                $this->freightHelper->logFrieghtMessage('threshold CA - Other ---- Flat Rate');
                $resultCAThreshold['carrierCode'] = self::FLAT_RATE_CODE;
                $resultCAThreshold['carrierRate'] = 0;
                return $resultCAThreshold;
            }

            $freeFrightLimit = 'canada_' . $configRegion . '_regional_free_freight_limit';
            $freeFrightLimitConfig = $this->freightHelper->getAdminConfigValue('freight_estimator/canada_freight/' . $freeFrightLimit);
            $this->freightHelper->logFrieghtMessage('US_FreeFreightLimit_Region ='.$freeFrightLimitConfig);
            if ((int)$cartTotal >= (int)$freeFrightLimitConfig) {
                if (!empty($this->freeFreight)) {
                    //Condition b) : free shipping
                    $this->freightHelper->logFrieghtMessage('threshold CA - Free Shipping');
                    $resultCAThreshold['carrierCode'] = self::FREE_SHIPPING_CODE;
                    $resultCAThreshold['carrierRate'] = 0;
                    return $resultCAThreshold;
                } else {
                    //Condition d) : Flat rate
                    $this->freightHelper->logFrieghtMessage('threshold CA - Flat Condition');
                    $resultCAThreshold['carrierCode'] = self::FLAT_RATE_CODE;
                    $resultCAThreshold['carrierRate'] = 0;
                    return $resultCAThreshold;
                }
            } else {
                $freeFrightUpChargeConfig = $this->freightHelper->getAdminConfigValue('freight_estimator/canada_freight/canada_freight_upcharge_order_threshold');
                $price = $this->freightHelper->getAdminConfigValue('freight_estimator/canada_freight/canada_' . $configRegion . '_flat_rate_overThreshold');
                $this->freightHelper->logFrieghtMessage('threshold CA - CanadaFreight_UpchargeOrderThreshold'.$freeFrightUpChargeConfig);
                if ((int)$cartTotal >= (int)$freeFrightUpChargeConfig) {
                    $price = $this->freightHelper->getAdminConfigValue('freight_estimator/canada_freight/canada_' . $configRegion . '_flat_rate_upToThreshold');
                }
            }
            $resultCAThreshold['carrierCode'] = self::CANADA_FLAT_RATE_CODE;
            $resultCAThreshold['carrierRate'] = $price;

            $this->freightHelper->logFrieghtMessage('--- Canadian Flat Rate --'. $price);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $resultCAThreshold;
    }

    public function checkRegion($configData, $shippingRegion)
    {
        if (!empty($shippingRegion)) {
            foreach ($configData as $key => $value) {
                $configRegion = $this->freightHelper->getAdminConfigValue($value);
                if (!empty($configRegion)) {
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
                }
            }
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
            if (($r[0] === 'us') && ($r[1] === 'region')) {
                return $r[2];
            }
        }

        return '';
    }

    /**
     * check Mirror products are exist or not
     * @param array $items
     * @return bool
     */

    public function isMirrorProduct(array $items): bool
    {
        foreach ($items as $item) {
            if ($item['parent_item_id']) {
             continue;
            }
            $productData = $item->getProduct();
            if ($this->mirrorAttributeSetId === null) {
                $this->mirrorAttributeSetId = $this->freightHelper
                        ->getAttributeSetIdByName(CreateCategoriesAttributeSets::MIRROR_ATTRIBUTESET_NAME);
            }
            if ((int)$productData->getAttributeSetId() === $this->mirrorAttributeSetId) {
                return true;
            }
        }
        return false;
    }
}
