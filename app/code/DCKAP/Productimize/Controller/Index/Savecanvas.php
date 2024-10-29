<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Productimize
 * @copyright  Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Productimize\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use DCKAP\Productimize\Helper\Data;
use DCKAP\Productimize\Model\ProductData;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Savecanvas
 * @package DCKAP\Productimize\Controller\Index
 */
class SaveCanvas extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_storeManager;
    protected $resultPageFactory;
    const MEDIA_FOLDER_PATH = 'productimize/savedcanvas/';
    const DIRECTORY_SEPARATOR = '/';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var json
     */
    private $json;


    /**
     * @var SerializerInterface
     */
    protected $serializer;
    protected $productimizeProductDataModel;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Savecanvas constructor.
     * @param Context $context
     * @param ProductcustomizerFactory $modelProductcustomizerFactory
     * @param JsonFactory $resultJsonFactory
     * @param Json $json
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
       private \DCKAP\Productimize\Helper\Data $helperData,
        Json $json,
        ProductData $productimizeProductDataModel,
        ResourceConnection $resourceConnection
    )
    {
        $this->_storeManager = $storeManager;
        $this->json = $json;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        //$this->helperData = $helperData;
        $this->serializer = $serializer;
        $this->productimizeProductDataModel = $productimizeProductDataModel;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    public function execute()
    {
        $requestUrl = "";
        $requestData = "";
        $responseJson = "";
        $randomFolder = $this->_storeManager->getStore()->getBaseMediaDir() .self::DIRECTORY_SEPARATOR. self::MEDIA_FOLDER_PATH;
        $uploadDir = $randomFolder;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $randFolder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $randomFolder = $this->_storeManager->getStore()->getBaseMediaDir() .self::DIRECTORY_SEPARATOR. self::MEDIA_FOLDER_PATH;
        if (!file_exists($randomFolder)) {
            mkdir($randomFolder, 0777, true);
        }

        $msg = '';

        $productArtworkDataJson = $this->getRequest()->getParam('artworkData');
        $productArtworkData = json_decode($productArtworkDataJson,true);


        $pzSelectedOptions = $productArtworkData['pzSelectedOptions'];
        $glassDimention = $productArtworkData['glassDimention'];
        $designedImageNameParams = [];

        if(isset($pzSelectedOptions['medium']) && $pzSelectedOptions['medium']['sku'])   {
            $designedImageNameParams['medium'] = $pzSelectedOptions['medium']['sku'];
        }
        if(isset($pzSelectedOptions['treatment']) && $pzSelectedOptions['treatment']['sku'])   {
            $designedImageNameParams['treatment'] = $pzSelectedOptions['treatment']['sku'];
        }
        if(isset($pzSelectedOptions['bottomMat']) && $pzSelectedOptions['bottomMat']['sku'])   {
            $designedImageNameParams['bottomMat'] = $pzSelectedOptions['bottomMat']['sku'];
            $designedImageNameParams['bottomMatLeft'] = (isset($pzSelectedOptions['bottomMat']['width']) && isset($pzSelectedOptions['bottomMat']['width']['left'])) ? $pzSelectedOptions['bottomMat']['width']['left'] : '';
            $designedImageNameParams['bottomMatRight'] = (isset($pzSelectedOptions['bottomMat']['width']) && isset($pzSelectedOptions['bottomMat']['width']['right'])) ? $pzSelectedOptions['bottomMat']['width']['right'] : '';
            $designedImageNameParams['bottomMatTop'] = (isset($pzSelectedOptions['bottomMat']['width']) && isset($pzSelectedOptions['bottomMat']['width']['top'])) ? $pzSelectedOptions['bottomMat']['width']['top'] : '';
            $designedImageNameParams['bottomMatBottom'] = (isset($pzSelectedOptions['bottomMat']['width']) && isset($pzSelectedOptions['bottomMat']['width']['bottom'])) ? $pzSelectedOptions['bottomMat']['width']['bottom'] : '';
        }

        if(isset($pzSelectedOptions['topMat']) && $pzSelectedOptions['topMat']['sku'] )  {
            $designedImageNameParams['topMat'] = $pzSelectedOptions['topMat']['sku'];
            $designedImageNameParams['topMatLeft'] = (isset($pzSelectedOptions['topMat']['width']) && isset($pzSelectedOptions['topMat']['width']['left'])) ? $pzSelectedOptions['topMat']['width']['left'] : '';
            $designedImageNameParams['topMatRight'] = (isset($pzSelectedOptions['topMat']['width']) && isset($pzSelectedOptions['topMat']['width']['right']) ) ? $pzSelectedOptions['topMat']['width']['right'] : '';
            $designedImageNameParams['topMatTop'] = ( isset($pzSelectedOptions['topMat']['width']) && isset($pzSelectedOptions['topMat']['width']['top'])) ? $pzSelectedOptions['topMat']['width']['top'] : '';
            $designedImageNameParams['topMatBottom'] = ( isset($pzSelectedOptions['topMat']['width']) && isset($pzSelectedOptions['topMat']['width']['bottom'])) ? $pzSelectedOptions['topMat']['width']['bottom'] : '';
        }
        if(isset($pzSelectedOptions['frame']) && $pzSelectedOptions['frame']['sku'])   {
            $designedImageNameParams['frame'] = $pzSelectedOptions['frame']['sku'];
        }
        if(isset($pzSelectedOptions['liner']) && $pzSelectedOptions['liner']['sku'])   {
            $designedImageNameParams['liner'] = $pzSelectedOptions['liner']['sku'];
        }
        $designedImageNameParams['width'] = isset($glassDimention[0]) ? $glassDimention[0] : 15;
        $designedImageNameParams['height'] = isset($glassDimention[1]) ? $glassDimention[1] : 15;
        $designedImageNameParams['productId'] = $productArtworkData['productId'];


        $artworkNameInfo = $this->helperData->productArtworkName($designedImageNameParams);

        if($artworkNameInfo['rowCount'] == 1)   {
            $productImg = $artworkNameInfo['imgURL'];
            $msg = 'Image exists.';
        }   else {

            $artworkData = [];

            $artworkData['imgWidth'] = isset($glassDimention[0]) ? $glassDimention[0] : 15;
            $artworkData['imgHeight'] = isset($glassDimention[1]) ? $glassDimention[1] : 15;
            $productData = array(
                $productArtworkData['productId'] => $productArtworkData['productUrl']
            );
            $artworkData['savedFileName'] = $artworkNameInfo['name'];

            foreach($pzSelectedOptions as $pzSelectedOptionskey=> $pzSelectedOptionsval)   {
                $data = $pzSelectedOptionsval;
                if ($pzSelectedOptionskey == 'treatment') {
                    $images = $this->helperData->getTreatmentData($data['sku'], Data::$rendererFrameCustomImageType);


                    if (isset($images) && isset($images['lengthImage']) && $images['lengthImage'] != "no_selection") {
                        $artworkData['treatment'] = [
                            'sideImg' => $images['lengthImage'],
                            "width" => isset($images['width']) ? (float)$images['width'] : 0.2,
                        ];
                    }
                }
                else if($pzSelectedOptionskey == 'frame' && isset($data['sku']) && !empty($data['sku']) && strpos(strtolower($data['sku']), 'no ') === false)    {
                    $images = $this->helperData->getProductImagesBySku($data['sku'], Data::$rendererFrameCustomImageType);
                    if (isset($images) && isset($images['lengthImage']) && isset($images['cornerImage'])) {
                        $artworkData[$pzSelectedOptionskey] = array(
                            'width' => isset($data['width']) ? $data['width'] : 0,
                            'sideImg' => $images['lengthImage'],
                            'cornerImg' => $images['cornerImage']
                        );
                    }
                }

                else if($pzSelectedOptionskey == 'liner' && isset($data['sku'])  && !empty($data['sku']) && strpos(strtolower($data['sku']), 'no ') === false)    {
                    $images = $this->helperData->getProductImagesBySku($data['sku'], Data::$rendererFrameCustomImageType);
                    if (isset($images) && isset($images['lengthImage']) && isset($images['cornerImage'])) {
                        $artworkData[$pzSelectedOptionskey] = array(
                            'width' => isset($data['width']) ? $data['width'] : 0,
                            'sideImg' => $images['lengthImage'],
                            'cornerImg' => $images['cornerImage']
                        );
                    }
                }
                else if($pzSelectedOptionskey == 'topMat' && isset($data['sku']) && !empty($data['sku']) && strpos(strtolower($data['sku']), 'no ') === false)  {
                    $images = $this->helperData->getProductImagesBySku($data['sku'], Data::$rendererMatCustomImageType);
                    if (isset($images) && isset($images['rendererImage'])) {
                        $artworkData[$pzSelectedOptionskey] = array(
                            'width' => '',
                            'sideImg' => $images['rendererImage']
                        );
                    }

                }
                else if($pzSelectedOptionskey == 'bottomMat' && isset($data['sku']) && !empty($data['sku']) && strpos(strtolower($data['sku']), 'no ') === false)  {
                    $images = $this->helperData->getProductImagesBySku($data['sku'], Data::$rendererMatCustomImageType);
                    if (isset($images) && isset($images['rendererImage'])) {
                        $artworkData[$pzSelectedOptionskey] = array(
                            'width' => '',
                            'sideImg' => $images['rendererImage']
                        );
                    }

                }
                if ($pzSelectedOptionskey == 'topMat' && isset($data['sku'])  && !empty($data['sku']) && strpos(strtolower($data['sku']), 'no ') === false  && isset($artworkData['topMat'])) {
                    if (isset($data['width'])) {
                        $dataWidth = $data['width'];
                        if (!isset($dataWidth['left'])) {
                            $artworkData[$pzSelectedOptionskey]['width'] = array(
                                'left'=> (float) $dataWidth,
                                'top'=> (float) $dataWidth,
                                'right'=> (float) $dataWidth,
                                'bottom'=> (float) $dataWidth
                            );
                        }
                        else {
                            $artworkData[$pzSelectedOptionskey]['width'] = array(
                                'left' => isset($dataWidth['left']) ? (float)$dataWidth['left'] : 0,
                                'top' => isset($dataWidth['top']) ? (float)$dataWidth['top'] : 0,
                                'right' => isset($dataWidth['right']) ? (float)$dataWidth['right'] : 0,
                                'bottom' => isset($dataWidth['bottom']) ? (float)$dataWidth['bottom'] : 0
                            );
                        }

                    }
                }
                if ($pzSelectedOptionskey == 'bottomMat' && isset($data['sku']) && !empty($data['sku']) && strpos(strtolower($data['sku']), 'no ') === false  && isset($artworkData['bottomMat'])) {
                    if (isset($data['width'])) {
                        $dataWidth = $data['width'];
                        if(isset($pzSelectedOptions['topMat']) && isset($pzSelectedOptions['topMat']['width'])) {
                            $topMatWidth = $pzSelectedOptions['topMat']['width'];
                            if (isset($topMatWidth['left'])) {
                                $topMatLeft = isset($topMatWidth['left']) ? (float)$topMatWidth['left'] : 0;
                                $topMatTop = isset($topMatWidth['top']) ? (float)$topMatWidth['top'] : 0;
                                $topMatRight = isset($topMatWidth['right']) ? (float)$topMatWidth['right'] : 0;
                                $topMatBottom = isset($topMatWidth['bottom']) ? (float)$topMatWidth['bottom'] : 0;
                            }
                            else {
                                $topMatLeft = (float) $topMatWidth;
                                $topMatTop = (float) $topMatWidth;
                                $topMatRight = (float) $topMatWidth;
                                $topMatBottom = (float) $topMatWidth;
                            }

                            $artworkData[$pzSelectedOptionskey]['width'] = array(
                                'left'=>  (isset($dataWidth['left']) && ( (float) $dataWidth['left'] - $topMatLeft) > 0) ? (float) ($dataWidth['left'] - $topMatLeft) : 0,
                                'top'=> (isset($dataWidth['top']) && ( (float) $dataWidth['top'] - $topMatTop) > 0) ? (float)($dataWidth['top'] - $topMatTop) : 0,
                                'right'=> (isset($dataWidth['right']) && ( (float) $dataWidth['right'] - $topMatRight) > 0) ? (float)($dataWidth['right'] - $topMatRight) : 0 ,
                                'bottom'=>  (isset($dataWidth['bottom']) && ( (float) $dataWidth['bottom'] - $topMatBottom) > 0) ? (float)($dataWidth['bottom'] - $topMatBottom) : 0
                            );
                        }
                        else {
                            $artworkData[$pzSelectedOptionskey]['width'] = array(
                                'left'=> isset($dataWidth['left']) ? (float) $dataWidth['left'] : (((float) $dataWidth > 0) ? (float) $dataWidth : 0),
                                'top'=> isset($dataWidth['top']) ?  (float)  $dataWidth['top']  : (((float) $dataWidth > 0) ? (float) $dataWidth : 0),
                                'right'=> isset($dataWidth['right']) ?  (float) $dataWidth['right']  : (((float) $dataWidth > 0) ? (float) $dataWidth : 0),
                                'bottom'=> isset($dataWidth['bottom']) ?  (float) $dataWidth['bottom']  : (((float) $dataWidth > 0) ? (float) $dataWidth : 0),
                            );
                        }
                    }
                }
            }


            $responseData = $this->helperData->generateImgInNode($productData, $artworkData, 1) ;

            try {
                if ($responseData['status'] == 1) {
                    $jsonproductImg = $this->helperData->getUnserializeData($responseData['response']);
                    $productImg = $jsonproductImg[$productArtworkData['productId']];
                    $this->helperData->setproductArtworkNameDB($artworkNameInfo);
                    $msg = 'Image created';
                    $responseJson = $responseData['response'];
                } else {
                    $productImg = "";
                    $responseJson = $responseData['response'];
                }
            }
            catch (\Exception $e) {
                $productImg = "";
                $responseJson = $e->getMessage();
                $requestData = json_encode($responseData, 1);
            }
            if (empty($productImg)) {
                try {
                    $productImages = $this->helperData->getProductImagesById($productArtworkData['productId'], ['base' => 'baseImg']);
                    if ($productImages && count($productImages) > 0 && isset($productImages['baseImg'])) {
                        $productImg = $productImages['baseImg'];
                        $msg = 'Base image is sent!';
                    }
                }
                catch (\Exception $e) {
                    $productImg = "";
                }
                if (empty($productImg)) {
                    $productImg = $productArtworkData['productUrl'];
                    $msg = 'Base image is not there. Product cropped images is sent!';
                }
            }
        }

        $result = array(
            'imageUrl' => $productImg,
            'msg' => $msg,
            'requestUrl' => $requestUrl,
            'requestData' => $requestData,
            'response' => $responseJson
        );
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($result);
        return $resultJson;
    }


}
