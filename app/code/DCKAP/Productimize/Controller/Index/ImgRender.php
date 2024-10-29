<?php

namespace DCKAP\Productimize\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use DCKAP\Productimize\Helper\Data AS ProductimizeHelper;
use DCKAP\Productimize\Model\ProductData;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class ImgRender extends Action
{

    /**
     * @var Curl
     */
    protected $curl;
    protected $apiUrl = "https://devnode.perficientdcsdemo.com/";
    protected $scopeConfig;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Curl $curl,
        StoreManagerInterface $storeManager,
        ProductimizeHelper $productimizeHelper,
        ProductData $productimizeProductDataModel,
        ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->curl = $curl;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->productimizeHelper = $productimizeHelper;
        $this->scopeConfig = $scopeConfig;
    }

    public function getApiUrl()
    {
        $this->apiUrl = $this->productimizeHelper->getGeneralConfigByCode('productimize/general/productimize_generate_image_nodejs_url');
        return $this->apiUrl;
    }

    public function execute()
    {
        $enableLog = $this->productimizeHelper->getGeneralConfigByCode('productimize/general/productimize_enable_log');
        $dckapProductImage = $this->productimizeHelper->getGeneralConfigByCode('productimize/general/productimize_dckap_server_product_image');

        $getParams = $this->getRequest()->getParams();
        $paramSkus = array();
        $isImgGenerationFailed = 0;
        if (isset($getParams) && isset($getParams['art_sku']) && strtolower($getParams['art_sku']) != "none" ) {
            array_push($paramSkus, $getParams['art_sku']);
        }
        if (isset($getParams) && isset($getParams['frame_sku']) && isset($getParams['frame_sku']) && strtolower($getParams['frame_sku']) != "none") {
            array_push($paramSkus, $getParams['frame_sku']);
        }
        if (isset($getParams) && isset($getParams['top_mat_sku']) && isset($getParams['top_mat_sku']) && strtolower($getParams['top_mat_sku']) != "none") {
            array_push($paramSkus, $getParams['top_mat_sku']);
        }
        if (isset($getParams) && isset($getParams['bottom_mat_sku']) && isset($getParams['bottom_mat_sku']) && strtolower($getParams['bottom_mat_sku']) != "none") {
            array_push($paramSkus, $getParams['bottom_mat_sku']);
        }
        if (isset($getParams) && isset($getParams['liner_sku'])) {
            array_push($paramSkus, $getParams['liner_sku']);
        }


        if (is_array($paramSkus) && count($paramSkus) > 0) {
            $store = $this->storeManager->getStore();
            $bottomMatData = array();
            $topMatData = array();
            $artworkData = array();
            // Use factory to create a new product collection
            $pc = $this->collectionFactory->create();
            /** Apply filters here */
            $productCollBySku = $pc->addAttributeToSelect('*')->addAttributeToFilter('sku', array('in' => $paramSkus));
            $loopInc = 0;
            $defaultConf = "";
            foreach ($productCollBySku as $product) {
                $loopInc++;
                if (isset($getParams['art_sku']) && ($product->getSku() == $getParams['art_sku'])) {
                    $artworkData['defaultConf'] = $product->getDefaultConfigurations();
                    $defaultConf = isset($artworkData['defaultConf']) ? json_decode($artworkData['defaultConf'], 1) : "";
                    if ($defaultConf && isset($defaultConf['frame_width'])) {
                        $explodedString = explode(":", $defaultConf['frame_width']);
                        $artworkData['frameWidth'] = $explodedString[0];
                    }
                    if ($defaultConf && isset($defaultConf['glass_width'])) {
                        $explodedString = explode(":", $defaultConf['glass_width']);
                        $artworkData['image_width'] = $explodedString[0];
                    }
                    else {
                        $artworkData['image_width'] = $product->getImageWidth();
                    }
                    if ($defaultConf && isset($defaultConf['glass_height'])) {
                        $explodedString = explode(":", $defaultConf['glass_height']);
                        $artworkData['image_height'] = $explodedString[0];
                    }
                    else {
                        $artworkData['image_height'] = $product->getImageHeight();
                    }


                    if (isset($getParams['image_edge_treatment']) && !empty($getParams['image_edge_treatment']) && strtolower($getParams['image_edge_treatment']) != "none") {
                        if($defaultConf && isset($defaultConf['treatment'])) {
                            $explodedString = explode(":", $defaultConf['treatment']);
                            $treatmentSku = $explodedString[0];
                            if (!empty($treatmentSku)) {
                                $treatmentData = $this->productimizeHelper->getTreatmentData($treatmentSku, ProductimizeHelper::$rendererFrameCustomImageType);
                                if (isset($treatmentData) && isset($treatmentData['lengthImage'])) {
                                    $artworkData['treatment'] = [
                                        'lengthImage' => $treatmentData['lengthImage'],
                                        "width" => isset($treatmentData['width']) ? (float)$treatmentData['width'] : 0.2,
                                    ];
                                }
                            }
                        }
                    }

                    $productImageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
                    $fallbackImage = $productImageUrl;

                    $artworkData['top_mat_size_left'] = $product->getTopMatSizeLeft();
                    $artworkData['top_mat_size_right'] = $product->getTopMatSizeRight();
                    $artworkData['top_mat_size_top'] = $product->getTopMatSizeTop();
                    $artworkData['top_mat_size_bottom'] = $product->getTopMatSizeBottom();
                    $artworkData['bottom_mat_size_left'] = $product->getBottomMatSizeLeft();
                    $artworkData['bottom_mat_size_right'] = $product->getBottomMatSizeRight();
                    $artworkData['bottom_mat_size_top'] = $product->getBottomMatSizeTop();
                    $artworkData['bottom_mat_size_bottom'] = $product->getBottomMatSizeBottom();
                    $artworkData['max_image_height'] = $product->getMaxImageHeight();
                    $artworkData['max_image_width'] = $product->getMaxImageWidth();
                    $artworkData['image'] = $productImageUrl;
                    $pzDefaultConf = $artworkData;
                    if (isset($getParams['top_mat_sku']) && strtolower($getParams['top_mat_sku']) != "none") {
                        $topMatData['left'] = ((int)$pzDefaultConf['top_mat_size_left'] > 0) ? ((float)$pzDefaultConf['top_mat_size_left']) : 0;
                        $topMatData['right'] = ((int)$pzDefaultConf['top_mat_size_right'] > 0) ? ((float)$pzDefaultConf['top_mat_size_right']) : 0;
                        $topMatData['top'] = ((int)$pzDefaultConf['top_mat_size_top'] > 0) ? ((float)$pzDefaultConf['top_mat_size_top']) : 0;
                        $topMatData['bottom'] = ((int)$pzDefaultConf['top_mat_size_bottom'] > 0) ? ((float)$pzDefaultConf['top_mat_size_bottom']) : 0;
                    }
                    if (isset($getParams['bottom_mat_sku']) && strtolower($getParams['bottom_mat_sku']) != "none" ) {
                        $bottomMatData['left'] = ((int)$pzDefaultConf['bottom_mat_size_left'] > 0) ? ((float)$pzDefaultConf['bottom_mat_size_left']) : 0;
                        $bottomMatData['right'] = ((int)$pzDefaultConf['bottom_mat_size_right'] > 0) ? ((float)$pzDefaultConf['bottom_mat_size_right']) : 0;
                        $bottomMatData['top'] = ((int)$pzDefaultConf['bottom_mat_size_top'] > 0) ? ((float)$pzDefaultConf['bottom_mat_size_top']) : 0;
                        $bottomMatData['bottom'] = ((int)$pzDefaultConf['bottom_mat_size_bottom'] > 0) ? ((float)$pzDefaultConf['bottom_mat_size_bottom']) : 0;
                        if (isset($topMatData)) {
                            $bottomMatData['left'] = (float)($bottomMatData['left']) - (float)($pzDefaultConf['top_mat_size_left']);
                            $bottomMatData['right'] = (float)($bottomMatData['right']) - (float)($pzDefaultConf['top_mat_size_right']);
                            $bottomMatData['top'] = (float)($bottomMatData['top']) - (float)($pzDefaultConf['top_mat_size_top']);
                            $bottomMatData['bottom'] = (float)($bottomMatData['bottom']) - (float)($pzDefaultConf['top_mat_size_bottom']);
                        }
                    }
                    $croppedImg = $product->getResource()->getAttribute('cropped')->getFrontend()->getValue($product);
                    if ($croppedImg && $croppedImg != "no_selection") {
                        $artworkData['croppedImg'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $croppedImg;
                    }

                    $waterMarkImageConf = $this->productimizeHelper->getWatermarkImageDataByType('image');
                    if ($waterMarkImageConf && count($waterMarkImageConf) > 0) {
                        $artworkData['watermarkImageConf'] = $waterMarkImageConf;
                    }
                }
                else if (isset($getParams['frame_sku']) && strtolower($getParams['frame_sku']) != "none" && ($product->getSku() == $getParams['frame_sku'])) {
                    $artworkData['frameImages'] = $this->productimizeHelper->getProductImagesByProduct($product , ProductimizeHelper::$rendererFrameCustomImageType);
                    $artworkData['frameWidth'] = $product->getFrameWidth();
                }
                else if (isset($getParams['top_mat_sku']) && strtolower($getParams['top_mat_sku']) != "none" && ($product->getSku() == $getParams['top_mat_sku'])) {
                    $artworkData['topMatImages'] = $this->productimizeHelper->getProductImagesByProduct($product, ProductimizeHelper::$rendererMatCustomImageType);
                }
                else if (isset($getParams['bottom_mat_sku']) && strtolower($getParams['bottom_mat_sku']) != "none" && ($product->getSku() == $getParams['bottom_mat_sku'])) {
                    $artworkData['bottomMatImages'] = $this->productimizeHelper->getProductImagesByProduct($product , ProductimizeHelper::$rendererMatCustomImageType);
                }
            }


            $pzDefaultConf = $artworkData;

            if ($loopInc == count($productCollBySku)) {
                $URL = $this->getApiUrl();
                if (isset($artworkData) && (!isset($artworkData['frameWidth']))) {
                    $artworkData['frameWidth'] = 1;
                }
                $fields = [];
                $fields['data'] = [];

                if (isset($artworkData) && isset($pzDefaultConf['image_width']) && $pzDefaultConf['image_width'] > 0) {

                    $mainUrl = isset($artworkData['croppedImg']) ? $artworkData['croppedImg'] : (isset($artworkData['image']) ? $artworkData['image'] : "");

                    $fallbackImage = $mainUrl;

                    if (isset($enableLog) && isset($dckapProductImage)) {
                        $mainUrl = 'https://wendover.dckap.co/uat/pub/media/catalog/product/r/e/renderer_11522_1_1.png';
                    }

                    $fields['data']['image'] = [
                        "url" => $mainUrl,
                        "dimension" => ["x" => (int)$artworkData['image_width'], "y" => (int)$artworkData['image_height']]
                    ];

                    if (isset($artworkData['watermarkImageConf'])) {
                        $fields['data']['watermark'] = [
                            "url" => $artworkData['watermarkImageConf']['url'],
                            "position" => isset($artworkData['watermarkImageConf']['position']) ? $artworkData['watermarkImageConf']['position'] : 'center',
                            "opacity" => isset($artworkData['watermarkImageConf']['opacity']) ? $artworkData['watermarkImageConf']['opacity'] : 100,
                            "dimension" => [
                                "x" => isset($artworkData['watermarkImageConf']['width']) ? ( (float)$artworkData['watermarkImageConf']['width']) : 100,
                                "y" => isset($artworkData['watermarkImageConf']['height']) ? ((float) $artworkData['watermarkImageConf']['height'] ): 100

                            ]
                        ];
                    }

                }
                if (isset($artworkData) && (isset($artworkData['treatment']) && isset($artworkData['treatment']['lengthImage']) && !empty($artworkData['treatment']['lengthImage']))) {

                    $fields['data']['treatment'] = [
                        'url' => $artworkData['treatment']['lengthImage'],
                        "width" => isset($artworkData['treatment']['width']) ? (float)$artworkData['treatment']['width'] : 0.2,
                    ];
                }
                if (isset($artworkData) && isset($getParams['frame_sku']) && strtolower($getParams['frame_sku']) != "none" && (isset($artworkData['frameWidth']) && $artworkData['frameWidth'] > 0)) {
                    if (isset($artworkData['frameImages']) && isset($artworkData['frameImages']['lengthImage']) && isset($artworkData['frameImages']['cornerImage'])) {
                    $fields['data']['frame'] = [
                        "cornerImage" => $artworkData['frameImages']['cornerImage'],
                        "sideImage" => $artworkData['frameImages']['lengthImage'],
                        "width" => (float)$artworkData['frameWidth']
                    ];
                    }
                }
                if (isset($topMatData) && (isset($topMatData['left']) && $topMatData['top'] > 0)) {
                    if (isset($artworkData['topMatImages']) && isset($artworkData['topMatImages']['rendererImage'])) {
                        $fields['data']['topMat'] = [
                            "sideImage" => $artworkData['topMatImages']['rendererImage'],
                            "width" => ["left" => $topMatData['left'], "right" => $topMatData['right'], "top" => $topMatData['top'], "bottom" => $topMatData['bottom']]
                        ];
                    }
                }
                if (isset($bottomMatData) && (isset($bottomMatData['left']) && $bottomMatData['top'] > 0)) {
                    if (isset($artworkData['bottomMatImages']) && isset($artworkData['bottomMatImages']['rendererImage'])) {
                        $fields['data']['bottomMat'] = [
                            "sideImage" => $artworkData['bottomMatImages']['rendererImage'],
                            "width" => ["left" => $bottomMatData['left'], "right" => $bottomMatData['right'], "top" => $bottomMatData['top'], "bottom" => $bottomMatData['bottom']]
                        ];
                    }
                }
                $fields["format"] = "png";


                if (isset($enableLog)) {
                    echo " Params ";
                    echo "<pre>"; print_r($artworkData); echo "</pre>";

                    echo " Params ";
                    echo " Node url " . $URL;
                    echo "<pre>"; print_r($fields); echo "</pre>";
                }

                $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($fields));
                $this->curl->addHeader("Content-Type", "application/json");
                $this->curl->addHeader("Access-Control-Allow-Origin", $URL);
                $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
                $this->curl->setOption(CURLOPT_CONNECTTIMEOUT, 3);
                $this->curl->setOption(CURLOPT_TIMEOUT, 60);
                $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);

                $res = $this->curl->post($URL, json_encode($fields));




                if (isset($enableLog)) {
                    echo "<br/>";
                    echo " STATUS AND RESPONSE ";
                    echo "<pre>"; print_r($this->curl->getStatus()); echo "</pre>";
                    echo "<pre>"; print_r($this->curl->getBody()); echo "</pre>";
                    echo "FALBACK IMAGE " . $fallbackImage;
                    exit;
                }

                if ($this->curl->getStatus() == 100 || $this->curl->getStatus() == 200) {
                    $response = $this->curl->getBody();
                    if (!preg_match('/data:([^;]*);base64,(.*)/', $response, $matches)) {
                        $isImgGenerationFailed = 1;
                    }
                    else {
                        $content = base64_decode($matches[2]);
                        header('Content-Type: ' . $matches[1]);
                        header('Content-Length: ' . strlen($content));
                        header("Access-Control-Allow-Origin: " . $URL);
                        echo $content;
                        die;
                    }
                } else {
                    $isImgGenerationFailed = 1;
                }
            } else {
                $isImgGenerationFailed = 1;
            }
            if ($isImgGenerationFailed == 1) {
                if (!empty($fallbackImage)) {
                    try {
                        if(preg_match('/\.(jpg|png|jpeg|JPG|JPEG|PNG)$/', $fallbackImage, $matches)) {
                            $content = file_get_contents($fallbackImage);
                            header('Content-Type: ' . 'image/'.$matches[1]);
                            header('Content-Length: ' . strlen($content));
                            echo $content;
                            die;
                        }
                        else {
                            echo '<img src="'.$fallbackImage.'"/>';
                        }
                    }
                    catch (\Exception $e) {
                        echo '<img src="'.$fallbackImage.'"/>';
                    }
                    die;
                }
                return false;
            }
        } else {
            return false;
        }
    }
}
