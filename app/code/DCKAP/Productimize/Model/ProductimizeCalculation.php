<?php

namespace DCKAP\Productimize\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as EavCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Filesystem\DirectoryList;
use Perficient\Productimize\Model\ProductConfiguredPrice;
use DCKAP\Productimize\Helper\Data AS ProductimizeHelper;
use Psr\Log\LoggerInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface as AttributeSetRepositoryInterface;

class ProductimizeCalculation
{
    public function __construct(
        protected readonly ResourceConnection $resourceConnection,
        protected readonly ProductFactory $productFactory,
        protected readonly CollectionFactory $productCollectionFactory,
        protected readonly EavCollectionFactory $eavCollectionFactory,
        protected readonly Config $eavConfig,
        protected readonly DirectoryList $directory,
        protected readonly ProductConfiguredPrice $perficientConfiguredprice,
        protected readonly ProductimizeHelper $dckapProductimizeHelper,
        private readonly LoggerInterface $logger,
        protected readonly AttributeSetRepositoryInterface $attributeSetRepository
    )
    {

    }

    public function getPDPPagePriceAndRestrictCustomizeButtonStatus($priceParams)
    {
        $productId = $priceParams['product_id'];
        $product = $this->productFactory->create()->load($productId);
        $croppedImage = $product->getCropped();
        $price = $this->dckapProductimizeHelper->getDisplayPrice($productId, false);
        $returnData = [];
        $returnData['restrictCustomizeButton'] = 1;
        $returnData['pdpDisplayPrice'] = $price;
        $returnData['accessRestrictionCode'] = $this->dckapProductimizeHelper->getCustomerAccessRestrictionCode();
        if (isset($product->getProductCustomizer) && $product->getProductCustomizer == 1 && (isset($croppedImage) && $croppedImage != "no_selection")) {
            $returnData['restrictCustomizeButton'] = 0;

        }
        return $returnData;
    }

    public function getProductById($productId)
    {
        try {
            $product = $this->productFactory->create()->load($productId);
            return $product;
        } catch (\Exception $e) {
            return null;
        }
    }


    public function getCustomisedPrice($priceParams)
    {
        $productId = $priceParams['product_id'];

        $endodeArtworkData = $this->getArtworkData($productId);
        $artwork = ($endodeArtworkData) ? json_decode($endodeArtworkData, 1) : null;

        // Calculate long and short glass side
        if ($artwork) {

            $priceParams['item_width'] = isset($priceParams['item_width']) ? $priceParams['item_width'] : $artwork['item_width'];
            $priceParams['item_height'] = isset($priceParams['item_height']) ? $priceParams['item_height'] : $artwork['item_height'];

            $priceParams['image_width'] = $artwork['image_width'];
            $priceParams['image_height'] = $artwork['image_height'];

        }

        if (isset($priceParams['frame_sku']) && $priceParams['frame_sku'] == 'No Frame') {
            $priceParams['frame_sku'] = strtolower(str_replace('No Frame', '', $priceParams['frame_sku']));
        } elseif (isset($priceParams['top_mat_sku']) && $priceParams['top_mat_sku'] == 'No Mat') {
            $priceParams['top_mat_sku'] = strtolower(str_replace('No Mat', '', $priceParams['top_mat_sku']));
        } elseif (isset($priceParams['bottom_mat_sku']) && $priceParams['bottom_mat_sku'] == 'No Mat') {
            $priceParams['bottom_mat_sku'] = strtolower(str_replace('No Mat', '', $priceParams['bottom_mat_sku']));
        } elseif (isset($priceParams['liner_sku']) && $priceParams['liner_sku'] == 'No Liner') {
            $priceParams['liner_sku'] = strtolower(str_replace('No Liner', '', $priceParams['liner_sku']));
        }

        $changedPriceParams = array(
            'medium' => $priceParams['medium'],
            'treatment' => $priceParams['treatment'],
            'frame_default_sku' => (isset($priceParams['frame_sku']) ? $priceParams['frame_sku'] : ''),
            'liner_sku' => (isset($priceParams['liner_sku']) ? $priceParams['liner_sku'] : ''),
            'top_mat_default_sku' => (isset($priceParams['top_mat_sku']) ? $priceParams['top_mat_sku'] : ''),
            'bottom_mat_default_sku' => (isset($priceParams['bottom_mat_sku']) ? $priceParams['bottom_mat_sku'] : ''),
            'glass_width' => (isset($priceParams['glass_width']) ? $priceParams['glass_width'] : ''),
            'glass_height' => (isset($priceParams['glass_height']) ? $priceParams['glass_height'] : ''),
            'item_width' => (isset($priceParams['item_width']) ? $priceParams['item_width'] : ''),
            'item_height' => (isset($priceParams['item_height']) ? $priceParams['item_height'] : ''),
            'art_work_color' => (isset($priceParams['custom_color']) ? $priceParams['custom_color'] : ''),
            "product_id" => $priceParams['product_id'],
            "product" => $priceParams['product_id']
        );

        $productJson = json_encode($changedPriceParams);
        $priceArr = $this->dckapProductimizeHelper->getDisplayAndSellingPrice($productId, $productJson);
        return $priceArr;
    }

    public function getArtworkData($productId)
    {

        //$productId = 27506;
        $artworkData = [];
        $product = $this->productFactory->create()->load($productId);

        //get attribute set name
        $attributeSetRepository = $this->attributeSetRepository->get($product->getAttributeSetId());
        $attributeName = $attributeSetRepository->getAttributeSetName();

        if (strtolower($attributeName) == 'art') {

            $defaultConfData = $this->dckapProductimizeHelper->getDefaultConfigurationJson($product->getDefaultConfigurations());


            $artworkData['configuration_level'] = $product->getResource()->getAttribute('configuration_level')->getFrontend()->getValue($product);
            $artworkData['image_height'] = $product->getImageHeight();
            $artworkData['image_width'] = $product->getImageWidth();
            $artworkData['item_height'] = $product->getItemHeight();
            $artworkData['item_width'] = $product->getItemWidth();
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

            // $artworkGlassWidth = $product->getGlassWidth();
            // $artworkGlassHeight = $product->getGlassHeight();
            // $artworkData['glass_width'] = isset($artworkGlassWidth) ? $artworkGlassWidth : $artworkData['image_width'];
            // $artworkData['glass_height'] = isset($artworkGlassHeight) ? artworkGlassHeight : $artworkData['image_height'];
            // $artworkData['default_size'] = $artworkData["glass_height"] . '×' . $artworkData["glass_width"];
            // if ($artworkData["glass_width"] > $artworkData["glass_height"]) {
            //     $artworkData['default_size'] = $artworkData["glass_width"] . '×' . $artworkData["glass_height"];
            // }

            if ($defaultConfData) {
                $artworkDataDefault = json_decode($defaultConfData['jsonStr'], 1);
                $artworkData['default_medium'] = ($artworkDataDefault && $artworkDataDefault['medium_default_sku']) ? $artworkDataDefault['medium_default_sku'] : null;
                $artworkData['default_treatment'] = ($artworkDataDefault && isset($artworkDataDefault['treatment_default_sku'])) ? $artworkDataDefault['treatment_default_sku'] : null;
                $artworkData['default_frame'] = ($artworkDataDefault && $artworkDataDefault['frame_default_sku']) ? $artworkDataDefault['frame_default_sku'] : null;
                $artworkData['default_top_mat'] = ($artworkDataDefault && isset($artworkDataDefault['top_mat_default_sku'])) ? $artworkDataDefault['top_mat_default_sku'] : null;
                $artworkData['default_bottom_mat'] = ($artworkDataDefault && isset($artworkDataDefault['bottom_mat_default_sku'])) ? $artworkDataDefault['bottom_mat_default_sku'] : null;
                $artworkData['default_liner'] = ($artworkDataDefault && isset($artworkDataDefault['liner_default_sku'])) ? $artworkDataDefault['liner_default_sku'] : null;
                $artworkData['glass_width'] = ($artworkDataDefault && isset($artworkDataDefault['glass_width'])) ? $artworkDataDefault['glass_width'] : null;
                $artworkData['glass_height'] = ($artworkDataDefault && isset($artworkDataDefault['glass_height'])) ? $artworkDataDefault['glass_height'] : null;

                //$artworkData['default_size'] = $artworkData["glass_height"] . '×' . $artworkData["glass_width"];
                //if ($artworkData["glass_width"] > $artworkData["glass_height"]) {
                $artworkData['default_size'] = $artworkData["glass_width"] . '×' . $artworkData["glass_height"];
                //}
            }

            $artworkData['default_configuration'] = ($defaultConfData) ? $defaultConfData['jsonStr'] : null;
            $artworkData['default_configuration_label'] = ($defaultConfData) ? $defaultConfData['labelStr'] : null;
        }

        $artworkInfo = json_encode($artworkData);
        return $artworkInfo;
    }

    public function isValueSame($firstVal, $secondVal)
    {
        if ($firstVal == $secondVal) {
            return true;
        }
        return false;
    }

    public function getFrameCalculation($frameParams)
    {

        $artwork = $this->getArtworkData($frameParams['product']);;
        $artworkDefaultData = ($artwork) ? json_decode($artwork, 1) : null;

        $defaultSize = $artworkDefaultData['default_size'];

        $configLevel = $frameParams['config_level'];
        $selectedMediumOption = $frameParams['selected_medium'];
        $selectedTreatmentOption = $frameParams['selected_treatment'];

        $selectedSizeOption = $frameParams['selected_size'];
        $hasChangedMediaTreatment = $frameParams['has_changed_medium_treatment'];
        $isDefaultFrame = $frameParams['is_default_frame'];

        if ($configLevel <= 4) {
            if ($selectedMediumOption && $selectedTreatmentOption) {
                if ($hasChangedMediaTreatment || ($selectedSizeOption && $defaultSize != $selectedSizeOption)) {
                    return $this->frameConditionCheck($frameParams, $artworkDefaultData);
                } else { //todo:display default frame
                    if ($isDefaultFrame) {
                        return $this->frameConditionCheck($frameParams, $artworkDefaultData);
                    } else {
                        return $this->frameConditionCheck($frameParams, $artworkDefaultData);
                    }
                }
            } else {//disable frame
                return [];
            }
        } else {//disable frame
            return [];
        }
    }

    public function frameConditionCheck($frameParams, $artworkDefaultData)
    {
        return $this->getFrameCollection($frameParams, $artworkDefaultData);
    }

    public function getTopMatCalculation($topMatParams)
    {

        $artwork = $this->getArtworkData($topMatParams['product']);;
        $artworkDefaultData = ($artwork) ? json_decode($artwork, 1) : null;

        $configLevel = $topMatParams['config_level'];
        $selectedMediumOption = $topMatParams['selected_medium'];
        $selectedTreatmentOption = $topMatParams['selected_treatment'];
        $hasChangedMediaTreatment = $topMatParams['has_changed_medium_treatment'];
        $requireTopMatForTreatment = $topMatParams['require_topmat_for_treatment'];
        $isDefaultTopMat = $topMatParams['is_default_topmat'];

        if ($configLevel <= 4) {
            if ($selectedMediumOption && $selectedTreatmentOption) {
                if ($hasChangedMediaTreatment) {
                    if ($requireTopMatForTreatment) {
                        return $this->getFirstMatCondition('topmat', $topMatParams, $artworkDefaultData);
                    } else {
                        //disable topmat
                        return [];
                    }
                } else {
                    if ($isDefaultTopMat) {
                        return $this->getFirstMatCondition('topmat', $topMatParams, $artworkDefaultData);
                    } else {
                        //disable topmat
                        return [];
                    }
                }
            } else {//disable topmat
                return [];
            }
        }
    }

    public function getBottomMatCalculation($bottomMatParams)
    {
        $artwork = $this->getArtworkData($bottomMatParams['product']);;
        $artworkDefaultData = ($artwork) ? json_decode($artwork, 1) : null;

        $configLevel = $bottomMatParams['config_level'];
        $selectedMediumOption = $bottomMatParams['selected_medium'];
        $selectedTreatmentOption = $bottomMatParams['selected_treatment'];
        $hasChangedMediaTreatment = $bottomMatParams['has_changed_medium_treatment'];
        $requireBottomMatForTreatment = $bottomMatParams['require_bottommat_for_treatment'];
        $isDefaultBottomMat = $bottomMatParams['is_default_bottommat'];

        if ($configLevel <= 4) {
            if ($selectedMediumOption && $selectedTreatmentOption) {
                if ($hasChangedMediaTreatment) {
                    if ($requireBottomMatForTreatment) {
                        return $this->getFirstMatCondition('bottommat', $bottomMatParams, $artworkDefaultData);
                    } else {
                        //disable bottommat
                        return [];
                    }
                } else {
                    if ($isDefaultBottomMat) {
                        return $this->getFirstMatCondition('bottommat', $bottomMatParams, $artworkDefaultData);
                    } else {
                        //disable bottommat
                        return [];
                    }
                }
            } else {//disable bottommat
                return [];
            }
        }
    }

    private function getSideValue($artworkDefaultData, $userSelectedParams) {
        $ShortSide = $userSelectedParams["height"];
        $LongSide = $userSelectedParams["width"];

        if ($artworkDefaultData["image_width"] < $artworkDefaultData["image_height"]) {

            $ShortSide = $userSelectedParams["width"];
            $LongSide = $userSelectedParams["height"];
        }
        return array(
            'longSide' => $LongSide,
            'shortSide' => $ShortSide
        );
    }

    public function getFirstMatCondition($matTypeOption, $matParams, $artworkDefaultData)
    {
        $hasChangedMediaTreatment = $matParams['has_changed_medium_treatment'];
        $hasChangedSizeFrame = $matParams['has_changed_size_frame'];
        $isDefaultTopMat = $matParams['is_default_topmat'];
        $isDefaultBottomMat = $matParams['is_default_bottommat'];
        $width = $matParams['width'];
        $height = $matParams['height'];

        $sideValues = $this->getSideValue($artworkDefaultData, $matParams);
        $longSide = $sideValues['longSide'];
        $shortSide = $sideValues['shortSide'];

        $isDefaultMat = ($matTypeOption == 'topmat') ? $isDefaultTopMat : $isDefaultBottomMat;


        //if ($width > 40 || $height > 60) {

        /**
         * IF(LongSide <= 60 AND ShortSide<=40) THEN Check #2
         * IF(LongSide > 60 OR ShortSide > 40) THEN Default
         */
        if($longSide <= 60 && $shortSide <= 40) { // Check #2
            return $this->getSecondMatCondition($matTypeOption, $matParams, $artworkDefaultData);

        } else {
            //display default mat with no op to select
            return $this->getMatArray('default', $matTypeOption, $matParams, $artworkDefaultData);
        }




        /*if($longSide > 60 && $shortSide > 40) { // Check #2

            //display default mat with no op to select
            return $this->getMatArray('default', $matTypeOption, $matParams, $artworkDefaultData);
        } else {
            if ($hasChangedMediaTreatment || $hasChangedSizeFrame) {
                return $this->getSecondMatCondition($matTypeOption, $matParams, $artworkDefaultData);
            } else {
                if ($isDefaultMat) {
                    //display default mat
                    return $this->getMatArray('default', $matTypeOption, $matParams, $artworkDefaultData);
                } else {
                    return $this->getSecondMatCondition($matTypeOption, $matParams, $artworkDefaultData);
                }
            }
        }*/

    }

    public function getSecondMatCondition($matTypeOption, $matParams, $artworkDefaultData)
    {
        $width = $matParams['width'];
        $height = $matParams['height'];

        $sideValues = $this->getSideValue($artworkDefaultData, $matParams);
        $longSide = $sideValues['longSide'];
        $shortSide = $sideValues['shortSide'];


        /*** MAT type Rule
         *
         * IF(LongSide <= 40 AND ShortSide<=32) THEN Standard
         * IF(LongSide > 40 OR ShortSide > 32) THEN Oversized
         */

        if ($longSide <= 40 && $shortSide <= 32) {
            return $this->getMatArray('standard', $matTypeOption, $matParams, $artworkDefaultData);
        } else {
            return $this->getMatArray('oversized', $matTypeOption, $matParams, $artworkDefaultData);
        }
    }

    public function getMatArray($type, $matTypeOption, $matParams, $artworkDefaultData)
    {
        $matArray = [];
        $type = trim($type);
        $isDefaultMatArray = false;

        $isDefaultTopMatSku = isset($matParams['is_default_topmat_sku']) ? $matParams['is_default_topmat_sku'] : $matParams['is_default_topmat'];
        $isDefaultBottomMatSku = isset($matParams['is_default_bottommat_sku']) ? $matParams['is_default_bottommat_sku'] : $matParams['is_default_bottommat'];
        $defaultMatSku = ($matTypeOption == 'topmat') ? $isDefaultTopMatSku : $isDefaultBottomMatSku;
        $matData = $this->getMatCollection();


        if ($type == 'default' && array_key_exists(trim($defaultMatSku), $matData)) {
            $matArray[trim($defaultMatSku)] = $matData[trim($defaultMatSku)];
            return $matArray;
        }

        $defaultMatData = [];

        foreach ($matData as $key => $data) {
            $matType = trim($data['m_filter_size']);

            if (trim(strtolower($matType)) == trim(strtolower($type))) {
                $matArray[$data['m_sku']] = $data;
            }

            if ($type == 'default' && trim($defaultMatSku) == trim($data['m_sku'])) {
                $matArray = [];
                $matArray[$data['m_sku']] = $data;
                $isDefaultMatArray = true;
                return $matArray;
            }
            if ($matTypeOption == 'topmat') {
                if (trim($artworkDefaultData['default_top_mat']) == trim($data['m_sku'])) {
                    $defaultMatData[$data['m_sku']] = $data;
                }
            } else {
                if (trim($artworkDefaultData['default_bottom_mat']) == trim($data['m_sku'])) {
                    $defaultMatData[$data['m_sku']] = $data;
                }
            }
        }


        if ($matTypeOption == 'topmat') {
            if (!array_key_exists(trim($artworkDefaultData['default_top_mat']), $matArray) && isset($matParams['selected_medium']) && isset($artworkDefaultData['default_medium'])) {
                if ($this->isValueSame($matParams['selected_medium'], trim($artworkDefaultData['default_medium']))) {
                    if ($this->isValueSame($matParams['selected_treatment'], trim($artworkDefaultData['default_treatment']))) {
                        if ($this->isValueSame($matParams['selected_size'], trim($artworkDefaultData['default_size']))) {
                            if (isset($defaultMatData) && isset($defaultMatData[trim($artworkDefaultData['default_top_mat'])])) {
                                $defaultMatData[trim($artworkDefaultData['default_top_mat'])]['default_product'] = 1;
                                $matArray[trim($artworkDefaultData['default_top_mat'])] = $defaultMatData[trim($artworkDefaultData['default_top_mat'])];
                            }
                        }
                    }
                }
            }
        } else {
            if (!array_key_exists(trim($artworkDefaultData['default_bottom_mat']), $matArray) && isset($matParams['selected_medium']) && isset($artworkDefaultData['default_medium'])) {
                if ($this->isValueSame($matParams['selected_medium'], trim($artworkDefaultData['default_medium']))) {
                    if ($this->isValueSame($matParams['selected_treatment'], trim($artworkDefaultData['default_treatment']))) {
                        if ($this->isValueSame($matParams['selected_size'], trim($artworkDefaultData['default_size']))) {
                            if (isset($defaultMatData) && isset($defaultMatData[trim($artworkDefaultData['default_bottom_mat'])])) {
                                $defaultMatData[trim($artworkDefaultData['default_bottom_mat'])]['default_product'] = 1;
                                $matArray[$artworkDefaultData['default_bottom_mat']] = $defaultMatData[trim($artworkDefaultData['default_bottom_mat'])];
                            }
                        }
                    }
                }
            }
        }


        if ($type == 'default' && !$isDefaultMatArray) {
            return [];
        }

        ksort($matArray);
        return $matArray;
    }

    public function getLinerCalculation($linerParams)
    {
        $artwork = $this->getArtworkData($linerParams['product']);;
        $artworkDefaultData = ($artwork) ? json_decode($artwork, 1) : null;

        $configLevel = $linerParams['config_level'];
        $selectedMediumOption = $linerParams['selected_medium'];
        $selectedTreatmentOption = $linerParams['selected_treatment'];
        $selectedFrameSku = $linerParams['selected_frame_sku'];
        $hasChangedMediaTreatment = $linerParams['has_changed_medium_treatment'];
        $hasChangedSizeFrame = $linerParams['has_changed_size_frame'];
        $requireLinerForTreatment = $linerParams['require_liner_for_treatment'];
        $frameType = $linerParams['frame_type'];
        $frameType = strtolower($frameType);
        $isDefaultLiner = $linerParams['is_default_liner'];
        $frameRabbetDepth = $linerParams['frame_rabbet_depth'];
        $minRabbetDepth = $linerParams['min_rabbet_depth'];
        $linerRabbetDepthCheck = $linerParams['liner_depth_check'];
        $defaultLinerSku = $linerParams['default_liner_sku'];

        if ($configLevel < 4) {
            if ($selectedMediumOption && $selectedTreatmentOption && $selectedFrameSku != "No Frame") {
                if ($hasChangedMediaTreatment) {
                    if ($requireLinerForTreatment && $frameType == 'standard') {
                        return $this->getLinerArray('custom', $linerRabbetDepthCheck, $minRabbetDepth, $frameRabbetDepth, $defaultLinerSku, $linerParams, $artworkDefaultData);
                    } else {
                        //disable liner
                        return [];
                    }
                } else {
                    if ($isDefaultLiner) {
                        if ($hasChangedSizeFrame) {
                            if ($frameType == 'standard') {
                                return $this->getLinerArray('custom', $linerRabbetDepthCheck, $minRabbetDepth, $frameRabbetDepth, $defaultLinerSku, $linerParams, $artworkDefaultData);
                            } else {
                                //disable liner
                                return [];
                            }
                        } else {
                            //show default liner
                            return $this->getLinerArray('default', $linerRabbetDepthCheck, $minRabbetDepth, $frameRabbetDepth, $defaultLinerSku, $linerParams, $artworkDefaultData);
                        }
                    } else {
                        //disable liner
                        return [];
                    }
                }
            } else {
                //disable liner
                return [];
            }
        } else {
            //disable liner
            return [];
        }
    }

    public function getLinerArray($type, $linerRabbetDepthCheck, $minRabbetDepth, $frameRabbetDepth, $defaultLinerSku, $linerParams, $artworkDefaultData)
    {
        $type = trim($type);
        $linerArray = [];
        $linerData = $this->getLinerCollection();
        $defaultLinerData = [];
        foreach ($linerData as $key => $data) {
            $linerHeight = $data['m_liner_depth'];
            $linerRabbetDepth = $data['m_liner_rabbet_depth'];
            $linerCheck = 0;
            if ($linerRabbetDepthCheck) {
                if (($linerRabbetDepth >= $minRabbetDepth)) {
                    $linerCheck = 1;
                }
            } else {
                $linerCheck = 1;
            }

            if ($type == 'default') {
                $linerArray = [];
                if ($defaultLinerSku == $data['m_sku']) {
                    $linerArray = $data;
                    return $linerArray;
                } else {
                    return [];
                }
            }


            if (($linerHeight <= $frameRabbetDepth) && ($linerCheck) && (($linerHeight - $linerRabbetDepth + $minRabbetDepth) <= $frameRabbetDepth)) {
                $linerArray[$data["m_sku"]] = $data;

            }

            if ($artworkDefaultData['default_liner'] == $data['m_sku']) {
                $defaultLinerData[$data['m_sku']] = $data;
            }

        }

        if (!array_key_exists($artworkDefaultData['default_liner'], $linerArray) && isset($linerParams['selected_medium']) && isset($artworkDefaultData['default_medium'])) {
            if ($this->isValueSame($linerParams['selected_medium'], $artworkDefaultData['default_medium'])) {
                if ($this->isValueSame($linerParams['selected_treatment'], $artworkDefaultData['default_treatment'])) {
                    if ($this->isValueSame($linerParams['selected_size'], $artworkDefaultData['default_size'])) {
                        if (isset($defaultLinerData) && isset($defaultLinerData[$artworkDefaultData['default_liner']])) {
                            $defaultLinerData[$artworkDefaultData['default_liner']]['default_product'] = 1;
                            $linerArray[$artworkDefaultData['default_liner']] = $defaultLinerData[$artworkDefaultData['default_liner']];
                        }
                    }
                }
            }
        }

        ksort($linerArray);
        return $linerArray;
    }

    public function getFrameCollection($frameParams, $artworkDefaultData)
    {

        $selectedMediaTreatmentRow = [];

        if (isset($frameParams['selected_medium']) && isset($frameParams['selected_treatment'])) {
            $selectedMediaTreatmentRow = $this->getMediaTreatmentData($frameParams['selected_medium'], $frameParams['selected_treatment']);
        }

        $file = $this->dckapProductimizeHelper->getProductimizeJsonPath() . 'frame.json';


        $productJsonCollection = file_get_contents($file);
        $productCollection = json_decode($productJsonCollection, true);
        $minRabbetDepth = $frameParams['min_rabbet_depth'];
        $glassSize = $frameParams['selected_size'];
        $glassDimention = explode("×", $glassSize);
        $glassSize = $glassDimention[0] * $glassDimention[1];

        $defaultFrameData = [];


        $finalProducts = array();
        foreach ($productCollection as $item) {

            $frameRabbetDepth = $item['m_frame_rabbet_depth'];
            $frameMaxOuterSize = $item['m_max_outer_size'];


            if (($frameRabbetDepth >= $minRabbetDepth) && ($frameMaxOuterSize >= $glassSize / 144)) {
                if (isset($selectedMediaTreatmentRow) && count($selectedMediaTreatmentRow) > 0 && ((int)($selectedMediaTreatmentRow['requires_liner']) > 0)) {
                    if (isset($item['m_show_with_liners']) && strtolower($item['m_show_with_liners']) == "yes") {
                        $finalProducts[$item['m_sku']] = $item;
                    }
                } else {
                    $finalProducts[$item['m_sku']] = $item;
                }
            }
            if ($artworkDefaultData['default_frame'] == $item['m_sku']) {
                $defaultFrameData[$item['m_sku']] = $item;
            }
        }

        if (!array_key_exists($artworkDefaultData['default_frame'], $finalProducts) && isset($frameParams['selected_medium']) && isset($artworkDefaultData['default_medium'])) {
            if ($this->isValueSame($frameParams['selected_medium'], $artworkDefaultData['default_medium'])) {
                if ($this->isValueSame($frameParams['selected_treatment'], $artworkDefaultData['default_treatment'])) {
                    if ($this->isValueSame($frameParams['selected_size'], $artworkDefaultData['default_size'])) {
                        if (isset($defaultFrameData) && isset($defaultFrameData[$artworkDefaultData['default_frame']])) {
                            $defaultFrameData[$artworkDefaultData['default_frame']]['default_product'] = 1;
                            $finalProducts[$artworkDefaultData['default_frame']] = $defaultFrameData[$artworkDefaultData['default_frame']];
                        }
                    }
                }
            }
        }
        ksort($finalProducts);
        return $finalProducts;
    }

    public function getMatCollection()
    {
        $matfile = $this->directory->getPath('media') . '/mat.json';
        $matfile = $this->dckapProductimizeHelper->getProductimizeJsonPath() . 'mat.json';
        $productJsonCollection = file_get_contents($matfile);
        $productCollection = json_decode($productJsonCollection, true);

        $finalProducts = array();
        foreach ($productCollection as $item) {
            $finalProducts[$item['m_sku']] = $item;
        }
        return $finalProducts;
    }

    public function getLinerCollection()
    {
        $linerfile = $this->dckapProductimizeHelper->getProductimizeJsonPath() . 'liner.json';
        $productJsonCollection = file_get_contents($linerfile);
        $productCollection = json_decode($productJsonCollection, true);

        $finalProducts = array();
        foreach ($productCollection as $item) {
            $finalProducts[] = $item;
        }
        return $finalProducts;
    }

    public function getProductCollection($attrSetName, $attrCode, $optionId, $type, $frameinfo)
    {
        $attributeSetCollection = $this->eavCollectionFactory->create();
        $attributeSetCollection->addFieldToFilter('entity_type_id', 4)->addFieldToFilter('attribute_set_name', $attrSetName);
        $attrSet = current($attributeSetCollection->getData());
        $attributeSetId = $attrSet["attribute_set_id"];

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setFlag('has_stock_status_filter', false)
            ->addAttributeToSelect("*");


        if ($type == 'frame') {
            $productCollection->addFieldToFilter($attrCode, array('neq' => $optionId));
        } else if ($type == 'liner') {
            $productCollection->addFieldToFilter($attrCode, array('eq' => $optionId));
        }

        if (isset($frameinfo['web_product_id'])) {
            if ($frameinfo['web_product_id'] == 27505) {
                $productCollection->getSelect()->limit(50, 150);
            } else {
                $productCollection->getSelect()->limit(50, 1);
            }
        } else {
            $productCollection->getSelect()->limit(50, 1);
        }
        $productCollection->load();
        return $productCollection;
    }

    public function getAttributeValue($product, $attrCode)
    {
        $productFactory = $this->productFactory->create()->load($product->getId());
        return $productFactory->getResource()->getAttribute($attrCode)->getFrontend()->getValue($productFactory);
    }

    public function getGlassDim($imageDim, $matDim)
    {
        if ($matDim > 0) {
            return $imageDim + ($matDim - 0.50);
        } else {
            return $imageDim;
        }
    }

    public function getImageDim($glassDim, $matDim)
    {
        if ($matDim > 0) {
            return $glassDim - ($matDim - 0.50);
        } else {
            return $glassDim;
        }
    }

    public function getMediaTreatmentData($selectedMediumOption, $selectedTreatmentOption)
    {

        if ($selectedMediumOption && $selectedTreatmentOption) {

            $connection = $this->resourceConnection->getConnection();

            $mediaTreatmentQuery = $connection->select()
                ->from(
                    ['m' => 'media']
                )
                ->join(
                    ['mt' => 'media_treatment'],
                    'm.sku=mt.media_sku'
                )
                ->join(
                    ['t' => 'treatment'],
                    't.treatment_sku=mt.treatment_sku'
                )
                ->where('m.sku=?', $selectedMediumOption)
                //->where('mt.display_to_customer=?', 1)
                ->where('t.treatment_sku =?', $selectedTreatmentOption);

            $mediaTreatItemRow = $connection->fetchRow($mediaTreatmentQuery);

            if (!isset($mediaTreatItemRow)) {
                return false;
            }


            return $mediaTreatItemRow;
        }
        return false;
    }


    public function getSizeCalculation($selectedMediumOption, $selectedTreatmentOption, $productId)
    {
        $productId = ($productId) ? $productId : 27506;
        $endodeArtworkData = $this->getArtworkData($productId);
        $artwork = ($endodeArtworkData) ? json_decode($endodeArtworkData, 1) : null;

        $artworkDefaultGlassWidth = 0;
        $artworkDefaultGlassHeight = 0;
        $isDefaultMediaTreatment = false;

        if ($artwork && $artwork['default_configuration']) {
            $artworkDataDefault = json_decode($artwork['default_configuration'], 1);
            $artwork['medium'] = ($artworkDataDefault && $artworkDataDefault['medium_default_sku']) ? $artworkDataDefault['medium_default_sku'] : null;
            $artwork['treatment'] = ($artworkDataDefault && isset($artworkDataDefault['treatment_default_sku'])) ? $artworkDataDefault['treatment_default_sku'] : null;

            $artworkDefaultGlassWidth = ($artworkDataDefault && isset($artworkDataDefault['glass_width'])) ? $artworkDataDefault['glass_width'] : 0;
            $artworkDefaultGlassHeight = ($artworkDataDefault && isset($artworkDataDefault['glass_height'])) ? $artworkDataDefault['glass_height'] : 0;
        }


        $connection = $this->resourceConnection->getConnection();

        $mediaTreatmentQuery = $connection->select()
            ->from(
                ['m' => 'media']
            )
            ->join(
                ['mt' => 'media_treatment'],
                'm.sku=mt.media_sku'
            )
            ->join(
                ['t' => 'treatment'],
                't.treatment_sku=mt.treatment_sku'
            )
            ->where('m.sku=?', $selectedMediumOption)
            //->where('mt.display_to_customer=?', 1)
            ->where('t.treatment_sku =?', $selectedTreatmentOption);
        $mediaTreatItemRow = $connection->fetchRow($mediaTreatmentQuery);

        if (!isset($mediaTreatItemRow)) {
            return false;
        }

        $treatment = array(
            "new_top_mat_size_left" => $mediaTreatItemRow["new_top_mat_size_left"],
            "new_bottom_mat_size_left" => $mediaTreatItemRow["new_bottom_mat_size_left"],
            "new_top_mat_size_right" => $mediaTreatItemRow["new_top_mat_size_right"],
            "new_bottom_mat_size_right" => $mediaTreatItemRow["new_bottom_mat_size_right"],
            "new_top_mat_size_top" => $mediaTreatItemRow["new_top_mat_size_top"],
            "new_top_mat_size_bottom" => $mediaTreatItemRow["new_top_mat_size_bottom"],
            "new_bottom_mat_size_top" => $mediaTreatItemRow["new_bottom_mat_size_top"],
            "new_bottom_mat_size_bottom" => $mediaTreatItemRow["new_bottom_mat_size_bottom"],
            "max_glass_size_long" => $mediaTreatItemRow["max_glass_size_long"],
            "max_glass_size_short" => $mediaTreatItemRow["max_glass_size_short"],
            "min_glass_size_long" => $mediaTreatItemRow["min_glass_size_long"],
            "min_glass_size_short" => $mediaTreatItemRow["min_glass_size_short"],
            "base_cost_treatment" => $mediaTreatItemRow["base_cost_treatment"]

        );

        $media = array(
            "max_image_size_long" => $mediaTreatItemRow["max_image_size_long"],
            "max_image_size_short" => $mediaTreatItemRow["max_image_size_short"],
            "min_image_size_long" => $mediaTreatItemRow["min_image_size_long"],
            "min_image_size_short" => $mediaTreatItemRow["min_image_size_short"],
            "base_cost_media" => $mediaTreatItemRow["base_cost_media"]
        );


        /*
        Determine Short and Long Side of Artwork
        IF artwork.image_width < artwork.image_height THEN

            i.     Short_Side = Width

            ii.     Long_Side = Height

            iii.     Orientation = Vertical

        ELSE

            i.     Short Side = Height

            ii.     Long Side = Width

            iii.     Orientation = Horizontal

        */


        $Short_Side = $artwork["image_height"];
        $Long_Side = $artwork["image_width"];
        $Orientation = "Horizontal";


        $Mat_Left = max($treatment["new_top_mat_size_left"], $treatment["new_bottom_mat_size_left"]);
        $Mat_Right = max($treatment["new_top_mat_size_right"], $treatment["new_bottom_mat_size_right"]);
        $Mat_Top = max($treatment["new_top_mat_size_top"], $treatment["new_bottom_mat_size_top"]);
        $Mat_Bottom = max($treatment["new_top_mat_size_bottom"], $treatment["new_bottom_mat_size_bottom"]);


        if ($artwork["image_width"] < $artwork["image_height"]) {

            $Short_Side = $artwork["image_width"];
            $Long_Side = $artwork["image_height"];
            $Orientation = "Vertical";
        }

        if ($selectedMediumOption == $artwork['medium'] && $selectedTreatmentOption == $artwork['treatment']) {

            $Mat_Left = max($artwork["top_mat_size_left"], $artwork["bottom_mat_size_left"]);
            $Mat_Right = max($artwork["top_mat_size_right"], $artwork["bottom_mat_size_right"]);
            $Mat_Top = max($artwork["top_mat_size_top"], $artwork["bottom_mat_size_top"]);
            $Mat_Bottom = max($artwork["top_mat_size_bottom"], $artwork["bottom_mat_size_bottom"]);

            $isDefaultMediaTreatment = true;
        }


        if ($Orientation == "Vertical") {

            $matTotalLong = $Mat_Top + $Mat_Bottom;

            $matTotalShort = $Mat_Left + $Mat_Right;
        } else {

            $matTotalShort = $Mat_Top + $Mat_Bottom;

            $matTotalLong = $Mat_Left + $Mat_Right;
        }

        // Apply Size Filters (Get Bounds)
        //a.      Check Product
        if ($Orientation == "Vertical") {
            $productGlassLong = $this->getGlassDim($artwork["max_image_height"], $matTotalLong);
            $productGlassShort = $this->getGlassDim($artwork["max_image_width"], $matTotalShort);
        } else {
            $productGlassLong = $this->getGlassDim($artwork["max_image_width"], $matTotalLong);
            $productGlassShort = $this->getGlassDim($artwork["max_image_height"], $matTotalShort);
        }

        //b. Check Media

        $mediaGlassLongMax = $this->getGlassDim($media["max_image_size_long"], $matTotalLong);
        $mediaGlassShortMax = $this->getGlassDim($media["max_image_size_short"], $matTotalShort);
        $mediaGlassLongMin = $this->getGlassDim($media["min_image_size_long"], $matTotalLong);
        $mediaGlassShortMin = $this->getGlassDim($media["min_image_size_short"], $matTotalShort);


        //c.      Check Treatment
        $treatmentGlassLongMax = $treatment["max_glass_size_long"];
        $treatmentGlassShortMax = $treatment["max_glass_size_short"];
        $treatmentGlassLongMin = $treatment["min_glass_size_long"];
        $treatmentGlassShortMin = $treatment["min_glass_size_short"];

        /*
        BASE COST QUESRY STARTED HERE
        */


        // 5. Fill Size Slider Array
        $baseCostQuery = "SELECT glass_size_long, glass_size_short FROM base_cost WHERE ";
        $baseCostQuery .= "base_cost.base_cost_media = '" . $media['base_cost_media'] . "' AND ";

        $baseCostQuery .= "base_cost.base_cost_treatment = '" . $treatment['base_cost_treatment'] . "' AND ";

        $baseCostQuery .= "base_cost.glass_size_long <= " . $productGlassLong . " AND ";

        $baseCostQuery .= "base_cost.glass_size_short <= " . $productGlassShort . "  AND ";

        $baseCostQuery .= "base_cost.glass_size_long <= " . $mediaGlassLongMax . " AND ";

        $baseCostQuery .= "base_cost.glass_size_short <=" . $mediaGlassShortMax . " AND ";

        $baseCostQuery .= "base_cost.glass_size_long >= " . $mediaGlassLongMin . " AND ";

        $baseCostQuery .= "base_cost.glass_size_short >= " . $mediaGlassShortMin . " AND ";

        $baseCostQuery .= "base_cost.glass_size_long <= " . $treatmentGlassLongMax . " AND ";

        $baseCostQuery .= "base_cost.glass_size_short <= " . $treatmentGlassShortMax . " AND ";

        $baseCostQuery .= "base_cost.glass_size_long >=  " . $treatmentGlassLongMin . " AND ";

        $baseCostQuery .= "base_cost.glass_size_short >= " . $treatmentGlassShortMin;
        $baseCostRecords = $connection->fetchAll($baseCostQuery);


        //6. Apply Proportion Filter
        $ratio = $artwork['image_height'] / $artwork['image_width'];

        $minimumRatio = number_format((float)($ratio * 0.98), 4);
        $maximumRatio = number_format((float)($ratio * 1.02), 4);

        $finalBaseCost = array();


        if (is_array($baseCostRecords) && !empty($baseCostRecords)) {
            foreach ($baseCostRecords as $basecostRow) {
                if ($Orientation == "Vertical") {

                    $baseGlassHeight = $basecostRow["glass_size_long"];
                    $baseGlassWidth = $basecostRow["glass_size_short"];
                    $baseImageHeight = $this->getImageDim($basecostRow["glass_size_long"], $matTotalLong);
                    $baseImageWidth = $this->getImageDim($basecostRow["glass_size_short"], $matTotalShort);

                } else {
                    $baseGlassHeight = $basecostRow["glass_size_short"];
                    $baseGlassWidth = $basecostRow["glass_size_long"];
                    $baseImageHeight = $this->getImageDim($basecostRow["glass_size_short"], $matTotalShort);
                    $baseImageWidth = $this->getImageDim($basecostRow["glass_size_long"], $matTotalLong);

                }
                if ((number_format((float)($baseImageHeight / $baseImageWidth), 4) >= $minimumRatio) && (number_format((float)($baseImageHeight / $baseImageWidth), 4) <= $maximumRatio)) {
                    $newSize = (int)$baseGlassWidth . '″×' . (int)$baseGlassHeight . '″';
                    array_push($finalBaseCost, $newSize);
                }

            }
        }
        $defaultSize = $artworkDefaultGlassWidth . '″×' . $artworkDefaultGlassHeight . '″';
        if ($isDefaultMediaTreatment && !in_array($defaultSize, $finalBaseCost)) {
            array_push($finalBaseCost, $defaultSize);
        }
        natsort($finalBaseCost);
        $sortedFinalBasecost = array();
        foreach ($finalBaseCost as $key => $value) {
            $sortedFinalBasecost[] = $finalBaseCost[$key];
        }

        return $sortedFinalBasecost;
    }

}
