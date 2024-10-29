<?php
/**
 * Perficient Catalog helper.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Helper;

use Exception;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Catalog\Block\GetData;
use Perficient\Productimize\Model\ProductDetails;
use Psr\Log\LoggerInterface;
use Wendover\Catalog\Setup\Patch\Data\CreateCategoriesAttributeSets;
use Wendover\Catalog\ViewModel\WendoverViewModel;
use Wendover\FreightEstimator\Helper\Data as FreightEstimatorHelper;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Class Data
 * @package Perficient\Catalog\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var null|int
     */
    protected $mirrorAttributeSetId = null;
    const MEDIA_PATH = 'catalog/product/default_configuration';
    const MEDIA_PATH_PLP_PRODUCTS = 'catalog/product/specifications';
    const FRAME_ATTR_SET = 'Frame';
    const MAT_ATTR_SET = 'Mat';
    const DIRECTORY_SEPARATOR = '/';
    const GUEST_PRICE_MULTIPLIER_PATH = 'sales/general/non_logged_in_price_multiplier';
    const FRAME_DAYS_TO_BACK_IN_STOCK = 'days_to_in_stock';
    const FRAME_STOCK_CHECK_URL = 'checkstock/index/checkframestock';
    const XML_PATH_STOCK_ALLOW = 'catalog/productalert/allow_stock';

    /**
     * @var array
     */
    public static $defaultConfLabel = [
        'liner_sku' => 'Liner',
        'frame_default_sku' => 'Frame',
        'top_mat_default_sku' => 'Top Mat',
        'bottom_mat_default_sku' => 'Bottom Mat',
        //'side-mark' => 'Side Mark',
        //'bottom_mat_sku' => 'Bottom Mat SKUs',
        'frame_width' => 'Frame Width',
        //'frame_depth' => 'Frame Depth',
        //'frame_color_default' => 'Frame Color',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        'medium' => 'Medium',
        'glass_width' => 'Glass Width',
        'glass_height' => 'Glass Height',
        'art_work_color' => 'Artwork Color',
        'side_mark' => 'Side Mark',
        'liner_width' => 'Liner Width',
        //'liner_color_default' => 'Liner Color',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        //'bottom_mat_color_default' => 'Bottom Mat Color',
        'image_height' => 'Image Height',
        'image_width' => 'Image Width',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
        //'top_mat_color_default' => 'Top Mat Color',
        'treatment' => 'Treatment',
        'default_frame_depth' => 'Frame Depth',
        'default_liner_depth' => 'Liner Depth',
        'default_frame_color' => 'Frame Color',
        'default_liner_color' => 'Liner Color',
        'default_top_mat_color' => 'Top Mat Color',
        'default_bottom_mat_color' => 'Bottom Mat Color'
    ];

    public static $defaultConfMirrorProductLabel = [
        'simplified_medium' => 'Medium',
        'glass_type' => 'Glass',
        'size_string' => 'Size',
        'frame_default_sku' => 'Frame',
        'color_frame' => 'Frame Color',
        'frame_width' => 'Frame Width',
        'default_frame_depth' => 'Frame Depth',
        'glass_height' => 'Glass Height',
        'glass_width' => 'Glass Width',
        'item_width' => 'Item Width',
        'item_height' => 'Item Height'
    ];

    public static $expectedConfMirrorProductLabel = [
        'Medium' => 'Medium',
        'Glass' => 'Glass',
        'Size' => 'Size',
        'Frame' => ['Frame Color', 'Frame Width', 'Frame Depth']
    ];

    // Used for Productimize Extension observer to update customize price in Cart.
    /**
     * @var array
     * Used in pricing - need to check if other details needs to be added****
     */
    public static $productimizeConfLabel = [
        'liner_sku' => 'Liner',
        //'frame_sku' => 'Frame',
        'frame_default_sku' => 'Frame',
        //'top_mat_sku' => 'Top Mat',
        //'bottom_mat_sku' => 'Bottom Mat',
        'top_mat_default_sku' => 'Top Mat',
        'bottom_mat_default_sku' => 'Bottom Mat',
        'medium' => 'Medium',
        'treatment' => 'Treatment',
        'glass_width' => 'Glass Width',
        'glass_height' => 'Glass Height',
        'image_height' => 'Image Height',
        'image_width' => 'Image Width',
        'art_work_color' => 'Artwork Color'
    ];

    /**
     * @var array
     * Used at multiple places - need to check if other details needs to be added****
     */
    public static $textOnlyOptions = [
        'medium' => 'Medium',
        'treatment' => 'Treatment',
        //?//'frame_skus' => 'Frame SKUs',
        'frame_default_sku' => 'Frame',
        'frame_width' => 'Frame Width',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        //?//'top_mat_skus' => 'Top Mat SKUs',
        'top_mat_default_sku' => 'Top Mat',
        'liner_width' => 'Liner Width',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
    ];
    /**
     * @var array
     * Used only on PDP
     */
    public static $pdpSwatchesList = [
        'frame_default_sku',
        'liner_sku',
        'top_mat_default_sku',
        'bottom_mat_default_sku',
    ];

    /**
     * @var array
     * Used only on PDP
     */
    public static $pdpLabelsList = [
        'medium',
        'treatment',
        'item_height',
        'item_width',
        'frame_width',
        //'frame_depth',
        'default_frame_depth',
        'liner_width',
        //'liner_depth',
        'default_liner_depth',
        'top_mat_size_bottom',
        'top_mat_size_left',
        'top_mat_size_right',
        'top_mat_size_top',
        'bottom_mat_size_bottom',
        'bottom_mat_size_left',
        'bottom_mat_size_right',
        'bottom_mat_size_top',
    ];

    /**
     * @var array
     * Used only on PDP
     */
    public static $pdfLabelsList = [
        'medium',
        'treatment',
        'item_height',
        'item_width',
        'frame_width',
        //'frame_depth',
        'default_frame_depth',
        'liner_width',
        //'liner_depth',
        'default_liner_depth',
        'top_mat_size_bottom',
        'top_mat_size_left',
        'top_mat_size_right',
        'top_mat_size_top',
        'bottom_mat_size_bottom',
        'bottom_mat_size_left',
        'bottom_mat_size_right',
        'bottom_mat_size_top',
        'default_frame_color'
    ];
    /**
     * These fields can be blank.
     * Used in generic methods to add missing options
     * @var array
     */
    public $configFieldsBlank = [
        'medium',         //If it is not received / received blank then, Keep Blank.
        'treatment',      //If it is not received / received blank then, Keep Blank.
        //'frame_sku',      //If it is not received / received blank then, Keep Blank.
        'frame_default_sku',      //If it is not received / received blank then, Keep Blank.
        'liner_sku',      //If it is not received / received blank then, Keep Blank.
        //'top_mat_sku',    //If it is not received / received blank then, Keep Blank.
        //'bottom_mat_sku', //If it is not received / received blank then, Keep Blank.
        'top_mat_default_sku',    //If it is not received / received blank then, Keep Blank.
        'bottom_mat_default_sku', //If it is not received / received blank then, Keep Blank.
        'frame_width',      //If it is not received / received blank then, Keep Blank.
        'liner_width',      //If it is not received / received blank then, Keep Blank.
        'default_frame_depth', //If it is not received / received blank then, Keep Blank.
        'default_liner_depth', //If it is not received / received blank then, Keep Blank.
        'default_frame_color', //If it is not received / received blank then, Keep Blank.
        'default_liner_color', //If it is not received / received blank then, Keep Blank.
        'default_top_mat_color', //If it is not received / received blank then, Keep Blank.
        'default_bottom_mat_color', //If it is not received / received blank then, Keep Blank.
    ];

    /**
     * These fields needs to be pull from artwork product, if those are empty.
     * Used in generic methods to add missing options
     * @var array
     */
    public $configFieldsRetrieve = [
        'top_mat_size_bottom',    //If it is not received / received blank then, we can pull from art product
        'top_mat_size_left',      //If it is not received / received blank then, we can pull from art product
        'top_mat_size_right',     //If it is not received / received blank then, we can pull from art product
        'top_mat_size_top',       //If it is not received / received blank then, we can pull from art product
        'bottom_mat_size_bottom', //If it is not received / received blank then, we can pull from art product
        'bottom_mat_size_left',   //If it is not received / received blank then, we can pull from art product
        'bottom_mat_size_right',  //If it is not received / received blank then, we can pull from art product
        'bottom_mat_size_top',    //If it is not received / received blank then, we can pull from art product
    ];

    /**
     * These fields needs to be calculate, if those are empty.
     * Not used anywhere
     * @var array
     */
    public $configFieldsCalculate = [
        'image_width',  //If it is not received / received blank then, calculate it.
        'image_height', //If it is not received / received blank then, calculate it.
        'glass_width',  //If it is not received / received blank then, calculate it.
        'glass_height', //If it is not received / received blank then, calculate it.
        'item_width',   //If it is not received / blank then, we can pull image width and image height
        // from art product and calculate item and glass dimensions - This is only for CRM
        'item_height',  //If it is not received / blank then, we can pull image width and image height
        // from art product and calculate item and glass dimensions - This is only for CRM
    ];


    /**
     * Variable to display the Pz_cart_variables in an displayable format
     * Used for generic options display method
     */
    public static $expectedDataOrder = [
        'Medium' => 'Medium',
        'Treatment' => 'Treatment',
        'Size' => 'Size',
        'Frame' => ['Frame Color', 'Frame Width', 'Frame Depth'],
        'Top Mat' => ['Top Mat Color', 'Top Mat Size Left', 'Top Mat Size Top', 'Top Mat Size Right', 'Top Mat Size Bottom'],
        'Bottom Mat' => ['Bottom Mat Color', 'Bottom Mat Size Left', 'Bottom Mat Size Top', 'Bottom Mat Size Right', 'Bottom Mat Size Bottom'],
        'Liner' => ['Liner Color', 'Liner Width', 'Liner Depth'],
        'Artwork Color' => 'Artwork Color',
        'Side Mark' => 'Side Mark'
    ];
    /**
     * @var array
     */
    public static $defaultConfSizeLabel = [
        'item_height' => 'Item Height',
        'item_width' => 'Item Width'
    ];

    /**
     * @var array
     */
    public static $defaultConfFrameSizeLabel = [
        'default_frame_depth' => 'Frame Depth',
        'frame_width' => 'Frame Width'
    ];

    /**
     * @param Json $json
     * @param StoreManagerInterface $storeManager
     * @param CategoryListInterface $categoryList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Resolver $layerResolver
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param LoggerInterface $logger
     * @param ProductFactory $productFactory
     * @param Image $catalogHelper
     * @param ProductDetails $productDetails
     * @param ResourceConnection $resourceConnection
     * @param DriverInterface $driverFile
     * @param DirectoryList $directoryListObj
     * @param CustomerSession $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param Http $request
     * @param WendoverViewModel $wendoverViewModel
     * @param EncoderInterface $urlEncoder
     * @param Configurable $configurable
     * @param Attribute $eavAttribute
     * @param FreightEstimatorHelper $freightEstimatorHelper
     */
    public function __construct(
        private Context                         $context,
        private Json                            $json,
        private StoreManagerInterface           $storeManager,
        private CategoryListInterface           $categoryList,
        private SearchCriteriaBuilder           $searchCriteriaBuilder,
        private Resolver                        $layerResolver,
        private AttributeSetRepositoryInterface $attributeSetRepository,
        private LoggerInterface                 $logger,
        private ProductFactory                  $productFactory,
        private Image                           $catalogHelper,
        private ProductDetails                  $productDetails,
        private ResourceConnection              $resourceConnection,
        private DriverInterface                 $driverFile,
        private DirectoryList                   $directoryListObj,
        private CustomerSession                 $customerSession,
        private ProductRepositoryInterface      $productRepository,
        protected Http                          $request,
        private WendoverViewModel               $wendoverViewModel,
        EncoderInterface                        $urlEncoder,
        private Configurable                    $configurable,
        private Attribute                       $eavAttribute,
        protected readonly FreightEstimatorHelper $freightEstimatorHelper,
        private readonly ImageHelper              $imageHelper,
    )
    {
        parent::__construct($context);
        $this->urlEncoder = $urlEncoder;
    }

    /**
     * @param $jsonString
     * @return array|bool|float|int|mixed|null|string
     */
    public function unserializeData($jsonString)
    {
        return $this->json->unserialize($jsonString);
    }

    /**
     * @param $data
     * @return bool|false|string
     */
    public function serializeData($data)
    {
        return $this->json->serialize($data);
    }

    /**
     *
     */
    public function getCartProductAdditionalInformation($jsonObj)
    {
        $pzCartPropertiesJson = $this->getDefaultConfigurationValue(
            $jsonObj,
            '"pz_cart_properties":"',
            '}'
        );
        if (empty($pzCartPropertiesJson)) {
            return '';
        }
        $pzCartPropertiesJson = $pzCartPropertiesJson . '}';
        $pzCartPropertiesJson = str_replace('\\', '', $pzCartPropertiesJson);
        if (!empty($pzCartPropertiesJson)) {
            return $this->getDefaultConfigurationValidJson($pzCartPropertiesJson);
        } /**/
    }

    /**
     * @param $string
     * @param string $start
     * @param string $end
     * @return false|string
     */
    private function getDefaultConfigurationValue($string, $start = "", $end = "")
    {
        if (isset($string) && (strpos((string)$string, $start) === true)) {
            $startCharCount = strpos((string)$string, $start) + strlen($start);
            $firstSubStr = substr((string)$string, $startCharCount, strlen((string)$string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }

    /**
     * @param $defaultConf
     * @return array
     */
    public function getDefaultConfigurationValidJson($defaultConf)
    {
        $defaultSize = '';
        $jsonStr = '';
        $json = [];
        if ($defaultConf) {
            try {
                $dataArray = $this->json->unserialize($defaultConf);
                if (isset($dataArray['item_width']) && isset($dataArray['item_height'])) {
                    $defaultSize = $dataArray['item_width'] . '"w' . ' x ' . $dataArray['item_height'] . '"h';
                }
                foreach ($dataArray as $key => $value) {
                    if ($key != 'item_width' && $key != 'item_height') {
                        if (array_key_exists($key, self::$defaultConfLabel)) {
                            $json[self::$defaultConfLabel[$key]] = $value;
                        } else {
                            $key = ucfirst((string)$key);
                            $json[$key] = $value;
                        }
                    }
                }
                if ($defaultSize) {
                    $json['Size'] = $defaultSize;
                }
                if (count($json) > 0) {
                    $jsonStr = $this->json->serialize($json);
                }
            } catch (Exception $e) {
                $this->logger->debug($e->getMessage());
            }

        }

        return ['dataArray' => $json, 'jsonStr' => $jsonStr];

    }

    /**
     * @param string $defaultConf
     * @return array $defaultConfigurationAttributes
     */
    public function getDefaultConfigurationJson($defaultConf, $defaultConfigurationAttributes = null)
    {
        if ($defaultConfigurationAttributes === null) {
            $defaultConfigurationAttributes = self::$defaultConfLabel;
        }
        $defaultSize = '';
        $jsonStr = '';
        $json = [];
        if (!empty($defaultConf)) {
            try {
                $rawDataArray = $this->json->unserialize($defaultConf);
                $attributeValueArray = [];
                $attributeLabelArray = [];
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        $attributeValueArray[$key] = trim($attributeValue[0]);
                        if (!isset($attributeValue[1])) {
                            $attributeLabelArray[$key] = $defaultConfigurationAttributes[$key];
                        } else {
                            $attributeLabelArray[$key] = trim($attributeValue[1]);
                        }
                    } else {
                        $attributeValueArray[$key] = '';
                        $attributeLabelArray[$key] = $defaultConfigurationAttribute;
                    }
                }

                if (!empty($attributeValueArray) && !empty($attributeLabelArray)) {
                    $dataArray = $attributeValueArray;
                    foreach ($dataArray as $key => $value) {
                        if (!array_key_exists($key, self::$textOnlyOptions)) {
                            $json[$attributeLabelArray[$key]] = $value;
                        } else {
                            $json[$defaultConfigurationAttributes[$key]] = $value;
                        }
                    }

                    if (count($json) > 0) {
                        $jsonStr = $this->json->serialize($json);
                    }
                }
            } catch (Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
        return ['dataArray' => $json, 'jsonStr' => $jsonStr];
    }

    /**
     * @param $defaultConf
     * @return array
     */
    public function getDefaultConfigurationJsonLabelValuePair($defaultConf)
    {
        $attributeLabelValuePairArray = [];
        try {
            if (!empty($defaultConf)) {
                $defaultConfigurationAttributes = self::$defaultConfLabel;
                $rawDataArray = $this->json->unserialize($defaultConf);
                $attributeLabelValuePairArray = [];
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        if (!isset($attributeValue[1])) {
                            $attributeLabelValuePairArray[self::$defaultConfLabel[$key]] = trim($attributeValue[0]);
                        } else {
                            $attributeLabelValuePairArray[$attributeValue[1]] = trim($attributeValue[0]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $attributeLabelValuePairArray;
    }

    /**
     * @param $name
     * @param string $path
     * @return string
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function getSwatchImagePathForDefaultConf($name, $path = self::MEDIA_PATH)
    {
        $store = $this->storeManager->getStore();
        $imageUrl = $this->getDefaultPlaceholderImg();
        $customImgData = $this->getCustomImgData($name, $path);

        if (!empty($customImgData)) {
            $imageUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path . '/' . $customImgData['image'];

            $customImgVerFile = $this->directoryListObj->getPath(DirectoryList::MEDIA)
                . '/' . GetData::CUSTOM_IMAGE_VERSION_FILE;
            if (!empty($customImgVerFile) && $this->driverFile->isExists($customImgVerFile)) {
                $customImgVersion = trim((string)$this->driverFile->fileGetContents($customImgVerFile));
                $imageUrl = $imageUrl . '?' . $customImgVersion;
            }
        }
        return $imageUrl;
    }


    /**
     * get Custom Image Data from table
     *
     * @param string $sku
     * @param string $type
     * @return array
     */
    public function getCustomImgData($sku, $type)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $customImageData = [];
            $imgType = '';

            if ($type == self::MEDIA_PATH) {
                $imgType = 'default_configuration';
            } elseif ($type == self::MEDIA_PATH_PLP_PRODUCTS) {
                $imgType = 'specifications';
            }

            $select = $connection->select();
            $select->reset();

            $select->from(
                ['pcpi' => $this->resourceConnection->getTableName('perficient_custom_product_images')],
                '*'
            )->where(
                'sku = "' . $sku . '"'
            )->where(
                'type = "' . $imgType . '"'
            );
            $customImageData = $connection->fetchRow($select);
        } catch (Exception $e) {
            $this->logger->critical($e);
            return [];
        }

        return $customImageData;
    }

    /**
     * @return string
     */

    public function getProductBaseUrl()
    {
        $store = $this->storeManager->getStore();
        $imageUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::MEDIA_PATH_PLP_PRODUCTS;
        return $imageUrl;
    }

    /**
     * @param string $defaultConf
     * @param ?array $defaultConfigurationAttributes
     * @return array
     */
    public function getDefaultConfigurationForPDP($defaultConf, $defaultConfigurationAttributes = null)
    {
        if ($defaultConfigurationAttributes === null) {
            $defaultConfigurationAttributes = self::$defaultConfLabel;
        }

        $pdfLabels = $pdfSwatches = [];
        try {
            $itemWidth = $itemHeight = '';
            if (isset($defaultConf) && !empty($defaultConf)) {
                $rawDataArray = $this->json->unserialize($defaultConf);

                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        $varKey = (!isset($attributeValue[1]) ? $defaultConfigurationAttributes[$key] : trim($attributeValue[1]));
                        if (in_array($key, self::$pdpLabelsList)) {
                            if ($key == 'item_width') {
                                $itemWidth = $attributeValue[0];
                            } elseif ($key == 'item_height') {
                                $itemHeight = $attributeValue[0];
                            } else {
                                if (in_array($attributeValue[1], $this->skipLabelDirectDisplay()) && ($attributeValue[0] != " ")) {
                                    $pdfLabels[$varKey] = $attributeValue[0];
                                } else {
                                    if (!in_array($key, self::$pdpSwatchesList) && ($attributeValue[0] != "")) {
                                        $pdfLabels[$varKey] = $this->getDisplayName($attributeValue[0], $key);
                                    }
                                }
                            }
                        } elseif (in_array($key, self::$pdpSwatchesList)) {
                            if ($attributeValue[0] != " ") {
                                $pdfSwatches[$varKey] = $attributeValue[0];
                            }
                        }
                    }
                }
                if ($itemWidth != '' && $itemHeight != '') {
                    $pdfLabels['Size'] = $itemWidth . '"w' . ' x ' . $itemHeight . '"h';
                }
                return ['labels' => $pdfLabels, 'swatches' => $pdfSwatches];
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        if (!isset($pdfLabels['Size'])) {
            $pdfLabels['Size'] = $itemWidth . '"w' . ' x ' . $itemHeight . '"h';
        }
        return ['labels' => $pdfLabels, 'swatches' => $pdfSwatches];
    }

    public function frameDimension(ProductInterface $frameProduct): ?string
    {
        $defaultConf = $frameProduct->getData('default_configurations');
        if (empty($defaultConf)) {
            return null;
        }
        $processedDefaultConf = $this->getDefaultConfigurationForPDP($defaultConf);
        if (empty($processedDefaultConf) || !isset($processedDefaultConf['labels'])) {
            return null;
        }
        $labels = $processedDefaultConf['labels'];
        if (!empty($labels['Frame Width']) && !empty($labels['Frame Depth'])) {
            return sprintf('%s"w x %s"h', $labels['Frame Width'], $labels['Frame Depth']);
        }
        if (!empty($labels['Frame Width'])) {
            return sprintf('%s"w', $labels['Frame Width']);
        }
        if (!empty($labels['Frame Depth'])) {
            return sprintf('%s"h', $labels['Frame Depth']);
        }
        return null;
    }

    /**
     * @param $defaultConf
     * @return array
     */
    public function getDefaultConfigurationForPDF($defaultConf)
    {
        $pdfLabels = $pdfSwatches = [];
        try {
            $itemWidth = $itemHeight = '';
            if (isset($defaultConf) && !empty($defaultConf)) {
                $rawDataArray = $this->json->unserialize($defaultConf);
                $defaultConfigurationAttributes = self::$defaultConfLabel;
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        $varKey = (!isset($attributeValue[1]) ? self::$defaultConfLabel[$key] : trim($attributeValue[1]));
                        if (in_array($key, self::$pdfLabelsList)) {
                            if ($key == 'item_width') {
                                $itemWidth = $attributeValue[0];
                            } elseif ($key == 'item_height') {
                                $itemHeight = $attributeValue[0];
                            } elseif (in_array($key, ['default_frame_color']) && ($attributeValue[0] != " ")) {
                                $pdfLabels[$varKey] = $attributeValue[0];
                            } else {
                                if (in_array($attributeValue[1], $this->skipLabelDirectDisplay()) && ($attributeValue[0] != " ")) {
                                    $pdfLabels[$varKey] = $attributeValue[0];
                                } else {
                                    if (!in_array($key, self::$pdpSwatchesList) && ($attributeValue[0] != "")) {
                                        $pdfLabels[$varKey] = $this->getDisplayName($attributeValue[0], $key);
                                    }
                                }
                            }
                        } elseif (in_array($key, self::$pdpSwatchesList)) {
                            if ($attributeValue[0] != " ") {
                                $pdfSwatches[$varKey] = $attributeValue[0];
                            }
                        }
                    }
                }
                if ($itemWidth != '' && $itemHeight != '') {
                    $pdfLabels['Outward Dimension (W X H)'] = $itemWidth . '"w' . ' x ' . $itemHeight . '"h';
                }
                return ['labels' => $pdfLabels, 'swatches' => $pdfSwatches];
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        if (!isset($pdfLabels['Size'])) {
            $pdfLabels['Size'] = $itemWidth . '"w' . ' x ' . $itemHeight . '"h';
        }
        return ['labels' => $pdfLabels, 'swatches' => $pdfSwatches];
    }

    /**
     * @param $categoryIds
     * @return CategoryInterface[]|void
     */
    public function getProductCategories($categoryIds)
    {
        if (is_countable($categoryIds) ? count($categoryIds) : 0) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('entity_id', $categoryIds, 'in')
                ->create();
            return $this->categoryList->getList($searchCriteria)->getItems();
        }
        return;
    }

    /**
     * Method used to get current category.
     * This one is used to avoid using of Registry, as it is deprecated.
     *
     * @return Category
     */
    public function getCurrentCategory()
    {
        return $this->layerResolver->get()->getCurrentCategory();
    }

    /**
     * Get Attribute Set Name
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function isOnlyProductListPage($product)
    {
        $attributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());
        if ($attributeSet->getAttributeSetName() == self::FRAME_ATTR_SET ||
            $attributeSet->getAttributeSetName() == self::MAT_ATTR_SET) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Attribute Set Name
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function isFrameAttributeSet($product)
    {
        $attributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());
        return ($attributeSet->getAttributeSetName() == self::FRAME_ATTR_SET);
    }

    /**
     * Get Product Option Id by Option Label
     * @param $attributeCode
     * @param $optionLabel
     * @return string
     */
    public function getOptionIdByLabel($attributeCode, $optionLabel)
    {
        $product = $this->productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
        }
        return $optionId;
    }

    /**
     * Used only on PDP for skipping to be displayed as default available options for product
     * @return array
     */
    public function skipLabelDirectDisplay()
    {
        return [
            "Frame Width",
            "Frame Depth",
            "Liner Width",
            "Liner Depth",
            "Bottom Mat Size Bottom",
            "Bottom Mat Size Right",
            "Bottom Mat Size Left",
            "Bottom Mat Size Top",
            "Top Mat Size Bottom",
            "Top Mat Size Right",
            "Top Mat Size Left",
            "Top Mat Size Top",
            "Liner Color"
        ];
    }

    /**
     * @param $configJson
     * @return null|string|string[]
     */
    public function createValidJson($configJson)
    {
        try {
            if (isset($configJson) && !empty($configJson)) {
                $configJson = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim((string)$configJson));
                $rawDataArray = $this->json->unserialize($configJson);
                $defaultConfigurationAttributes = self::$defaultConfLabel;
                $attributeValueArray = [];
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        $attributeValueArray[$key] = trim($attributeValue[0]);
                    }
                }
                return $this->json->serialize($attributeValueArray);
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return false;
    }

    /**
     * @return string
     */
    public function getDefaultPlaceholderImg()
    {
        return $this->catalogHelper->getDefaultPlaceholderUrl('small_image');
    }

    /**
     * @param $childProduct
     * @return Image
     */
    public function thumpNailImage($childProduct)
    {
        return $this->catalogHelper->init($childProduct, 'product_page_image_small');
    }

    /**
     * @param $treatmentSku
     * @return string
     */
    public function getDisplayName($sku, $table)
    {
        return $this->productDetails->getDisplayName($sku, $table);
    }

    /**
     * @param $pzCartProperties
     * @return array
     */
    public function getValidCustomizedOptions($pzCartProperties, $sizeOnly = false, $expectedDataOrder = null)
    {
        if ($expectedDataOrder === null) {
            $expectedDataOrder = self::$expectedDataOrder;
        }
        $jsonStr = '';
        $validPzCartProperties = [];
        if ($pzCartProperties) {
            try {
                if ($sizeOnly) {
                    $sizeOnlyArr = ["Size" => "Size"];
                    $expectedSize = array_intersect_key($expectedDataOrder, $sizeOnlyArr);
                    $expectedDataOrder = $expectedSize;
                }
                $dataArray = $this->json->unserialize($pzCartProperties);

                foreach ($expectedDataOrder as $data => $values) {
                    switch ($data) {
                        case "Frame":
                        case "Liner":
                            $frameStringValue = $dataArray[$data];
                            if (!empty($frameStringValue)) {
                                $frameStringValue .= !empty($dataArray[$values[0]]) ? ", " . $dataArray[$values[0]] : "";
                                if (!empty($dataArray[$values[1]]) && !empty($dataArray[$values[2]])) {
                                    $defaultSize = $dataArray[$values[1]] . '"w' . ' x ' . $dataArray[$values[2]] . '"d';
                                    $frameStringValue .= ", " . $defaultSize;
                                }
                                $validPzCartProperties[$data] = $frameStringValue;
                            }
                            break;
                        case "Top Mat":
                        case "Bottom Mat":
                            $matStringValue = $dataArray[$data];
                            if (!empty($matStringValue)) {
                                $matStringValue .= !empty($dataArray[$values[0]]) ? ", " . $dataArray[$values[0]] : "";
                                if (!empty($dataArray[$values[1]]) && !empty($dataArray[$values[2]]) && !empty($dataArray[$values[3]]) && !empty($dataArray[$values[4]])) {
                                    if ($dataArray[$values[1]] == $dataArray[$values[2]] && $dataArray[$values[2]] == $dataArray[$values[3]] && $dataArray[$values[3]] == $dataArray[$values[4]]) {
                                        $defaultSize = $dataArray[$values[1]] . '"';
                                    } else {
                                        $defaultSize = "<span class='tooltip-container'><span class='hint' role='button' tabindex='0' data-toggle='dropdown' aria-haspopup='true' data-trigger-keypress-button='true' aria-expanded='false'><i class='fa fa-info-circle'></i>" . __('Weighted') . "</span><span class='pz-tooltip-content' role='dialog'><a tabindex='0' class='close-icon' aria-label='Close' data-action-keypress='true' role='button'></a>";
                                        $defaultSize .= 'Left: ' . $dataArray[$values[1]] . '", ' .
                                            'Right: ' . $dataArray[$values[2]] . '", ' .
                                            'Top: ' . $dataArray[$values[3]] . '", ' .
                                            'Bottom: ' . $dataArray[$values[4]] . '"';
                                        $defaultSize .= "</span></span>";

                                    }
                                    $matStringValue .= ", " . $defaultSize;

                                }
                                $validPzCartProperties[$data] = $matStringValue;
                            }
                            break;
                        case "Size":
                            if (!empty($dataArray['Item Width']) && !empty($dataArray['Item Height'])) {
                                $defaultSize = round((float)$dataArray['Item Width'], 2) . '"w' . ' x ' . round((float)$dataArray['Item Height'], 2) . '"h';
                                $validPzCartProperties[$values] = $defaultSize;
                            }
                            break;
                        default:
                            if (isset($dataArray[$data]) && !empty($dataArray[$data])) {
                                if ($data == "Medium") {
                                    $validPzCartProperties[$values] = $this->getDisplayName($dataArray[$data], 'medium');
                                } elseif ($data == "Treatment") {
                                    $validPzCartProperties[$values] = $this->getDisplayName($dataArray[$data], 'treatment');
                                } else {
                                    $validPzCartProperties[$values] = $dataArray[$data];
                                }
                            }
                    }

                }

                if (count($validPzCartProperties) > 0) {
                    $jsonStr = $this->json->serialize($validPzCartProperties);
                }
            } catch (Exception $e) {

                $this->logger->debug($e->getMessage());
            }
        }

        return ['dataArray' => $validPzCartProperties, 'jsonStr' => $jsonStr];
    }

    public function getCustomizedOptions(string $pzCartPropertiesStr, array $expectedData): array
    {
        $pzCartProperties = $this->unserializeData($pzCartPropertiesStr);
        if (empty($pzCartProperties)) {
            return [];
        }
        $result = [];
        foreach ($expectedData as $label => $value) {
            if (!empty($pzCartProperties[$label])) {
                $result[$label] = $pzCartProperties[$label];
            }
        }
        return $result;
    }

    /**
     * @param $infoBuyRequest
     * @return mixed|string
     */
    public function getPzCartProperties($infoBuyRequest)
    {
        $infoRequest = $this->json->unserialize($infoBuyRequest->getValue());
        $productConfig = '';
        if (isset($infoRequest['pz_cart_properties']) && $infoRequest['pz_cart_properties']) {
            $productConfig = $infoRequest['pz_cart_properties'];
        }
        return $productConfig;
    }

    /**
     * @param $defaultConf
     * @return array
     */
    public function getDefaultConfigurationSize($defaultConf)
    {
        $sizeLabels = [];
        try {
            if (isset($defaultConf) && !empty($defaultConf)) {
                $rawDataArray = $this->json->unserialize($defaultConf);
                $defaultConfigurationAttributes = self::$defaultConfSizeLabel;
                $itemWidth = $itemHeight = '';
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        if (in_array($key, array_keys(self::$defaultConfSizeLabel))) {
                            if ($key == 'item_width') {
                                $itemWidth = $attributeValue[0];
                            } elseif ($key == 'item_height') {
                                $itemHeight = $attributeValue[0];
                            } else {
                                // some other stuff can go here
                            }
                        }
                    }
                }
                if ($itemWidth != '' && $itemHeight != '') {
                    $sizeLabels['Size'] = $itemWidth . '"w' . ' x ' . $itemHeight . '"h';
                }
                return ['labels' => $sizeLabels];
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return ['labels' => $sizeLabels];
    }

    /**
     * Get attribute option value by option id
     *
     * @param $attributeCode
     * @param $optionId
     * @return string
     */
    public function getOptionLabelById($attributeCode, $optionId)
    {
        $optionLabel = '';
        //Start fix for WENDOVER-532 : Bloomreach API - Search by Color Name, Not ID
        if ($attributeCode == 'colors') {
            $attributeCode = 'color';
        }
        //End fix for WENDOVER-532 : Bloomreach API - Search by Color Name, Not ID
        $product = $this->productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute($attributeCode);

        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionLabel = $isAttributeExist->getSource()->getOptionText($optionId);
        }

        return $optionLabel;
    }

    /**
     * @param $infoBuyRequest
     * @return mixed
     */
    public function getIsQuickShipProduct($infoBuyRequest)
    {
        $infoRequest = $this->json->unserialize($infoBuyRequest->getValue());
        if (isset($infoRequest['quick_ship_product'])) {
            return $infoRequest['quick_ship_product'];
        }
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductImageUrl($product)
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
    }

    /**
     * Method used to check, if customer is logged-in or not.
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        $customerData = $this->customerSession->getCustomerData();
        if (!empty($customerData) && $customerData->getId() !== null) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getGuestPriceMultiplier()
    {
        $guestPriceMultiplier = $this->scopeConfig->getValue(
            self::GUEST_PRICE_MULTIPLIER_PATH,
            ScopeInterface::SCOPE_STORE
        );

        return $guestPriceMultiplier;
    }

    /**
     * Get product by SKU
     * @param $productSku
     * @return null
     */
    public function getProductBySku($productSku)
    {
        try {
            if ($productSku) {
                return $product = $this->productRepository->get($productSku);
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * Get frame stock AJAX Url
     * @return string
     */
    public function getFrameStockAjaxUrl()
    {
        try {
            return $this->_urlBuilder->getUrl(self::FRAME_STOCK_CHECK_URL);
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * Get frame stock data
     * @param $defaultFrame
     * @return null
     */
    public function getFrameStockData($defaultFrame)
    {
        try {
            $frameExtensionAttr = $defaultFrame->getExtensionAttributes();
            if ($frameExtensionAttr) {
                $frameStockData = $frameExtensionAttr->getStockItem();
                return $frameStockData;
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * Check frame is in stock or not
     * @param $frameStockData
     * @return null
     */
    public function isFrameInStock($frameStockData)
    {
        try {
            $frameQty = $frameStockData->getQty();
            if ($frameQty == 0) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * Get days to back in stock
     * @param $defaultFrame
     * @return null
     */
    public function getDaysToInStock($defaultFrame)
    {
        try {
            $daysToInStock = $defaultFrame->getData(self::FRAME_DAYS_TO_BACK_IN_STOCK);
            return $daysToInStock;
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * Get Notify URL
     * @param string $type
     * @param Int $productId
     * @param $currentPdpUrl
     * @return string
     */
    public function getNotifyUrl($type, $productId, $currentPdpUrl)
    {
        try {
            if ($type && $productId && $currentPdpUrl) {
                return $this->_getUrl(
                    'productalert/add/' . $type,
                    [
                        'product_id' => $productId,
                        ActionInterface::PARAM_NAME_URL_ENCODED =>
                            $this->urlEncoder->encode($currentPdpUrl)
                    ]
                );
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * Check stock alert enabled
     * Check whether stock alert is allowed
     * @return bool
     */
    public function isStockAlertEnabled()
    {
        try {
            return $this->scopeConfig->isSetFlag(
                self::XML_PATH_STOCK_ALLOW,
                ScopeInterface::SCOPE_STORE
            );
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return null;
    }

    /**
     * get product by id
     *
     * @param $productId
     * @return null
     */
    public function getProductById($productId)
    {
        try {
            return $product = $this->productRepository->getById($productId);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * get current page
     *
     * @return null
     */
    public function isPdpPage()
    {
        try {
            return $this->request->getFullActionName();
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @return WendoverViewModel
     */
    public function getWendoverViewModel()
    {
        return $this->wendoverViewModel;
    }

    /**
     * Get Attribute Value
     *
     * @param $product
     * @param $attrCode
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeValue($product, $attrCode)
    {
        $productFactory = $this->productFactory->create()->load($product->getId());
        return $productFactory->getResource()->getAttribute($attrCode)->getFrontend()->getValue($productFactory);

    }

    /**
     * Get Attribute Set Name
     *
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function isMatAttributeSet($product)
    {
        $attributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());
        return ($attributeSet->getAttributeSetName() == self::MAT_ATTR_SET);
    }

    /**
     * Get the parent Id
     *
     * @param $childId
     * @return Product|void
     */
    public function getParentId($childId)
    {
        /* for simple product of configurable product */
        $product = $this->configurable->getParentIdsByChild($childId);
        if (isset($product[0])) {
            $parent_id = $product[0];
            return $this->productFactory->create()->load($parent_id);
        }
    }

    /**
     * Get the Super attributes
     *
     * @param $childId
     * @return Product|void
     */
    public function getConfigurableAttribute($childId)
    {
        /* for simple product of configurable product */
        $product = $this->configurable->getParentIdsByChild($childId);
        if (isset($product[0])) {
            $parent_id = $product[0];
            $parentProduct = $this->productRepository->getById($parent_id);
            $productTypeInstance = $parentProduct->getTypeInstance();
            $productTypeInstance->setStoreFilter($parentProduct->getStoreId(), $parentProduct);
            $attributes = $productTypeInstance->getConfigurableAttributes($parentProduct);
            $superAttributeList = [];
            foreach ($attributes as $_attribute) {
                $attributeCode = $_attribute->getProductAttribute()->getAttributeCode();
                $superAttributeList[$_attribute->getAttributeId()] = $this->getAttributedValueId($childId, $attributeCode);
            }
            return $superAttributeList;
        }
    }

    /**
     * Get the getMirrorProductUrl
     *
     * @param int $productId
     * @return string
     */
    public function getMirrorProductUrl($productId): string
    {
        /* for append configurable attributes in product url */
        $parent = $this->getParentId($productId);
        $productUrl =$parent->getProductUrl().'#';
        $superAttribute = $this->getConfigurableAttribute($productId);
        foreach ($superAttribute as $key => $attribute)
        {
            $productUrl.= $key.'='.$attribute.'&';
        }
        return rtrim($productUrl,"&");
    }

    /**
     * To Check given product is child mirror product with parent
     *
     * @param ProductInterface $product
     * @return bool
     */
    public function isChildMirrorProduct(ProductInterface $product): bool
    {
        if ($this->mirrorAttributeSetId === null) {
            $this->mirrorAttributeSetId = $this->getMirrorAttributeSetId();
        }

        return $product->getTypeId() === Product\Type::TYPE_SIMPLE &&
            $this->isMirrorProduct($product) &&
            !empty($this->getParentId($product->getId()));
    }

    public function isMirrorProduct(ProductInterface $product): bool
    {
        if ($this->mirrorAttributeSetId === null) {
            $this->mirrorAttributeSetId = $this->getMirrorAttributeSetId();
        }
        return (int)$product->getAttributeSetId() === $this->mirrorAttributeSetId;
    }

    public function isConfigurableMirrorProduct(ProductInterface $product): bool
    {
        return $this->isMirrorProduct($product) && $product->getTypeId() === Configurable::TYPE_CODE;
    }

    /**
     * @return int
     */
    public function getMirrorAttributeSetId(): int
    {
        return $this->freightEstimatorHelper
            ->getAttributeSetIdByName(CreateCategoriesAttributeSets::MIRROR_ATTRIBUTESET_NAME);
    }

    /**
     * Loading the by ID
     *
     * @param $productId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function productLoadByID($productId){
       return $this->productRepository->getById($productId);
    }


    /**
     * Loading the by ID
     *
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeName($id){
        $attributeSet = $this->attributeSetRepository->get($id);
        $attributeName = '';
        if($attributeSet){
           $attributeName = $attributeSet->getAttributeSetName();
        }
        return $attributeName;

    }

    /**
     * Loading the by attribute
     *
     * @param $attribute
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributedId($attribute){

        return $this->eavAttribute->getIdByCode(
            Product::ENTITY,
            $attribute
        );
    }

    /**
     * Loading the by ID
     *
     * @param $product
     * @param $attrCode
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributedValueId($productId, $attrCode){
        $product = $this->productFactory->create()->load($productId);
        return $product->getData($attrCode);
    }

    /**
     * Loading the by Medium
     *
     * @param $sku
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMedium($sku)
    {
        $product = $this->productRepository->get($sku);
        return $product->getResource()->getAttribute('simplified_medium')->getFrontend()->getValue($product); // attribute name
    }

    public function configurableAttribute()
    {

        $_attributes = $this->configurableBlock->decorateArray($this->configurableBlock->getAllowAttributes());
        foreach ($_attributes as $_attribute)
        {
           echo $_attribute->getAttributeId();
        }


    }

    /**
     * Loading the by Frame color and size
     *
     * @param $sku
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFrameColorSize($sku)
    {
        $frame = $this->productRepository->get($sku);
        if($frame){
            $frameColor = $frame->getAttributeText('color_frame');
            $frameWidth = $frame->getData('frame_width');
            $frameDepth = $frame->getData('frame_depth');
            return  $frameColor.', '.$frameWidth.'"w x '.$frameDepth.'"d';
        }
        return null;

    }

    /**
     * Return Simple product name
     *
     * @param $product
     * @return string
     */
    public function getSimpleProductName($product) : string
    {
        if (empty($product)) {
            return '';
        }
        if ($simpleOption = $product->getCustomOption('simple_product')) {
            return $simpleOption->getProduct()->getName();
        }

        return $product->getName();
    }

    /**
     * Return Simple product URL
     *
     * @param ProductInterface|null $product
     * @return string
     */
    public function getSimpleProductURL(?ProductInterface $product) : string
    {
        if (empty($product)) {
            return '';
        }
        if ($simpleOption = $product->getCustomOption('simple_product')) {
            return $this->getMirrorProductUrl($simpleOption->getProduct()->getId());
        }

        return $product->getProductUrl();
    }
}
