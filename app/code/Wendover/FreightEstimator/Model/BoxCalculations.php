<?php
/**
 * Around Plugin Used for Box Size Calculation
 * @category: Magento
 * @license: Magento Enterprise Edition (MEE) license
 * @project: Wendover
 */
declare(strict_types=1);

namespace Wendover\FreightEstimator\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Serialize\Serializer\Json;
use Wendover\Catalog\Setup\Patch\Data\CreateCategoriesAttributeSets;
use Wendover\FreightEstimator\Helper\Data as FreightHelper;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Perficient\Rabbitmq\Api\Data\TreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\TreatmentRepositoryInterface;
use Perficient\Rabbitmq\Model\TreatmentRepository as TreatmentRepositoryFetch;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session as checkoutSession;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote;

/**
 * Class BoxCalculations
 */
class BoxCalculations
{
    const MORE_THAN_ONE = 1.0;

    const PZ_CART = "pz_cart_properties";

    const BUBBLE = "bubble";

    const BANDING = "banding";

    const FOAM = "foam";

    public const MAX_LONG_SIDE_FOR_BANDING_BOX ='freight_estimator/parcel_box_config/max_long_side_for_banding_box';

    public const MAX_ITEMS_PER_PARCEL_BOX_MIRROR ='freight_estimator/parcel_box_config/max_items_per_parcel_box_mirror';

    public const MAX_SIZE_DIFF_BUBBLE_LONGSIDE ='freight_estimator/max_size_diff/max_size_diff_bubble_longSide';

    public const MAX_SIZE_DIFF_BUBBLE_SHORTSIDE ='freight_estimator/max_size_diff/max_size_diff_bubble_shortSide';

    public const MAX_SIZE_DIFF_BANDING_LONGSIDE ='freight_estimator/max_size_diff/max_size_diff_banding_longSide';

    public const MAX_SIZE_DIFF_BANDING_SHORTSIDE ='freight_estimator/max_size_diff/max_size_diff_banding_shortSide';

    public const PACK_MATERIALS_SIZE_BUBBLE_TOLONGSIDE ='freight_estimator/Pack_materials_size/pack_materials_size_bubble_toLongSide';

    public const PACK_MATERIALS_SIZE_BUBBLE_TOSHORTSIDE ='freight_estimator/Pack_materials_size/pack_materials_size_bubble_toShortSide';

    public const PACK_MATERIALS_SIZE_BUBBLE_TODEPTH ='freight_estimator/Pack_materials_size/pack_materials_size_bubble_toDepth';

    public const PACK_MATERIALS_SIZE_BUBBLE_BETWEENPIECES ='freight_estimator/Pack_materials_size/pack_materials_size_bubble_betweenPieces';

    public const PACK_MATERIALS_SIZE_FOAM_TOLONGSIDE ='freight_estimator/Pack_materials_size/pack_materials_size_foam_toLongSide';

    public const PACK_MATERIALS_SIZE_FOAM_TOSHORTSIDE ='freight_estimator/Pack_materials_size/pack_materials_size_foam_toShortSide';

    public const PACK_MATERIALS_SIZE_FOAM_TODEPTH ='freight_estimator/Pack_materials_size/pack_materials_size_foam_toDepth';

    public const PACK_MATERIALS_SIZE_FOAM_BETWEENPIECES ='freight_estimator/Pack_materials_size/pack_materials_size_foam_betweenPieces';

    public const PACK_MATERIALS_SIZE_BANDING_TOLONGSIDE ='freight_estimator/Pack_materials_size/pack_materials_size_banding_toLongSide';

    public const PACK_MATERIAL_SIZE_BANDING_TOSHORTSIDE ='freight_estimator/Pack_materials_size/pack_materials_size_banding_toShortSide';

    public const PACK_MATERIALS_SIZE_BANDING_TODEPTH ='freight_estimator/Pack_materials_size/pack_materials_size_banding_toDepth';

    public const PACK_MATERIALS_SIZE_BANDING_BETWEENPIECES ='freight_estimator/Pack_materials_size/pack_materials_size_banding_betweenPieces';

    public const MAX_SIZE_PARCEL_BOX ='freight_estimator/parcel_box_config/max_size_parcel_box';

    public const MAX_WEIGHT_PER_PARCEL_BOX ='freight_estimator/parcel_box_config/max_weight_per_parcel_box';

    public const MAX_ITEMS_PER_PARCEL_BOX ='freight_estimator/parcel_box_config/max_items_per_parcel_box';

    public const MAX_SIZE_DIFF_FOAM_LONGSIDE ='freight_estimator/Pack_materials_size/max_size_diff_foam_longSide';

    public const MAX_SIZE_DIFF_FOAM_SHORTSIDE ='freight_estimator/Pack_materials_size/max_size_diff_foam_shortSide';

    /**
     * @var int|null
     */
    private $mirrorAttributeSetId = null;

    /**
     * @var array
     */
    protected $resultArr = [];

    /**
     * @var int
     */
    protected $boxCount = 0;

    /**
     * @var int
     */
    protected $productCount = 0;

    /**
     * @var int
     */
    protected $mirrorProductCount = 0;


    /**
     * @var int
     */
    protected $individualBoxing;

    /**
     * @var array
     */
    protected $quotesArray;

    /**
     * @var array
     */
    protected $finalResultArr = [];

    /**
     * @var array
     */
    protected $boxOutput = [];

    /**
     * @var int
     */
    protected $qtyCount = 0;

    /**
     * @var string
     */
    protected $packageMethod = self::BANDING;

    /**
     * Array of quotes
     *
     * @var array
     */
    protected static $boxCache = [];

    /**
     * @var int
     */
    protected $box_id = 1;

    /**
     * @var double
     */
    protected $sumItemDepth = 0;

    /**
     * @var array
     */
    protected $productItemDepth = [];

    /**
     * @var int
     */
    protected $quoteItemCount = 0;

    /**
     * @var int
     */
    protected $itemsCountInBox = 0;

    /**
     * @var int
     */
    protected $changeShippingMethod = 0;

    protected $quotesArtArray = [];

    protected $quotesMirrorArray = [];



    /**
     * @param CustomerSession $customerSession
     * @param Json $jsonSerialize
     * @param FreightHelper $freightHelper
     * @param LoggerInterface $logger
     * @param Cart $cart
     * @param CartRepositoryInterface $cartRepository
     * @param TreatmentInterfaceFactory $treatmentInterfaceFactory
     * @param TreatmentRepositoryInterface $treatmentRepositoryInterface
     * @param TreatmentRepositoryFetch $treatmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ProductFactory $productFactory
     * @param checkoutSession $checkoutSession
     * @param Product $product
     */
    public function __construct(
        protected CustomerSession                     $customerSession,
        protected Json                                $jsonSerialize,
        protected FreightHelper                       $freightHelper,
        protected LoggerInterface                     $logger,
        protected Cart                                $cart,
        protected CartRepositoryInterface             $cartRepository,
        private readonly TreatmentInterfaceFactory    $treatmentInterfaceFactory,
        private readonly TreatmentRepositoryInterface $treatmentRepositoryInterface,
        private readonly TreatmentRepositoryFetch     $treatmentRepository,
        private readonly SearchCriteriaBuilder        $searchCriteriaBuilder,
        private readonly FilterBuilder                $filterBuilder,
        private readonly ProductFactory               $productFactory,
        protected checkoutSession                     $checkoutSession,
        private readonly ProductRepositoryInterface   $productRepository,
    )
    {
    }

    /**
     * callBoxCalculations
     * @return array
     */
    public function callBoxCalculations()
    {
        //Format quote items to array
        $this->freightHelper->logFrieghtMessage('------- Box Calculations starts-------');
        $quoteId = $this->cart->getQuote()->getId();
        $quote = $this->cartRepository->get($quoteId);
        $items = $quote->getAllItems();
        $this->quoteItemCount = count($items);
        $maxLongSideForBandingBox = $this->freightHelper->getAdminConfigValue(self::MAX_LONG_SIDE_FOR_BANDING_BOX);
        $quotesArray = [];

        $uniQueProducts = [];
        try {
            foreach ($items as $item) {


                $buyRequest = $this->jsonSerialize->unserialize($item->getOptionByCode('info_buyRequest')->getValue());
                if (!array_key_exists(self::PZ_CART, $buyRequest)) {
                    continue;
                }

                if (empty($buyRequest['pz_cart_properties'])) {
                    continue;
                }
                $pz_cart_properties = $this->jsonSerialize->unserialize($buyRequest['pz_cart_properties']);
                $productId = $item->getProductId();
                $productData = $item->getProduct();
                $getAttributeSetId = $productData->getAttributeSetId();

                $width = (float)$pz_cart_properties['Item Width'] ?? 0;
                $height =  (float)$pz_cart_properties['Item Height'] ?? 0;
                $glassWidth =  (float)$pz_cart_properties['Glass Width'] ?? 0;
                $glassHeight =  (float)$pz_cart_properties['Glass Height'] ?? 0;

                $frameDepthValue = $this->freightHelper->getAdminConfigValue(FreightHelper::DEFAULT_FRAME_DEPTH);
                $frameDepth = empty($pz_cart_properties['Frame Depth']) ? (float)$frameDepthValue : (float)$pz_cart_properties['Frame Depth'];
                $this->freightHelper->logFrieghtMessage('Frame Depths --'.$frameDepth);

                if ($this->mirrorAttributeSetId === null) {
                    $this->mirrorAttributeSetId = $this->freightHelper
                                ->getAttributeSetIdByName(CreateCategoriesAttributeSets::MIRROR_ATTRIBUTESET_NAME);
                }
                if ((int)$getAttributeSetId === $this->mirrorAttributeSetId) {
                    if ($item['parent_item_id']) {
                        continue;
                    }
                    $this->packageMethod = self::FOAM;
                    $frameSku = $pz_cart_properties['Frame'];
                    $this->freightHelper->logFrieghtMessage("---packageMethod--->".$this->packageMethod);
                    if ($width == 0 || $height == 0 || $frameDepth == 0) {
                        $this->freightHelper->logFrieghtMessage('----Items Value Are Missing-----');
                        return $this->finalResultArr;
                    }
                    $weight = 0;
                    $itemData = [
                        'productId' => $productId,
                        'itemWidth' => $width,
                        'itemHeight' => $height,
                        'glassWidth' => $glassWidth,
                        'glassHeight' => $glassHeight
                    ];
                    $weight = (float)$productData->getWeight();
                    $this->freightHelper->logFrieghtMessage("---Weight--->".$weight);
                    $this->quoteItemArray($item, $width, $height, $frameDepth, $weight, $glassWidth, $glassHeight,true);
                } else {
                    //Art products

                    if ($width == 0 || $height == 0 || $frameDepth == 0) {
                        $this->freightHelper->logFrieghtMessage('----Items Value Are Missing-----');
                        return $this->finalResultArr;
                    }
                    $weight = 0;
                    $itemData = [
                        'productId' => $productId,
                        'itemWidth' => $width,
                        'itemHeight' => $height,
                        'glassWidth' => $glassWidth,
                        'glassHeight' => $glassHeight
                    ];
                    $weight = $this->freightHelper->calculateItemWeight($itemData);
                    $this->freightHelper->logFrieghtMessage("---Weight--->".$weight);
                    $this->freightHelper->logFrieghtMessage("---max([width, height]--->".max([$width, $height]));
                    $this->freightHelper->logFrieghtMessage("---maxLongSideForBandingBox--->".$maxLongSideForBandingBox);
                    $this->packageMethod = self::BANDING;
                    if (max([$width, $height]) > $maxLongSideForBandingBox) {
                        $this->packageMethod = self::BUBBLE;
                    }
                    $this->freightHelper->logFrieghtMessage('----packageMethod-----'.$this->packageMethod);
                    $this->quoteItemArray( $item, $width, $height, $frameDepth, $weight, $glassWidth, $glassHeight,false);
                }
            }

            $this->freightHelper->logFrieghtMessage('Processed Array:');
            $this->freightHelper->logFrieghtMessage(json_encode($this->quotesArtArray, 1));
            $this->freightHelper->logFrieghtMessage(json_encode($this->quotesMirrorArray, 1));

            //Step-1: Sorting array
            $quotesArtArray = $this->phpArraySort($this->quotesArtArray, ['area', 'long_side', 'short_side']);
            $this->freightHelper->logFrieghtMessage("Quote Data once sorted: ".json_encode($quotesArtArray, 1));
            $quotesMirrorArray = $this->phpArraySort($this->quotesMirrorArray, ['area', 'long_side', 'short_side']);
            $this->freightHelper->logFrieghtMessage("Quote Data once sorted: ".json_encode($quotesMirrorArray, 1));

            //Data initalization
            $requireIndividualBoxingFlag = $this->customerSession->getRequireIndividualBoxing();
            if (isset($requireIndividualBoxingFlag)) {
                $this->individualBoxing = $requireIndividualBoxingFlag;
            }

            if (count($quotesArtArray)){
                $this->iterateItems($quotesArtArray);
            }

            if (count($quotesMirrorArray)){
                $this->mirrorIterateItems($quotesMirrorArray);
            }

            $this->freightHelper->logFrieghtMessage('Final Array:');
            $this->freightHelper->logFrieghtMessage(json_encode($this->finalResultArr));
            $this->finalResultArr['max_box_weight'] = array_column($this->resultArr, 'box_weight') ? max(array_column($this->resultArr, 'box_weight')) : NULL;
            $this->finalResultArr['max_box_size'] = array_column($this->resultArr, 'box_size') ? max(array_column($this->resultArr, 'box_size')): NULL;
            $this->finalResultArr['total_area'] = array_column($this->resultArr, 'area') ? round(array_sum(array_column($this->resultArr, 'area')), 2) : NULL;
            $this->finalResultArr['number_of_boxes'] = array_column($this->resultArr, 'box_id')? max(array_column($this->resultArr, 'box_id')) : NULL;
            $this->finalResultArr['box_details'] = $this->resultArr;
            $this->checkoutSession->setBoxDetails($this->resultArr);
            $this->finalResultArr['timestamp'] = time();
            $this->setBoxCached($quotesArray, $this->finalResultArr);
            $this->freightHelper->logFrieghtMessage('Box Output:');
            $this->freightHelper->logFrieghtMessage(json_encode($this->finalResultArr));
            return $this->finalResultArr;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param Quote $item
     * @param float $width
     * @param float $height
     * @param float $frameDepth
     * @param float $weight
     * @param float $glassWidth
     * @param float $glassHeight
     * @param bool $isMirror
     */
    public function quoteItemArray( $item, float $width, float $height, float $frameDepth, float  $weight,  float $glassWidth, float  $glassHeight, bool $isMirror)
    {
        $this->qtyCount = (int)$item->getQty();
        for ($i = 0; $i < $this->qtyCount; $i++) {
            $quotesArray =
                [
                    "sku" => $item->getSku(),
                    "qty" => self::MORE_THAN_ONE,
                    "item_id" => $item->getProductId(),
                    "width" => $width,
                    "height" => $height,
                    "frame_depth" => $frameDepth,
                    "long_side" => max([$width, $height]),
                    "short_side" => min([$width, $height]),
                    "area" => ($width * $height) / 144,
                    "weight" => $weight,
                    "glass_width" => $glassWidth,
                    "glass_height" => $glassHeight,
                    "package" => $this->packageMethod,
                    "cart_qty" => (int) $item->getQty()
                ];
            if($isMirror){
                $this->quotesMirrorArray[] = $quotesArray;
            } else {
                $this->quotesArtArray[] = $quotesArray;
            }
            }
    }

    /**
     * @param $quotesArray
     * @param int $offset
     *
     */
    public function iterateItems($quotesArray, int $offset = 0)
    {
        //Condition1: IndividualBoxing & Slicing 4 Boxes will have at most 4 pieces of art
        $this->freightHelper->logFrieghtMessage('========= iterateItems');
        if (!empty($this->individualBoxing)) {
            $quotes = array_slice($quotesArray, $offset, 1);
        } else {
            $quotes = array_slice($quotesArray, $offset, 4);
        }
        //Call box details
        $boxShipTogether = $this->boxCalculations($quotes);
        if ($this->changeShippingMethod === 1) {
            return ['change_shipping_method' => $this->changeShippingMethod];
        }
        $this->freightHelper->logFrieghtMessage('products in a box---->'. json_encode($boxShipTogether, 1));
        $this->resultArr[] = $boxShipTogether;
        $boxShipTogetherCount = 0;
        if (!empty($boxShipTogether)) {
            $boxShipTogetherCount = $boxShipTogether['count_items_in_box'];
        }
        $this->productCount += $boxShipTogetherCount;
        $this->freightHelper->logFrieghtMessage('---Box count -----> '.$this->boxCount);

        $this->freightHelper->logFrieghtMessage('artProductCount---'.$this->productCount);
        $this->freightHelper->logFrieghtMessage('quotesArtArray---'.is_countable($quotesArray));
        $this->freightHelper->logFrieghtMessage('countarray---'.count($quotesArray));

        $varCountQuoteArray = is_countable($quotesArray) ? count($quotesArray) : 0;
        if ($this->productCount < (is_countable($quotesArray) ? count($quotesArray) : 0)) {
            //Recurssive call till all quote items are considered in boxes
            $this->freightHelper->logFrieghtMessage('Recurssive call till all quote items are considered in boxes');
            $this->box_id++;
            $this->iterateItems($quotesArray, $this->productCount);
        }
    }

    /**
     * @param $quotesMirrorArray
     * @param $offset
     * @return void
     */
    public function mirrorIterateItems($quotesMirrorArray, int $offset = 0): void
    {
        $maxMirrorItemsPerParcelBox = (int)$this->freightHelper->getAdminConfigValue(self::MAX_ITEMS_PER_PARCEL_BOX_MIRROR);
        $quotes = array_slice($quotesMirrorArray, $offset, $maxMirrorItemsPerParcelBox);
        $boxCount =  array_column($this->resultArr, 'box_id')? max(array_column($this->resultArr, 'box_id')):0;
        $this->freightHelper->logFrieghtMessage("art count: ".$boxCount);
        $this->freightHelper->logFrieghtMessage($boxCount.'---box count and mirror array---'.json_encode($quotes));
        $mirrorBoxShipTogether = $this->mirrorBoxCalculations($quotes, $boxCount);
        if (empty($mirrorBoxShipTogether)) {
           return;
        }
        if (count($mirrorBoxShipTogether)) {
            $this->resultArr[] = $mirrorBoxShipTogether;
        }
        $boxShipTogetherCount = 0;
        if (!empty($mirrorBoxShipTogether)) {
            $boxShipTogetherCount = $mirrorBoxShipTogether['count_items_in_box'];
        }
        $this->freightHelper->logFrieghtMessage('boxShipTogetherCount---'.$boxShipTogetherCount);
        $this->mirrorProductCount += $boxShipTogetherCount;
        $this->freightHelper->logFrieghtMessage('mirrorProductCount---'.$this->mirrorProductCount);
        $this->freightHelper->logFrieghtMessage('quotesMirrorArray---'.is_countable($quotesMirrorArray));
        $this->freightHelper->logFrieghtMessage('countarray---'.count($quotesMirrorArray));
        if ($this->mirrorProductCount < (is_countable($quotesMirrorArray) ? count($quotesMirrorArray) : 0)) {
            //Recurssive call till all quote items are considered in boxes
            $this->freightHelper->logFrieghtMessage('Recurssive call till mirror quote items are considered in boxes');
            $this->mirrorIterateItems($quotesMirrorArray, $this->mirrorProductCount);
        }
        $boxCount =  array_column($this->resultArr, 'box_id')? max(array_column($this->resultArr, 'box_id')):0;
        $this->freightHelper->logFrieghtMessage("art and mirror count: ".$boxCount);
    }

    /**
     * @param $request
     * @return int
     */
    public function maxSideDifferenceValues($request)
    {
        $result = '';
        switch ($this->packageMethod) {
            case self::BUBBLE:
                $result = ($request === 'longside')
                    ? $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_DIFF_BUBBLE_LONGSIDE)
                    : $result = $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_DIFF_BUBBLE_SHORTSIDE);
                break;
            case self::FOAM:
                $result = ($request === 'longside')
                    ? $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_DIFF_FOAM_LONGSIDE)
                    : $result = $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_DIFF_FOAM_SHORTSIDE);
                break;
            default:
                $result = ($request === 'longside')
                    ? $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_DIFF_BANDING_LONGSIDE)
                    : $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_DIFF_BANDING_SHORTSIDE);
        }

        return $result;
    }
    /**
     * @param $maxLongSide
     * @param $maxShortSide
     * @param $itemDepth
     * @return void
     */
    public function getBoxDetails($maxLongSide, $maxShortSide, $itemDepth, $itemsInBox, $packageMethod)
    {
        //Calculate box size based on the packaging method:
        $boxLength = 0;
        $boxWidth = 0;
        $boxDepth = 0;
        $boxDepth_ToCalculate = ($itemsInBox - 1);

        if ($packageMethod === self::BUBBLE) {
            // Bubble validation conditions from admin configuration values
            $packMaterialsSize_Bubble_ToLongSide = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BUBBLE_TOLONGSIDE);
            $packMaterialsSize_Bubble_ToShortSide = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BUBBLE_TOSHORTSIDE);
            $packMaterialsSize_Bubble_ToDepth = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BUBBLE_TODEPTH);
            $packMaterialsSize_Bubble_BetweenPieces = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BUBBLE_BETWEENPIECES);
            $boxLength = round($maxLongSide + $packMaterialsSize_Bubble_ToLongSide);
            $boxWidth = round($maxShortSide + $packMaterialsSize_Bubble_ToShortSide);
            $boxDepth = round($itemDepth + $packMaterialsSize_Bubble_ToDepth + ($boxDepth_ToCalculate * $packMaterialsSize_Bubble_BetweenPieces));
        } else if ($packageMethod === self::FOAM) {
            // Foam validation conditions from admin configuration values
            $packMaterialsSize_Foam_ToLongSide = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_FOAM_TOLONGSIDE);
            $packMaterialsSize_Foam_ToShortSide = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_FOAM_TOSHORTSIDE);
            $packMaterialsSize_Foam_ToDepth = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_FOAM_TODEPTH);
            $packMaterialsSize_Foam_BetweenPieces = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_FOAM_BETWEENPIECES);
            $boxLength = round($maxLongSide + $packMaterialsSize_Foam_ToLongSide);
            $boxWidth = round($maxShortSide + $packMaterialsSize_Foam_ToShortSide);
            $boxDepth = round($itemDepth + $packMaterialsSize_Foam_ToDepth + ($boxDepth_ToCalculate * $packMaterialsSize_Foam_BetweenPieces));
        } else {
            // Banding validation conditions from admin configuration values
            $packMaterialsSize_Banding_ToLongSide = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BANDING_TOLONGSIDE);
            $packMaterialsSize_Banding_ToShortSide = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIAL_SIZE_BANDING_TOSHORTSIDE);
            $packMaterialsSize_Banding_ToDepth = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BANDING_TODEPTH);
            $packMaterialsSize_Banding_BetweenPieces = $this->freightHelper->getAdminConfigValue(self::PACK_MATERIALS_SIZE_BANDING_BETWEENPIECES);

            $boxLength = round($maxLongSide + $packMaterialsSize_Banding_ToLongSide);
            $boxWidth = round($maxShortSide + $packMaterialsSize_Banding_ToShortSide);
            $boxDepth =  round(($itemDepth) + $packMaterialsSize_Banding_ToDepth + ($boxDepth_ToCalculate * $packMaterialsSize_Banding_BetweenPieces));
        }

        $boxSize = $boxLength + 2 * ($boxWidth + $boxDepth);

        $boxData = ['boxLength' => $boxLength,
                'boxWidth' => $boxWidth,
                'boxDepth' => $boxDepth,
                'boxSize' => $boxSize
            ];
        $this->freightHelper->logFrieghtMessage('**************box data ==================> ' . json_encode($boxData, 1));

        return $boxData;
    }

    /**
     * @param $quotes
     * @return array
     */
    public function boxCalculations($quotes)
    {
        $boxsTogether = [];

        if (empty($quotes)) {
            return $boxsTogether;
        }

        //Step 1: Get primary validation conditions from admin configuration values
        //==================================================================
        $maxSizeForParcelBox = $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_PARCEL_BOX);
        $maxWeightPerParcelBox = $this->freightHelper->getAdminConfigValue(self::MAX_WEIGHT_PER_PARCEL_BOX);
        $maxItemsPerParcelBox = $this->freightHelper->getAdminConfigValue(self::MAX_ITEMS_PER_PARCEL_BOX);

        //Get Maximum Side limitation defined in the admin configuration
        $maxSizeDiffLongSide = $this->maxSideDifferenceValues('longside');
        $maxSizeDiffShortSide = $this->maxSideDifferenceValues('shortside');

        //Step 2: Max dimension: Max dimension (i.e. long side) of items is within 2" of each other)
        //================================================================================================
        //Get Max Long and Min Long from the selected set of quote items
        $maxLongSide = max(array_column($quotes, 'long_side'));
        $minLongSide = min(array_column($quotes, 'long_side'));

        //Get Max Long and Min Long from the selected set of quote items
        $maxShortSide = max(array_column($quotes, 'short_side'));
        $minShortSide = min(array_column($quotes, 'short_side'));

        //Setp 3: Calculate Item Depth
        $sumItemDepth = 0.0;
        $itemsCountInBox = 0;
        $boxWeight = 0;
        $tempArray = [];
        $uniqueSku = [];
        $productQty = 0;
        foreach ($quotes as $key => $item) {
            $key++;
            $roundUp = 0;
            $tempArray['item_id'][] = $item['item_id'];
            $tempArray['long_side'][] = $item['long_side'];
            $tempArray['short_side'][] = $item['short_side'];
            $tempArray['frame_height'][] = $item['frame_depth'];
            $tempArray['area'][] = $item['area'];
            $tempArray['sku'][] = $item['sku'];
            $packageMethod = $item['package'];
            if (!in_array($item['sku'], $uniqueSku)) {
                $uniqueSku[] = $item['sku'];
                $productQty = 1;
            } else {
                $productQty++;
            }
            $currentElement = count($uniqueSku)-1;
            $boxWeight +=  $item['weight'];
            $sumItemDepth += $item['frame_depth'];
            if ($productQty === (int) $item['cart_qty'] && $uniqueSku[$currentElement] === $item['sku']) {
                $sumItemDepth = ceil($sumItemDepth);
                $roundUp = 1;
            }
            //Get Max short and Min short from the selected set of quote items
            $maxLongSide = max($tempArray['long_side']);
            $maxShortSide = max($tempArray['short_side']);
            $boxDetails = $this->getBoxDetails($maxLongSide, $maxShortSide, $sumItemDepth, $key, $packageMethod);
            $boxSize = $boxDetails['boxSize'];
            $boxLength = $boxDetails['boxLength'];
            $boxWidth = $boxDetails['boxWidth'];
            $boxDepth = $boxDetails['boxDepth'];
            if (($boxSize > $maxSizeForParcelBox) && ($key === 1)) {
                $this->changeShippingMethod = 1;
                return;
            }
        }

        $itemLongSideDifference = max($tempArray['long_side']) - min($tempArray['long_side']);
        $itemShortSideDifference = max($tempArray['short_side']) - min($tempArray['short_side']);
        $resetFlag = 0;

        if (count($quotes) === $key) {
            $this->freightHelper->logFrieghtMessage("Condition 1 --> Box Size ==>". $boxSize ."<= ".$maxSizeForParcelBox);
            $this->freightHelper->logFrieghtMessage("Condition 2 ---> Box Weight ===>".$boxWeight ."<= ".$maxWeightPerParcelBox);
            $this->freightHelper->logFrieghtMessage("Condition 3 ---> itemsCountInBox ===>".$itemsCountInBox." <= ".$maxItemsPerParcelBox);
            $this->freightHelper->logFrieghtMessage("Condition 4 ---> MaxSizeDiff Long Side ===>".$itemLongSideDifference." <= ".$maxSizeDiffLongSide);
            $this->freightHelper->logFrieghtMessage("Condition 5 ---> MaxSizeDiff Short Side ===>".$itemShortSideDifference." <= ".$maxSizeDiffShortSide);
            $addtoBox = 0;
            if (
                    $boxSize <= $maxSizeForParcelBox &&
                    $boxWeight <= $maxWeightPerParcelBox &&
                    $itemsCountInBox <= $maxItemsPerParcelBox &&
                    $itemLongSideDifference <= $maxSizeDiffLongSide &&
                    $itemShortSideDifference <= $maxSizeDiffShortSide
                ) {
                    $this->freightHelper->logFrieghtMessage("Items go to the exiting box ---->".$this->box_id);
                    $itemsCountInBox = $key; // Increment total no. of item in a box
                    $this->freightHelper->logFrieghtMessage('Items in a box *************'.$itemsCountInBox);
                    $this->box_id = $this->box_id;
                    $addtoBox = 1;
                } else if (count($quotes) == 1) {
                    $this->freightHelper->logFrieghtMessage(" Curent items in box ---->".$itemsCountInBox);
                    $this->freightHelper->logFrieghtMessage(" Increment box id---->".$this->boxCount);
                    ($this->boxCount > 0) ? $this->box_id++ : '';
                    $this->freightHelper->logFrieghtMessage("Item packed in a new box---->".$this->box_id);
                    $itemsCountInBox = 1; // Reset items in a new box as one
                    $resetFlag = 1;
                    $addtoBox = 1;
                }

                if ( $addtoBox == 1) {
                    $boxsTogether['items_in_box'] = $tempArray['item_id'];
                    $boxsTogether['sku_in_box'] = $tempArray['sku'];
                    $boxsTogether['count_items_in_box'] = $itemsCountInBox;
                    $boxsTogether['max_long_side'] = max($tempArray['long_side']);
                    $boxsTogether['max_short_side'] = max($tempArray['short_side']);
                    $boxsTogether['sum_frame_height'] = array_sum($tempArray['frame_height']);
                    $boxsTogether['area'] = round(array_sum($tempArray['area']), 2);
                    $boxsTogether['box_size'] = $boxSize;
                    $boxsTogether['box_length'] = $boxLength;
                    $boxsTogether['box_width'] = $boxWidth;
                    $boxsTogether['box_height'] = $boxDepth;
                    $boxsTogether['box_weight'] = $boxWeight;
                    $boxsTogether['box_item_long_side_diff'] = max($tempArray['long_side']) - min($tempArray['long_side']);
                    $boxsTogether['box_item_short_side_diff'] = max($tempArray['short_side']) - min($tempArray['short_side']);
                    //$boxsTogether['box_id'] = $this->box_id;
                    $this->boxCount++;
                    $boxsTogether['box_id'] = $this->boxCount;
                    if ($resetFlag === 1) {
                        $boxWeight = 0;
                        $sumItemDepth = 0;
                    }

                }
            }

        //Recurring call
        if (count($boxsTogether) == 0) {
            //internal call to see which from the selected set of 4 items require re-consideration
            $this->freightHelper->logFrieghtMessage('boxsTogether count zero need internal looping');
            $this->freightHelper->logFrieghtMessage(" Curent box count ---->".$this->boxCount);
            $quotes = array_slice($quotes, 0, (is_countable($quotes) ? count($quotes) : 0) - 1);
            $this->freightHelper->logFrieghtMessage("after inner slice ->".json_encode($quotes, 1));
            ($this->boxCount > 0) ? $this->box_id++ : '';
            $this->freightHelper->logFrieghtMessage("after increment box id inner call ->".$this->box_id);
            $boxsTogether = $this->boxCalculations($quotes);
        }
        $this->freightHelper->logFrieghtMessage(" art boxsTogether ---->".json_encode($boxsTogether));
        return $boxsTogether;
    }

    /**
     * @param array $quotes
     * @param int $boxCount
     * @return array
     */

     public function mirrorBoxCalculations(array $quotes,int $boxCount)
     {

         $boxsTogether = [];

         if (empty($quotes)) {
             return $boxsTogether;
         }
         //Step 1: Get primary validation conditions from admin configuration values
         //==================================================================
         $maxSizeForParcelBox = $this->freightHelper->getAdminConfigValue(self::MAX_SIZE_PARCEL_BOX);
         $maxWeightPerParcelBox = $this->freightHelper->getAdminConfigValue(self::MAX_WEIGHT_PER_PARCEL_BOX);
         $maxItemsPerParcelBox = $this->freightHelper->getAdminConfigValue(self::MAX_ITEMS_PER_PARCEL_BOX);

         //Get Maximum Side limitation defined in the admin configuration
         $maxSizeDiffLongSide = $this->maxSideDifferenceValues('longside');
         $maxSizeDiffShortSide = $this->maxSideDifferenceValues('shortside');

         //Step 2: Max dimension: Max dimension (i.e. long side) of items is within 2" of each other)
         //================================================================================================
         //Get Max Long and Min Long from the selected set of quote items
         $maxLongSide = max(array_column($quotes, 'long_side'));
         $minLongSide = min(array_column($quotes, 'long_side'));

         //Get Max Long and Min Long from the selected set of quote items
         $maxShortSide = max(array_column($quotes, 'short_side'));
         $minShortSide = min(array_column($quotes, 'short_side'));

         //Setp 3: Calculate Item Depth
         $sumItemDepth = 0.0;
         $itemsCountInBox = 0;
         $boxWeight = 0;
         $tempArray = [];
         $uniqueSku = [];
         $productQty = 0;
         foreach ($quotes as $key => $item) {
             $key++;
             $roundUp = 0;
             $tempArray['item_id'][] = $item['item_id'];
             $tempArray['long_side'][] = $item['long_side'];
             $tempArray['short_side'][] = $item['short_side'];
             $tempArray['frame_height'][] = $item['frame_depth'];
             $tempArray['area'][] = $item['area'];
             $tempArray['sku'][] = $item['sku'];
             $packageMethod = $item['package'];
             if (!in_array($item['sku'], $uniqueSku)) {
                 $uniqueSku[] = $item['sku'];
                 $productQty = 1;
             } else {
                 $productQty++;
             }
             $currentElement = count($uniqueSku)-1;
             $boxWeight +=  $item['weight'];
             $sumItemDepth += $item['frame_depth'];
             if ($productQty === (int) $item['cart_qty'] && $uniqueSku[$currentElement] === $item['sku']) {
                 $sumItemDepth = ceil($sumItemDepth);
                 $roundUp = 1;
             }
             //Get Max short and Min short from the selected set of quote items
             $maxLongSide = max($tempArray['long_side']);
             $maxShortSide = max($tempArray['short_side']);
             $itemsCountInBox = $key;
             $boxDetails = $this->getBoxDetails($maxLongSide, $maxShortSide, $sumItemDepth, $key, $packageMethod);
             $boxSize = $boxDetails['boxSize'];
             $boxLength = $boxDetails['boxLength'];
             $boxWidth = $boxDetails['boxWidth'];
             $boxDepth = $boxDetails['boxDepth'];
             if (($boxSize > $maxSizeForParcelBox) && ($key === 1)) {
                 $this->changeShippingMethod = 1;
                 return[];
             }
         }

         $itemLongSideDifference = max($tempArray['long_side']) - min($tempArray['long_side']);
         $itemShortSideDifference = max($tempArray['short_side']) - min($tempArray['short_side']);
         $resetFlag = 0;

         if (count($quotes) === $key) {
                 $boxsTogether['items_in_box'] = $tempArray['item_id'];
                 $boxsTogether['sku_in_box'] = $tempArray['sku'];
                 $boxsTogether['count_items_in_box'] = count($tempArray['sku']);
                 $boxsTogether['max_long_side'] = max($tempArray['long_side']);
                 $boxsTogether['max_short_side'] = max($tempArray['short_side']);
                 $boxsTogether['sum_frame_height'] = array_sum($tempArray['frame_height']);
                 $boxsTogether['area'] = round(array_sum($tempArray['area']), 2);
                 $boxsTogether['box_size'] = $boxSize;
                 $boxsTogether['box_length'] = $boxLength;
                 $boxsTogether['box_width'] = $boxWidth;
                 $boxsTogether['box_height'] = $boxDepth;
                 $boxsTogether['box_weight'] = $boxWeight;
                 $boxsTogether['box_item_long_side_diff'] = max($tempArray['long_side']) - min($tempArray['long_side']);
                 $boxsTogether['box_item_short_side_diff'] = max($tempArray['short_side']) - min($tempArray['short_side']);
                 //$boxsTogether['box_id'] = $this->box_id;
                 $this->boxCount++;
                 $boxsTogether['box_id'] = $this->boxCount;
                 if ($resetFlag === 1) {
                     $boxWeight = 0;
                     $sumItemDepth = 0;
                 }
         }

         //Recurring call
         if (count($boxsTogether) == 0) {
             //internal call to see which from the selected set of 4 items require re-consideration
             $this->freightHelper->logFrieghtMessage('boxsTogether count zero need internal looping');
             $this->freightHelper->logFrieghtMessage(" Curent box count ---->".$this->boxCount);
             $quotes = array_slice($quotes, 0, (is_countable($quotes) ? count($quotes) : 0) - 1);
             $this->freightHelper->logFrieghtMessage("after inner slice ->".json_encode($quotes, 1));
             ($this->boxCount > 0) ? $this->box_id++ : '';
             $this->freightHelper->logFrieghtMessage("after increment box id inner call ->".$this->box_id);
             $boxsTogether = $this->boxCalculations($quotes);
         }
         $this->freightHelper->logFrieghtMessage("mirror boxsTogether ---->".json_encode($boxsTogether));
         return $boxsTogether;

    }

    /**
     * @param $array
     * @param array $sortBy
     * @param int $sort
     * @return array
     */
    public function phpArraySort($array, $sortBy = [], $sort = SORT_REGULAR)
    {
        if (is_array($array) && count($array) > 0 && !empty($sortBy)) {
            $map = [];
            foreach ($array as $key => $val) {
                $sortKey = '';
                foreach ($sortBy as $keyKey) {
                    $sortKey .= $val[$keyKey];
                }
                $map[$key] = $sortKey;
            }
            asort($map, $sort);
            $sorted = [];
            foreach ($map as $key => $val) {
                $sorted[] = $array[$key];
            }
            return array_reverse($sorted);
        }
        return $array;
    }

    /**
     * @param array $requestParams
     * @return string
     */
    public function getBoxCacheKey($requestParams)
    {
        $requestParams = $this->jsonSerialize->serialize($requestParams);
        return crc32((string)$requestParams);
    }

    /**
     * @param array $requestParams
     * @return null|string
     */
    public function getBoxCached($requestParams)
    {
        $cache_ttl = $this->freightHelper->getAdminConfigValue(FreightHelper::XML_PATH_CACHE_TTL);
        $cacheKey = $this->getBoxCacheKey($requestParams);
        self::$boxCache = $this->customerSession->getQuotesCache();
        if (isset(self::$boxCache[$cacheKey])) {
            $cache = self::$boxCache[$cacheKey];
            if ($cache['timestamp'] < strtotime('-' . $cache_ttl . ' minutes')) {
                unset(self::$boxCache[$cacheKey]);
            }
            return self::$boxCache[$cacheKey] ?? null;
        }

        return null;
    }

    /**
     * @param array $requestParams
     * @param array $response
     * @return $this
     */
    public function setBoxCached($requestParams, $response)
    {
        if ($response != null) {
            $cacheKey = $this->getBoxCacheKey($requestParams);
            self::$boxCache[$cacheKey] = $response;
            $this->customerSession->setQuotesCache(self::$boxCache);
        }
        return $this;
    }

    public function getTreatmentDataById($productId)
    {
        $productData = $this->productFactory->create()->load($productId);
        $treatmentId = $productData->getResource()->getAttribute('treatment')->getFrontend()->getValue($productData);

        return $treatmentId ?? '';
    }
}
