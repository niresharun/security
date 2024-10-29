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
use Magento\Catalog\Model\Product;
use DCKAP\Productimize\Model\ProductimizeCalculation;
use DCKAP\Productimize\Helper\Data;
use Magento\Framework\App\ResourceConnection;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Catalog\Model\ProductRepository;
use Perficient\Productimize\Model\ProductConfiguredPrice;

/**
 * Class Index
 * @package DCKAP\Productimize\Controller\Index
 */
class Index extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    protected $resultFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var productRepository
     */
    protected $productRepository;

    /**
     * @var helperData
     */
    protected $helperData;

    /**
     * @var perficientHelperData
     */
    protected $perficientHelperData;

    /**
     * @var magentoSession
     */
    protected $magentoSession;
    
    /**
     * @var productConfiguredPrice
     */
    protected $productConfiguredPrice;

    /**
     * @var attributeSet
     */
    protected $attributeSet;
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var productimizeCalculation
     */
    protected $productimizeCalculation;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \DCKAP\Productimize\Helper\Data $helperData
     */
    public function __construct(Context $context,
                                PageFactory $resultPageFactory,
                                Product $product,
                                ProductimizeCalculation $productimizeCalculation,
                                ResourceConnection $resourceConnection,
                                ProductRepository $productRepository,
                                \DCKAP\Productimize\Helper\Data $helperData,
                                \Perficient\Company\Helper\Data $perficientHelperData,
                                \Magento\Customer\Model\SessionFactory $magentoSession,
                                ProductConfiguredPrice $productConfiguredPrice,
                                \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
        $this->perficientHelperData = $perficientHelperData;
        $this->product = $product;
        $this->productimizeCalculation = $productimizeCalculation;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->magentoSession = $magentoSession;
        $this->productConfiguredPrice = $productConfiguredPrice;
        $this->attributeSet = $attributeSet;

    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $session = $this->magentoSession->create();
        $artworkData = [];
        $productId = $this->getRequest()->getParam('product');
        $productData = $this->product->load($productId);
        $restrictedAccess = $this->helperData->getCustomerAccessRestrictionCode();
        $productPrice = $this->helperData->getDisplayPrice($productId, false);

        //get attribute set name
        $attributeSetRepository = $this->attributeSet->get($productData->getAttributeSetId());
        $attributeName = $attributeSetRepository->getAttributeSetName();
        $productDefaultConf = $productData->getDefaultConfigurations();
        $frontEndPzCartPropertiesData = null;


        if (strtolower($attributeName) == 'art') {
            $defaultConfData = $this->helperData->getDefaultConfigurationJson($productDefaultConf);
            $artworkData['configuration_level'] = $productData->getResource()->getAttribute('configuration_level')->getFrontend()->getValue($productData);
            $artworkData['image_height'] = $productData->getImageHeight();
            $artworkData['image_width'] = $productData->getImageWidth();
            $artworkData['top_mat_size_left'] = $productData->getTopMatSizeLeft();
            $artworkData['top_mat_size_right'] = $productData->getTopMatSizeRight();
            $artworkData['top_mat_size_top'] = $productData->getTopMatSizeTop();
            $artworkData['top_mat_size_bottom'] = $productData->getTopMatSizeBottom();
            $artworkData['bottom_mat_size_left'] = $productData->getBottomMatSizeLeft();
            $artworkData['bottom_mat_size_right'] = $productData->getBottomMatSizeRight();
            $artworkData['bottom_mat_size_top'] = $productData->getBottomMatSizeTop();
            $artworkData['bottom_mat_size_bottom'] = $productData->getBottomMatSizeBottom();
            $artworkData['max_image_height'] = $productData->getMaxImageHeight();
            $artworkData['max_image_width'] = $productData->getMaxImageWidth();
            $artworkData['max_image_width'] = $productData->getMaxImageWidth();


            $artworkData['default_configuration'] = ($defaultConfData) ? $defaultConfData['jsonStr'] : null;
            $artworkData['default_configuration_label'] = ($defaultConfData) ? $defaultConfData['labelStr'] : null;


            $artworkData['glass_width'] = isset($artworkData['default_configuration']['glass_width']) ? $artworkData['default_configuration']['glass_width'] : $artworkData['image_width'] ;
            $artworkData['glass_height'] = isset($artworkData['default_configuration']['glass_height']) ? $artworkData['default_configuration']['glass_height'] : $artworkData['image_height'];


            $frontEndPzCartPropertiesData = ($defaultConfData) ? $defaultConfData['pzCartPropertiesData'] : null;
        }


        $artworkInfo = $artworkData ? json_encode($artworkData) : "";
        $productId = $this->getRequest()->getParam('product');
        if ($this->getRequest()->getParam('isAjax')) {
            $topMatCollection = [];
            $bottomMatCollection = [];
            $frameCollection = [];
            $finalSizes = [];
            $editParamsSkuOnly = null;
            $renderArtworkData = [];
            $linerData = [];
            $selectedMedium = "";
            $selectedTreatment = "";
            $userSelectedMedium = "";
            $userSelectedTreatment = "";
            $frameDefaultSku = "";
            $topMatDefaultSku = "";
            $bottomMatDefaultSku = "";
            $linerDefaultSku = "";
            $configuredPrice = "";
            $quoteData = "";
            $pageFrom = "";
            $selectedEditParams = [];
            $qouteIdForEdit = $this->getRequest()->getParam('id');
            $pageLayoutType = $this->getRequest()->getParam('pageLayoutType');
            if ($qouteIdForEdit > 0 && isset($pageLayoutType) && $pageLayoutType == "edit") {
                $pageFrom = $this->getRequest()->getParam('page');
                $editType = $this->getRequest()->getParam('type');

                if ($pageFrom == "wishlist_index_configure") {
                    $quoteData = $this->helperData->getAllAdditionalOptionsByWishlistId($qouteIdForEdit, $productDefaultConf);
                } else {
                    $urlQryString = $this->getRequest()->getParam('qryStr');
                    if (isset($urlQryString) && !empty($urlQryString)) {
                        parse_str($urlQryString, $urlQryStringArr);
                        if (is_array($urlQryStringArr) && count($urlQryStringArr) > 0 && array_key_exists('wishlist_share_id', $urlQryStringArr)) {
                            $qouteIdForEdit = $urlQryStringArr['wishlist_share_id'];
                            $quoteData = $this->helperData->getAllAdditionalOptionsByWishlistId($qouteIdForEdit, $productDefaultConf);

                        } else if (isset($editType) && str_contains($editType, 'configure')) {
                            $selectedEditParams = $this->helperData->getEditUrlQryString($urlQryString);
                            $userSelectedMedium = $selectedEditParams['medium'];
                            $userSelectedTreatment = $selectedEditParams['treatment'];
                            $editParamsSkuOnly = $selectedEditParams;
                        }
                    }
                    else if ($pageFrom == "checkout_cart_configure") {
                        $quoteData = $this->helperData->getAllAdditionalOptionsByQuoteId($qouteIdForEdit, $productDefaultConf);
                    }
                }
                if (isset($quoteData) && isset($quoteData['fullOptions'])) {
                    $selectedEditParams = $quoteData['fullOptions'];
                }
                if (isset($quoteData) && isset($quoteData['optionValueWithSkuOnly'])) {
                    $editParamsSkuOnly = $quoteData['optionValueWithSkuOnly'];
                }
            }
            if (isset($artworkData) && !empty($artworkData) && array_key_exists('configuration_level', $artworkData)) {
                if (($artworkData['default_configuration'])) {
                    $parsedDefCon = json_decode($artworkData['default_configuration'], 1);
                        if ($parsedDefCon && $parsedDefCon['medium_default_sku']) {
                            $selectedMedium = $parsedDefCon['medium_default_sku'];
                        }
                        if ($parsedDefCon && $parsedDefCon['treatment_default_sku']) {
                            $selectedTreatment = $parsedDefCon['treatment_default_sku'];
                        }
                    if ($parsedDefCon && isset($parsedDefCon['frame_default_sku'])) {
                        $frameDefaultSku = $parsedDefCon['frame_default_sku'];
                    }
                    if ($parsedDefCon && isset($parsedDefCon['top_mat_default_sku'])) {
                        $topMatDefaultSku = $parsedDefCon['top_mat_default_sku'];
                    }
                    if ($parsedDefCon && isset($parsedDefCon['bottom_mat_default_sku'])) {
                        $bottomMatDefaultSku = $parsedDefCon['bottom_mat_default_sku'];
                    }
                    if ($parsedDefCon && isset($parsedDefCon['liner_default_sku'])) {
                        $linerDefaultSku = $parsedDefCon['liner_default_sku'];
                    }
                }
            }
            $connection = $this->resourceConnection->getConnection();
            $query = "SELECT *, m.display_name as media_display_name, t.display_name as treat_display_name, mt.display_to_customer as display_to_customermt FROM `media_treatment` as mt INNER join `media` as m on m.sku=mt.media_sku INNER join `treatment` as t on t.treatment_sku=mt.treatment_sku  JOIN frame_treatment tf on tf.treatment_sku = t.treatment_sku ";
            if ($artworkData['configuration_level'] > 1 && $selectedMedium && $selectedTreatment) {
                $query .= " WHERE (mt.display_to_customer IN (0,1) AND m.sku='" . $selectedMedium . "' AND t.treatment_sku='" . $selectedTreatment . "')";
            }
            else if ($selectedMedium && $selectedTreatment) {
                $query .= " WHERE (mt.display_to_customer in (0,1) AND m.sku='" . $selectedMedium . "' AND t.treatment_sku='" . $selectedTreatment . "') OR mt.display_to_customer=1";
            }
            else {
                $query .=  "WHERE mt.display_to_customer=1";
            }
            $mediaTreatmentitems = $connection->fetchAll($query);
            $mediafinalArrayNew = [];
            if (is_array($mediaTreatmentitems)) {
                if (!empty($mediaTreatmentitems)) {
                    foreach ($mediaTreatmentitems as $mediaTreatmentitem) {
                        //if ($qouteIdForEdit > 0 && (empty($editType)) && isset($selectedEditParams) && array_key_exists('medium', $selectedEditParams) && $selectedEditParams['medium'] == $mediaTreatmentitem["media_display_name"]) {
                        if ($qouteIdForEdit > 0 && (empty($editType)) && isset($selectedEditParams) && array_key_exists('medium', $selectedEditParams) && $selectedEditParams['medium'] == $mediaTreatmentitem["media_sku"]) {
                            $userSelectedMedium = $mediaTreatmentitem['media_sku'];
                            $editParamsSkuOnly['medium'] = $userSelectedMedium;
                        }
                        if ($qouteIdForEdit > 0 && (empty($editType)) && isset($selectedEditParams) && array_key_exists('treatment', $selectedEditParams) && trim($selectedEditParams['treatment']) == trim($mediaTreatmentitem["treatment_sku"])) {
                            $userSelectedTreatment = $mediaTreatmentitem['treatment_sku'];
                            $editParamsSkuOnly['treatment'] = $userSelectedTreatment;
                        }
                        if (array_key_exists($mediaTreatmentitem['media_sku'], $mediafinalArrayNew)) {
                            $emptyArray = array();
                            $frames = array();
                            if (array_key_exists($mediaTreatmentitem['treatment_sku'], $mediafinalArrayNew[$mediaTreatmentitem['media_sku']]['treatment'])) {
                                $frames = $mediafinalArrayNew[$mediaTreatmentitem['media_sku']]['treatment'][$mediaTreatmentitem['treatment_sku']]['frames'];
                            } else {
                                $frames = array();
                            }
                            array_push($frames, $mediaTreatmentitem['frame_type']);
                            $mediafinalArrayNew[$mediaTreatmentitem['media_sku']]['treatment'][$mediaTreatmentitem['treatment_sku']] = [
                                'treatment_id' => $mediaTreatmentitem['treatment_id'],
                                'treatment_sku' => $mediaTreatmentitem['treatment_sku'],
                                'base_cost_treatment' => $mediaTreatmentitem['base_cost_treatment'],
                                'display_name' => $mediaTreatmentitem['treat_display_name'],
                                'requires_top_mat' => $mediaTreatmentitem['requires_top_mat'],
                                'requires_bottom_mat' => $mediaTreatmentitem['requires_bottom_mat'],
                                'requires_liner' => $mediaTreatmentitem['requires_liner'],
                                'liner_depth_check' => $mediaTreatmentitem['liner_depth_check'],
                                'min_glass_size_short' => $mediaTreatmentitem['min_glass_size_short'],
                                'min_glass_size_long' => $mediaTreatmentitem['min_glass_size_long'],
                                'max_glass_size_short' => $mediaTreatmentitem['max_glass_size_short'],
                                'max_glass_size_long' => $mediaTreatmentitem['max_glass_size_long'],
                                'min_rabbet_depth' => $mediaTreatmentitem['min_rabbet_depth'],
                                'image_edge_treatment' => $mediaTreatmentitem['image_edge_treatment'],
                                'new_top_mat_size_left' => $mediaTreatmentitem['new_top_mat_size_left'],
                                'new_top_mat_size_top' => $mediaTreatmentitem['new_top_mat_size_top'],
                                'new_top_mat_size_right' => $mediaTreatmentitem['new_top_mat_size_right'],
                                'new_top_mat_size_bottom' => $mediaTreatmentitem['new_top_mat_size_bottom'],
                                'new_bottom_mat_size_left' => $mediaTreatmentitem['new_bottom_mat_size_left'],
                                'new_bottom_mat_size_top' => $mediaTreatmentitem['new_bottom_mat_size_top'],
                                'new_bottom_mat_size_right' => $mediaTreatmentitem['new_bottom_mat_size_right'],
                                'new_bottom_mat_size_bottom' => $mediaTreatmentitem['new_bottom_mat_size_bottom'],
                                'display_to_customer' => $mediaTreatmentitem['display_to_customermt'],
                                'frames' => $frames,
                            ];
                        } else {
                            $frameType = array();
                            $mediafinalArrayNew[$mediaTreatmentitem['media_sku']] = [
                                'media_id' => $mediaTreatmentitem['media_id'],
                                'sku' => $mediaTreatmentitem['sku'],
                                'base_cost_media' => $mediaTreatmentitem['base_cost_media'],
                                'display_name' => $mediaTreatmentitem['media_display_name'],
                                'display_to_customer' => $mediaTreatmentitem['display_to_customermt'],
                                'min_image_size_short' => $mediaTreatmentitem['min_image_size_short'],
                                'min_image_size_long' => $mediaTreatmentitem['min_image_size_long'],
                                'max_image_size_short' => $mediaTreatmentitem['max_image_size_short'],
                                'max_image_size_long' => $mediaTreatmentitem['max_image_size_long'],
                                'treatment' => [
                                    $mediaTreatmentitem['treatment_sku'] => [
                                        'treatment_id' => $mediaTreatmentitem['treatment_id'],
                                        'treatment_sku' => $mediaTreatmentitem['treatment_sku'],
                                        'base_cost_treatment' => $mediaTreatmentitem['base_cost_treatment'],
                                        'display_name' => $mediaTreatmentitem['treat_display_name'],
                                        'requires_top_mat' => $mediaTreatmentitem['requires_top_mat'],
                                        'requires_bottom_mat' => $mediaTreatmentitem['requires_bottom_mat'],
                                        'requires_liner' => $mediaTreatmentitem['requires_liner'],
                                        'liner_depth_check' => $mediaTreatmentitem['liner_depth_check'],
                                        'min_glass_size_short' => $mediaTreatmentitem['min_glass_size_short'],
                                        'min_glass_size_long' => $mediaTreatmentitem['min_glass_size_long'],
                                        'max_glass_size_short' => $mediaTreatmentitem['max_glass_size_short'],
                                        'max_glass_size_long' => $mediaTreatmentitem['max_glass_size_long'],
                                        'min_rabbet_depth' => $mediaTreatmentitem['min_rabbet_depth'],
                                        'image_edge_treatment' => $mediaTreatmentitem['image_edge_treatment'],
                                        'new_top_mat_size_left' => $mediaTreatmentitem['new_top_mat_size_left'],
                                        'new_top_mat_size_top' => $mediaTreatmentitem['new_top_mat_size_top'],
                                        'new_top_mat_size_right' => $mediaTreatmentitem['new_top_mat_size_right'],
                                        'new_top_mat_size_bottom' => $mediaTreatmentitem['new_top_mat_size_bottom'],
                                        'new_bottom_mat_size_left' => $mediaTreatmentitem['new_bottom_mat_size_left'],
                                        'new_bottom_mat_size_top' => $mediaTreatmentitem['new_bottom_mat_size_top'],
                                        'new_bottom_mat_size_right' => $mediaTreatmentitem['new_bottom_mat_size_right'],
                                        'new_bottom_mat_size_bottom' => $mediaTreatmentitem['new_bottom_mat_size_bottom'],
                                        'display_to_customer' => $mediaTreatmentitem['display_to_customermt'],
                                        'frames' => [$mediaTreatmentitem['frame_type']]
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }
            unset($mediafinalArray);
            $mediafinalArray = $mediafinalArrayNew;
            if ($qouteIdForEdit > 0 && isset($pageLayoutType) && $pageLayoutType == "edit") {
                if ($selectedEditParams && count($selectedEditParams) > 0) {
                    $didMediaOrTreatmentChange = 0;
                    $didSizeOrFrameChange = 0;
                    $isDefaultFrame = 0;
                    $isDefaultTopMat = 0;
                    $isDefaultBottomMat = 0;
                    $isDefaultLiner = 0;
                    $selectedFrame = array_key_exists('frame', $selectedEditParams) ? explode(',', $selectedEditParams['frame'])[0] : '';
                    $selectedFrameColor = "";
                    $selectedFrameWidth = 0.1;
                    $selectedFrameRabbetDepth = 0.1;
                    $selectedFrameType = "";
                    $selectedFrameName = "";
                    if ($frameDefaultSku == $selectedFrame) {
                        $isDefaultFrame = 1;
                    } else {
                        $didSizeOrFrameChange = 1;
                    }


                    $selectedTopMat = array_key_exists('top mat', $selectedEditParams) ? explode(',', $selectedEditParams['top mat'])[0] : '';
                    $selectedTopMatColor = "";
                    $selectedTopMatWidth = 0.1;
                    if ($topMatDefaultSku == $selectedTopMat) {
                        $isDefaultTopMat = 1;
                    }
                    $selectedBottomMat = array_key_exists('bottom mat', $selectedEditParams) ? explode(',', $selectedEditParams['bottom mat'])[0] : '';
                    $selectedBottomMatColor = "";
                    $selectedBottomMatWidth = 0.1;
                    if ($bottomMatDefaultSku == $selectedBottomMat) {
                        $isDefaultBottomMat = 1;
                    }
                    $selectedLiner = array_key_exists('liner', $selectedEditParams) ? explode(',', $selectedEditParams['liner'])[0] : '';
                    $selectedLinerColor = "";
                    $selectedLinerWidth = 0.1;
                    if ($linerDefaultSku == $selectedLiner) {
                        $isDefaultLiner = 1;
                    }

                    $selectedArtworkCustomColor = array_key_exists('artwork color', $selectedEditParams) ? explode(',', $selectedEditParams['artwork color'])[0] : '';


                    if (array_key_exists('medium', $selectedEditParams)) {
                        if ($selectedMedium != $userSelectedMedium) {
                            $didMediaOrTreatmentChange = 1;
                        }
                        $selectedMedium = $userSelectedMedium;
                    }
                    if (array_key_exists('treatment', $selectedEditParams)) {
                        if ($selectedTreatment != $userSelectedTreatment) {
                            $didMediaOrTreatmentChange = 1;
                        }
                        $selectedTreatment = $userSelectedTreatment;
                    }
                    if ($selectedMedium && $selectedTreatment) {
                        $selectedTreatmentData = $mediafinalArray[$userSelectedMedium]['treatment'][$userSelectedTreatment];
                        $treatmentMinRabbetDepth = $selectedTreatmentData['min_rabbet_depth'];
                        $treatmentRequireTopMat = $selectedTreatmentData['requires_top_mat'];
                        $treatmentRequireBottomMat = $selectedTreatmentData['requires_bottom_mat'];
                        $treatmentRequireLiner = $selectedTreatmentData['requires_liner'];
                        $treatmentLinerRabbetDepthCheck = $selectedTreatmentData['liner_depth_check'];
                        $finalSizes = $this->productimizeCalculation->getSizeCalculation($selectedMedium, $selectedTreatment,
                            $productId);
                        if (array_key_exists('size', $selectedEditParams)) {
                            $selectedSize = $selectedEditParams['size'];
                           // $defaultSize = $artworkData["glass_height"] . '×' . $artworkData["glass_width"];
                            //if ($artworkData["glass_width"] > $artworkData["glass_height"]) {
                            $defaultSize = $artworkData["glass_width"] . '×' . $artworkData["glass_height"];
                            //}
                            if ($selectedSize != $defaultSize) {
                                $didSizeOrFrameChange = 1;
                            }
                            $selectedSize = ($selectedSize) ? $selectedSize : $defaultSize;
                            $renderArtworkData['size'] = array(
                                'sku' => $selectedSize,
                                'displayName' => $selectedSize
                            );
                            $splittedSelectedSize = explode('×', $selectedSize);
                        }

                        // Get frame collection
                        $frameData = array(
                            "config_level" => $artworkData['configuration_level'],
                            "selected_medium" => $selectedMedium,
                            "selected_treatment" => $selectedTreatment,
                            "selected_size" => $selectedSize,
                            "has_changed_medium_treatment" => $didMediaOrTreatmentChange,
                            "is_default_frame" => $isDefaultFrame,
                            "min_rabbet_depth" => $treatmentMinRabbetDepth,
                            "product" => $productId,
                            "type" => "frame"
                        );
                        $frameCollection = $this->productimizeCalculation->getFrameCalculation($frameData);
                        if ($frameCollection && count($frameCollection) > 0 && $selectedFrame && trim(strtolower($selectedFrame)) != "no frame") {
                            if (isset($frameCollection[$selectedFrame])) {
                                $currFrame = $frameCollection[$selectedFrame];
                                $selectedFrameColor = $currFrame["m_color_frame"];
                                $selectedFrameWidth = $currFrame["m_frame_width"];
                                $selectedFrameRabbetDepth = $currFrame["m_frame_rabbet_depth"];
                                $selectedFrameType = $currFrame["m_frame_type"];
                                $selectedFrameName = $selectedFrame;
                            }
                        }

                        // Get Mats Collection
                        $topMatData = array(
                            "config_level" => $artworkData['configuration_level'],
                            "selected_medium" => $selectedMedium,
                            "selected_treatment" => $selectedTreatment,
                            "selected_size" => $selectedSize,
                            "require_topmat_for_treatment" => $treatmentRequireTopMat,
                            "has_changed_medium_treatment" => $didMediaOrTreatmentChange,
                            "has_changed_size_frame" => $didSizeOrFrameChange,
                            "is_default_topmat" => $isDefaultTopMat,
                            "is_default_bottommat" => $isDefaultBottomMat,
                            "width" => $splittedSelectedSize[0],
                            "height" => $splittedSelectedSize[1],
                            "product" => $productId,
                            "type" => "topmat"
                        );
                        $topMatCollection = $this->productimizeCalculation->getTopMatCalculation($topMatData);

                        if ($topMatCollection && count($topMatCollection) > 0 && $selectedTopMat && trim(strtolower($selectedTopMat)) != "no top mat") {
                            if (isset($topMatCollection[$selectedTopMat])) {
                                $currItem = $topMatCollection[$selectedTopMat];
                                $selectedTopMatColor = $currItem["m_color_mat"];
                            }
                        }

                        $bottomMatData = array(
                            "config_level" => $artworkData['configuration_level'],
                            "selected_medium" => $selectedMedium,
                            "selected_treatment" => $selectedTreatment,
                            "selected_size" => $selectedSize,
                            "require_bottommat_for_treatment" => $treatmentRequireBottomMat,
                            "has_changed_medium_treatment" => $didMediaOrTreatmentChange,
                            "has_changed_size_frame" => $didSizeOrFrameChange,
                            "is_default_topmat" => $isDefaultTopMat,
                            "is_default_bottommat" => $isDefaultBottomMat,
                            "width" => $splittedSelectedSize[0],
                            "height" => $splittedSelectedSize[1],
                            "product" => $productId,
                            "type" => "bottommat"
                        );

                        $bottomMatCollection = $this->productimizeCalculation->getBottomMatCalculation($bottomMatData);
                        if ($bottomMatCollection && count($bottomMatCollection) > 0 && $selectedBottomMat && trim(strtolower($selectedBottomMat)) != "no bottom mat") {
                            if (isset($bottomMatCollection[$selectedBottomMat])) {
                                $currItem = $bottomMatCollection[$selectedBottomMat];
                                $selectedBottomMatColor = $currItem["m_color_mat"];
                            }
                        }

                        // Get LinerCollection
                        $linerCollData = array(
                            "config_level" => $artworkData['configuration_level'],
                            "selected_medium" => $selectedMedium,
                            "selected_treatment" => $selectedTreatment,
                            "selected_size" => $selectedSize,
                            "frame_type" => $selectedFrameType,
                            "has_changed_medium_treatment" => $didMediaOrTreatmentChange,
                            "has_changed_size_frame" => $didSizeOrFrameChange,
                            "min_rabbet_depth" => $treatmentMinRabbetDepth ? $treatmentMinRabbetDepth : 0,
                            "frame_rabbet_depth" => $selectedFrameRabbetDepth ? $selectedFrameRabbetDepth : 0,
                            "require_liner_for_treatment" => $treatmentRequireLiner,
                            "selected_frame_sku" => $selectedFrame ? $selectedFrame : '',
                            "liner_depth_check" => isset($treatmentLinerRabbetDepthCheck) ? $treatmentLinerRabbetDepthCheck : 0,
                            "default_liner_sku" => $linerDefaultSku,
                            "is_default_liner" => $isDefaultLiner,
                            "product" => $productId,
                            "type" => "liner"
                        );

                        $linerData = $this->productimizeCalculation->getLinerCalculation($linerCollData);
                        if ($linerData && count($linerData) > 0 && $selectedLiner && trim(strtolower($selectedLiner)) != "no liner") {
                            if (isset($linerData[$selectedLiner])) {
                                $currItem = $linerData[$selectedLiner];
                                $selectedLinerColor = $currItem["m_color_liner"];
                                $selectedLinerWidth = $currItem["m_liner_width"];
                            }
                        }

                        // Price Param generation
                        $outDimData = array(
                            "outerWidth" => $splittedSelectedSize[0],
                            "outerHeight" => $splittedSelectedSize[1],
                            "frameType" => ($selectedFrameType) ? $selectedFrameType : "",
                            "frameWidth" => ($selectedFrameWidth) ? $selectedFrameWidth : "",
                            "linerWidth" => ($selectedLinerWidth) ? $selectedLinerWidth : "",

                        );

                        $outerDimention = $this->getOuterDimensionCalc($outDimData);

                        $priceParams = array(
                            "product_id" => $productId,
                            "product" => $productId,
                            "medium" => $selectedMedium,
                            "treatment" => $selectedTreatment,
                            "frame_sku" => $selectedFrame,
                            "top_mat_sku" => $selectedTopMat,
                            "bottom_mat_sku" => $selectedBottomMat,
                            "liner_sku" => $selectedLiner,
                            "image_width" => (isset($artworkData["image_width"]))  ? $artworkData["image_width"]: null,
                            "image_height" => (isset($artworkData["image_height"]))  ? $artworkData["image_height"]: null,
                            "glass_width" => $splittedSelectedSize[0],
                            "glass_height" => $splittedSelectedSize[1],
                            "item_width" => (is_array($outerDimention) && count($outerDimention) > 1)  ? $outerDimention[0] : null,
                            "item_height" => (is_array($outerDimention) && count($outerDimention) > 1)  ? $outerDimention[1] : null,
                            "custom_color" => $selectedArtworkCustomColor
                        );

                        $configuredPrice = $this->productimizeCalculation->getCustomisedPrice($priceParams);


                        // TODO TREATMENT
                        if (array_key_exists('treatment', $selectedEditParams)) {
                            $treatmentData = $this->helperData->getTreatmentData($selectedEditParams['treatment'], Data::$rendererFrameCustomImageType);
                            if (isset($treatmentData) && isset($treatmentData['lengthImage'])) {
                                $renderArtworkData['treatment'] = [
                                    'lengthImage' => $treatmentData['lengthImage'],
                                    "width" => isset($treatmentData['width']) ? (float)$treatmentData['width'] : 0.2,
                                ];
                            }
                        }

                        // To render the left side image
                        if (array_key_exists('frame', $selectedEditParams)) {
                            $images = $this->helperData->getProductImagesBySku($selectedFrame, Data::$rendererFrameCustomImageType);
                            if (isset($images) && isset($images['lengthImage'])) {
                                $renderArtworkData['frame'] = array(
                                    'sku' => $selectedFrame,
                                    'displayName' => $selectedFrameName,
                                    'color' => $selectedFrameColor,
                                    'width' => $selectedFrameWidth,
                                    "cornerImage" => $images['cornerImage'],
                                    "lengthImage" => $images['lengthImage'],
                                );
                            }
                        }
                        if (array_key_exists('top mat', $selectedEditParams)) {
                            if (strtoLower($selectedTopMat) != 'no top mat') {
                                $images = $this->helperData->getProductImagesBySku($selectedTopMat, Data::$rendererMatCustomImageType);
                                if (isset($images) && isset($images['rendererImage'])) {
                                    $renderArtworkData['topMat'] = array(
                                        'sku' => $selectedTopMat,
                                        'displayName' => $selectedTopMat,
                                        'color' => $selectedTopMatColor,
                                        'width' => $selectedTopMatWidth,
                                        "lengthImage" => $images['rendererImage'],
                                    );
                                }
                            }
                        }
                        if (array_key_exists('bottom mat', $selectedEditParams)) {
                            if (isset($selectedBottomMat) && strtoLower($selectedBottomMat) != 'no bottom mat') {
                                $images = $this->helperData->getProductImagesBySku($selectedBottomMat, Data::$rendererMatCustomImageType);
                                if (isset($images) && isset($images['rendererImage'])) {
                                    $renderArtworkData['bottomMat'] = array(
                                        'sku' => $selectedBottomMat,
                                        'displayName' => $selectedBottomMat,
                                        'color' => $selectedBottomMatColor,
                                        'width' => $selectedBottomMatWidth,
                                        "lengthImage" => $images['rendererImage'],
                                    );
                                }
                            }
                        }
                        if (array_key_exists('liner', $selectedEditParams)) {
                            if (isset($selectedLiner) && strtoLower($selectedLiner) != 'no liner') {
                                $images = $this->helperData->getProductImagesBySku($selectedLiner, Data::$rendererFrameCustomImageType);
                                if (isset($images) && isset($images['lengthImage']) && isset($images['cornerImage'])) {
                                    $renderArtworkData['liner'] = array(
                                        'sku' => $selectedLiner,
                                        'displayName' => $selectedLiner,
                                        'color' => $selectedLinerColor,
                                        'width' => $selectedLinerWidth,
                                        "cornerImage" => $images['cornerImage'],
                                        "lengthImage" => $images['lengthImage'],
                                    );
                                }
                            }
                        }
                    }
                }
            }

            $resultPage = $this->resultPageFactory->create();
            $blockInstance = $resultPage->getLayout()->getBlock('productimize.home')
                ->setData('product_ids', $productId)
                ->setData('productData', $productData)
                ->setData('renderArtworkData', $renderArtworkData)
                ->setData('artworkDataLabel', $artworkData['default_configuration_label'])
                ->setData('artworkData', $artworkInfo)
                ->setData('productPrice', $productPrice)
                ->toHtml();

            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(
                [
                    'content' => $blockInstance,
                    'returndata' => $mediafinalArray,
                    'sizeData' => $finalSizes,
                    'FrameName' => $frameCollection,
                    'artworkData' => $artworkInfo,
                    'productPrice' => $productPrice,
                    'configuredPrice' => $configuredPrice,
                    'topMatData' => $topMatCollection,
                    'bottomMatData' => $bottomMatCollection,
                    'linerData' => $linerData,
                    'editData' => $editParamsSkuOnly,
                    'renderArtworkData' => $renderArtworkData,
                    'accessRestriction' => $restrictedAccess,
                    'pzCartPropertiesData' => $frontEndPzCartPropertiesData
                ]
            );
            return $resultJson;
        }
    }

    public function  getOuterDimensionCalc ($artworkData) {
        $glassWidth = null;
        if (isset($artworkData["outerWidth"])) {
            $glassWidth = (float)($artworkData["outerWidth"]);
            }
        if (isset($artworkData["frameWidth"])) {
            $glassWidth += (float)($artworkData["frameWidth"]) * 2;
        }
        if (isset($artworkData["linerWidth"])) {
            $glassWidth += (float)($artworkData["linerWidth"]) * 2;
        }
        if (strtolower($artworkData["frameType"]) == "standard") {
            $glassWidth -= (float)(0.5);
        } else if (strtolower($artworkData["frameType"]) == "floater") {
            $glassWidth += (float)(0.25);
        }
        if ($artworkData["linerWidth"]) {
            $glassWidth -= 0.5;
        }
        $glassHeight = null;

        if ($artworkData["outerHeight"]) {
            $glassHeight = (float)($artworkData["outerHeight"]);
        }
        if ($artworkData["frameWidth"]) {
            $glassHeight += (float)($artworkData["frameWidth"]) * 2;
        }
        if ($artworkData["linerWidth"]) {
            $glassHeight += (float)($artworkData["linerWidth"]) * 2;
        }
        if (strtolower($artworkData["frameType"]) == "standard") {
            $glassHeight -= (float)(0.5);
        } else if (strtolower($artworkData["frameType"]) == "floater") {
            $glassHeight += (float)(0.25);
        }
        if ($artworkData["linerWidth"]) {
            $glassHeight -= (float)(0.5);
        }
        return [(float)($glassWidth), (float)($glassHeight)];
    }

}