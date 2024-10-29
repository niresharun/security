<?php
/**
 * Helper For Lift Gate Implementation
 * @category: Magento
 * @project: Wendover
 */
declare(strict_types=1);

namespace Wendover\FreightEstimator\Helper;

use Magento\Eav\Model\Entity\Attribute\Set as AttributeSetModel;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Checkout\Model\Cart;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Shipping\Model\Rate\ResultFactory as RateFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory as RateMethodFactory;
use Wendover\FreightEstimator\Helper\Data as FreightHelper;
use Wendover\FreightEstimator\Model\FreightConditions;
use Wendover\FreightEstimator\Logger\Logger as CustomLogger;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Perficient\Rabbitmq\Api\Data\TreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\TreatmentRepositoryInterface;
use Perficient\Rabbitmq\Model\TreatmentRepository as TreatmentRepositoryFetch;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;

class Data extends AbstractHelper
{
    const PATH_PALLET_WEIGHT_BLUESHIP = 'carriers/blueship/pallet_weight_gate';
    const SCOPE_ID = 0;
    const LIFT_GATE_LOADING_LOCK = 'Residential';
    const LOG_FREIGHT_DEBUGGING_LOG = '/var/log/freightLog.log';
    const XML_PATH_FREIGHT_DEBUGGING = 'freight_setting/general/freight_debugging';
    const XML_PATH_UPS_BOX_LIMIT1 = 'freight_setting/box_dimension/ups_box_limit1';
    const XML_PATH_UPS_BOX_LIMIT2 = 'freight_setting/box_dimension/ups_box_limit2';
    const XML_PATH_UPS_AREA_LIMIT = 'freight_setting/box_dimension/ups_area_limit';
    const XML_PATH_CACHE_TTL = 'freight_setting/general/cache_ttl';
    const DEFAULT_FRAME_DEPTH = 'freight_setting/general/frame_depth';

    const US_CODE = 'US';

    const CA_CODE = 'CA';
    /**
     * DIVISION_BY to calculate pallet weight default 96
     * $var int
     */
    const DIVISION_BY = 96;
    /**
     * MULTIPLICATION_BY to calculate pallet weight default 90
     */
    const MULTIPLICATION_BY = 90;

    /**
     * $var string
     */
    const FLAT_TITLE ='Flat Rate';
    const LOADING_DOCK_VALUE = 'No';
    const PZ_CART = "pz_cart_properties";
    private $palletWeight = 0;
    protected $debuggingEnabled;
    protected $result;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param Cart $cart
     * @param Config $eavConfig
     * @param LoggerInterface $logger
     * @param RateMethodFactory $rateMethodFactory
     * @param RateFactory $rateFactory
     * @param CustomLogger $customLogger
     * @param CustomerFactory $customerFactory
     * @param ProductFactory $productFactory
     * @param TreatmentInterfaceFactory $treatmentInterfaceFactory
     * @param TreatmentRepositoryInterface $treatmentRepositoryInterface
     * @param TreatmentRepositoryFetch $treatmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param Json $jsonSerialize
     * @param AttributeSetCollectionFactory $attributeSetCollectionFactory
     */
    public function __construct(
        Context                                       $context,
        protected ScopeConfigInterface                $scopeConfigInterface,
        protected Cart                                $cart,
        protected Config                              $eavConfig,
        protected LoggerInterface                     $logger,
        protected RateMethodFactory                   $rateMethodFactory,
        protected RateFactory                         $rateFactory,
        protected CustomLogger                        $customLogger,
        protected CustomerFactory                     $customerFactory,
        private readonly ProductFactory               $productFactory,
        private readonly TreatmentInterfaceFactory    $treatmentInterfaceFactory,
        private readonly TreatmentRepositoryInterface $treatmentRepositoryInterface,
        private readonly TreatmentRepositoryFetch     $treatmentRepository,
        private readonly SearchCriteriaBuilder        $searchCriteriaBuilder,
        private readonly FilterBuilder                $filterBuilder,
        protected Json                                $jsonSerialize,
        private readonly AttributeSetCollectionFactory $attributeSetCollectionFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return int
     */
    public function getAdminConfigValue($path)
    {
        return $this->scopeConfigInterface->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            self::SCOPE_ID
        );
    }

    /**
     * @param $totalItemWeight
     * @param $palletWidth
     * @return int
     */
    public function getPalletWeight($totalItemWeight, $palletWidth)
    {
        if ($palletWidth > 0) {
            $this->palletWeight = ceil($totalItemWeight +
                ($palletWidth / self::DIVISION_BY) * self::MULTIPLICATION_BY);
        }
        return $this->palletWeight;
    }

    /**
     * @param $path
     * @return bool
     */
    public function getConfigVal($path = '')
    {
        if ($path == '') {
            return false;
        }
        return $this->scopeConfigInterface->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration if frieght calculation logger is enabled or not
     */
    public function isFreightDebugging(): bool
    {
        return (bool) $this->scopeConfigInterface->getValue(
            Data::XML_PATH_FREIGHT_DEBUGGING,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $message
     * @param $message
     */
    public function logFrieghtMessage($message)
    {
        $debuggingEnabled = $this->isFreightDebugging();
        if (gettype($message) === 'array') {
            $message = json_encode($message);
        }
        if ($debuggingEnabled) {
            $this->customLogger->info($message);
        }
        /*$debuggingEnabled = $this->isFreightDebugging();
        if ($debuggingEnabled) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/freightLog.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($message);
        }*/
    }

    /**
     * @return mixed
     */
    public function flatRateShippingMethod()
    {
        $this->result = $this->rateFactory->create();
        $rate = $this->rateMethodFactory->create();
        /* Set carrier's method data */
        $flat_title =  $this->getAdminConfigValue('carriers/flatrate/name');
        $rate->setCarrier(FreightConditions::FLAT_RATE_CODE);
        $rate->setCarrierTitle($flat_title);
        /** Displayed as shipping method under Carrier */
        $rate->setMethod(FreightConditions::FLAT_RATE_CODE);
        $rate->setMethodTitle($flat_title);
        $rate->setPrice(0);
        $rate->setCost(0);
        $this->result->append($rate);
        return $rate;
    }

    /**
     * Get Customer Attribute Option Value
     * @param $optionId
     * @param $attributeCode
     * @throws LocalizedException
     */
    public function getAttributeOptionValue($optionId, $attributeCode): bool|string
    {
        $value = '';
        if ($optionId) {
            $attribute = $this->eavConfig->getAttribute('customer_address', $attributeCode);
            $value = $attribute->getSource()->getOptionText($optionId);
        }
        return $value;
    }

     /**
     * @return mixed
     */
    public function freightManualCountry()
    {
        $usTerritories = 'freight_estimator/us_freight/us_region_5';
        $configRegion = $this->getAdminConfigValue($usTerritories);
        $freightManualCountry = [
            self::US_CODE,
            self::CA_CODE
        ];
        return $freightManualCountry;
    }


     /**
      * Function getCustomerFreeFreigh
     * @return mixed
     */
    public function getCustomerFreeFreight($customerId)
    {
        try {
            $customer = $this->customerFactory->create()->load($customerId);
           return $customer->getData('qualifies_for_free_freight');
               } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Perform Item Weight Calculation
     *
     * @param $item
     *
     * @return float
     */
    public function calculateItemWeight($itemData) {
        $weight = 0;

        $this->logFrieghtMessage('-- Item weight calculation:-- ');

        if ($itemData) {
            $glassSizeItemWeightValue = $this->getAdminConfigValue(
                'freight_estimator/max_size_diff/glass_size_threshold_item_weights');

            $width = $itemData['itemWidth'];
            $height = $itemData['itemHeight'];
            $glassWidth = $itemData['glassWidth'];
            $glassHeight = $itemData['glassHeight'];

            $productId = $itemData['productId'];
            $treatmentArray = $this->getTreatmentData($productId);

            if (($glassWidth > 0) && ($glassHeight > 0) && !empty($treatmentArray)) {
                $treatmentWeightThreshold = $treatmentArray['treatment_weight_over_threshold'] ?? 0.0;
                if (($glassWidth * $glassHeight / 144) <= $glassSizeItemWeightValue) {
                    $treatmentWeightThreshold = $treatmentArray['treatment_weight_upto_threshold'] ?? 0.0;
                }
                $this->logFrieghtMessage('treatmentWeightThreshold--'.$treatmentWeightThreshold);
                $this->logFrieghtMessage("width--->". $width);
                $this->logFrieghtMessage("height--->". $height);
                $weight = (( $width * $height ) / 144 ) * $treatmentWeightThreshold;
                $this->logFrieghtMessage('Item weight--'.$weight);
            }
        }

        return $weight;
    }

    public function getTreatmentDataById($productId)
    {
        $productData = $this->productFactory->create()->load($productId);
        $treatmentId = $productData->getResource()->getAttribute('treatment')->getFrontend()->getValue($productData);
        return $treatmentId ?? '';
    }

    /**
     * @param $productId
     * @return array
     */
    public function getTreatmentData($productId) {
        $treatmentId = $this->getTreatmentDataById($productId);
        $treatmentArray = [];
        if (!empty($treatmentId)) {
            $filtersTreatment = [
                $this->filterBuilder
                    ->setField('main_table.treatment_sku')
                    ->setValue($treatmentId)
                    ->setConditionType('eq')
                    ->create()
            ];
            $searchCriteriaTreatment = $this->searchCriteriaBuilder
                ->addFilters($filtersTreatment)
                ->create();
            $treatmentRepository = $this->treatmentRepository->getList($searchCriteriaTreatment);
            if (is_countable($treatmentRepository->getItems()) ? count($treatmentRepository->getItems()) : 0) {
                foreach ($treatmentRepository->getItems() as $treatItem) {
                    $treatmentArray['treatment_weight_upto_threshold'] = $treatItem->getTreatmentWeightPerSqFtUpToThreshold();
                    $treatmentArray['treatment_weight_over_threshold'] = $treatItem->getTreatmentWeightPerSqFtOverThreshold();
                    $this->logFrieghtMessage('treatment_weight_upto_threshold => '.$treatmentArray['treatment_weight_upto_threshold']);
                    $this->logFrieghtMessage('treatment_weight_over_threshold => '.$treatmentArray['treatment_weight_over_threshold']);
                }
            }
        }
        $this->logFrieghtMessage("---treatmentArray--->".json_encode($treatmentArray, 1));

        return $treatmentArray;
    }

    /**
     * @param string $attributeName
     *
     * @return int
     */
    public function getAttributeSetIdByName(string $attributeName): int
    {
        $attributeSet = $this->attributeSetCollectionFactory->create()
            ->addFieldToFilter(AttributeSetModel::KEY_ATTRIBUTE_SET_NAME, $attributeName)->getFirstItem();
        if ($attributeSet->getId()) {
            return (int)$attributeSet->getId();
        }
        return 0;
    }
}
