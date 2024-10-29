<?php

namespace DCKAP\Productimize\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as EavCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

class CustomProductCollection extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $productFactory;
    protected $productCollectionFactory;
    protected $eavCollectionFactory;
    protected $eavConfig;
    protected $directory;
    protected $storeManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        CollectionFactory $productCollectionFactory,
        EavCollectionFactory $eavCollectionFactory,
        Config $eavConfig,
        DirectoryList $directory,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ){
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->eavCollectionFactory = $eavCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->directory = $directory;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    public function getProductimizeJsonPath () {
        $pubFolderPath = $this->directory->getPath('pub');
        $productimizeJsonFolder = $pubFolderPath . '/productimize_json/';
        if (!file_exists($productimizeJsonFolder)) {
            mkdir($productimizeJsonFolder, 0777, true);
        }
        return $productimizeJsonFolder;
    }



    public function getProductImages($product,$productImgType)  {
        $productimages = array();
        $productimages = $product->getMediaGalleryImages();
        $finalProduct = array();
        foreach($productImgType as $key=>$value)    {
            $cornerImg = $productimages->getItemByColumnValue('label', $key);
            if($cornerImg)  {
                $finalProduct[$value] = $cornerImg->getUrl();
            }
        }
        return $finalProduct;
    }

    public function getProductMediaUrl()    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
    }

    public function getFrameCollection()
    {

        try {
            $optionId1 = $this->eavConfig->getAttribute('catalog_product', 'frame_type')->getSource()->getOptionId('Liner');
            $optionId2 = $this->eavConfig->getAttribute('catalog_product', 'frame_type')->getSource()->getOptionId('Edge Treatment');
            $optionId = [$optionId1, $optionId2];

            $productCollection = $this->getProductCollection('Frame', 'frame_type', $optionId, 'frame');

            // Call Liner Data
            $this->getLinerCollection();

            // Call edge treatment data
            $this->getEdgeTreatmentCollection();

            $finalProducts = array();
            foreach ($productCollection as $item) {
                $product = $this->productFactory->create()->load($item->getId());

                $cornerImage = ($product->getRendererCorner() && $product->getRendererCorner() != "no_selection")
                    ? $this->normaliseProductImagePath($product->getRendererCorner()) : null;
                $lengthImage = ($product->getRendererLength() && $product->getRendererLength() != "no_selection")
                    ? $this->normaliseProductImagePath($product->getRendererLength()) : null;
                $thumbImage = ($product->getThumbnail() && $product->getThumbnail() != "no_selection")
                    ? $this->normaliseProductImagePath($product->getThumbnail()) : null;
                (!empty($height))? (!empty($dimension)? ' x ':'').$height . '"h':'';

                if ($cornerImage && $lengthImage && $thumbImage) {
                    $finalProduct = array();
                    $finalProduct['renderCornerImage'] = $this->getProductMediaUrl() . $cornerImage;
                    $finalProduct['renderLengthImage'] = $this->getProductMediaUrl() . $lengthImage;
                    $finalProduct['thumbnail'] = $this->getProductMediaUrl() . $thumbImage;
                    $finalProduct['specificationImage'] = $product->getSpecDetails() ?
                        $this->getProductMediaUrl() . $this->normaliseProductImagePath($product->getSpecDetails()) : null;

                    $finalProduct['m_sku'] = $item->getSku();
                    $finalProduct['m_name'] = $item->getName();
                    $finalProduct['m_status'] = $item->getStatus();
                    $itemFrameType = $product->getResource()->getAttribute('frame_type')->getFrontend()->getValue($product);//$this->getAttributeValue($item, 'frame_type');

                    $finalProduct['m_frame_type'] = isset($itemFrameType) ? $itemFrameType : null;
                    $finalProduct['m_frame_width'] = $item->getFrameWidth();

                    $finalProduct['m_frame_depth'] = $item->getFrameDepth();
                    $finalProduct['m_frame_rabbet_depth'] = $item->getFrameRabbetDepth();

                    $finalProduct['m_max_outer_size'] = $item->getMaxOuterSize();
                    $finalProduct['m_moulding_waste_pct'] = $item->getMouldingWastePct();
                    $finalProduct['m_landed_cost_per_foot'] = $item->getFrameRabbetDepth();

                    $itemColorFrame = $product->getResource()->getAttribute('color_frame')->getFrontend()->getValue($product);//$this->getAttributeValue($item, 'color_frame');
                    $finalProduct['m_color_frame'] = isset($itemColorFrame) ? $itemColorFrame : null;

                    $itemColorFamily = $product->getResource()->getAttribute('color_family_frame')->getFrontend()->getValue($product);//$this->getAttributeValue($item, 'color_family');
                    $finalProduct['m_color_family'] = isset($itemColorFamily) ? $itemColorFamily : null;

                    $itemColorFamilyFrame = $product->getResource()->getAttribute('color_family_frame')->getFrontend()->getValue($product);//$this->getAttributeValue($item, 'color_family_frame');
                    $finalProduct['m_frame_family'] = isset($itemColorFamilyFrame) ? $itemColorFamilyFrame : null;

                    $itemFrameWidthRange = $product->getResource()->getAttribute('frame_width_range')->getFrontend()->getValue($product);//$this->getAttributeValue($item, 'frame_width_range');
                    $finalProduct['m_frame_width_range'] = isset($itemFrameWidthRange) ? $itemFrameWidthRange : null;

                    $itemShowWithLiner = $product->getResource()->getAttribute('show_with_liners')->getFrontend()->getValue($product);//$this->getAttributeValue($item, 'show_with_liners');

                    $finalProduct['m_show_with_liners'] = isset($itemShowWithLiner) ? $itemShowWithLiner : null;
                    $finalProducts[$finalProduct['m_sku']] = $finalProduct;
                }
            }

            $productimizeJsonFolder = $this->getProductimizeJsonPath();
            $file = $productimizeJsonFolder . 'frame.json';

            file_put_contents($file, json_encode($finalProducts));

        } catch (\Exception $e){
            $this->logger->info($e->getMessage());
        }
    }

    public function getProductCollection($attrSetName, $attrCode, $optionId, $type)
    {
        $attributeSetCollection = $this->eavCollectionFactory->create();
        $attributeSetCollection->addFieldToFilter('entity_type_id', 4)->addFieldToFilter('attribute_set_name', $attrSetName);
        $attrSet = current($attributeSetCollection->getData());
        $attributeSetId = $attrSet["attribute_set_id"];

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setFlag('has_stock_status_filter', false)
            ->addAttributeToSelect("*");

        if ($type == 'frame') {
            $productCollection->addFieldToFilter($attrCode, array('nin' => $optionId));
        } else if ($type == 'liner') {
            $productCollection->addFieldToFilter($attrCode, array('eq' => $optionId));
        } else if ($type == 'mat') {
            $productCollection->addFieldToFilter($attrCode, array('in' => $optionId));
        } else if ($type == 'edge_treatment') {
            $productCollection->addFieldToFilter($attrCode, array('eq' => $optionId));
        }

        if ($type === 'edge_treatment') {
            $productCollection->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        } else {
            $productCollection->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG);
        }

        return $productCollection;
    }

    public function getAttributeValue($product, $attrCode)
    {
        $productFactory = $this->productFactory->create()->load($product->getId());
        return $productFactory->getResource()->getAttribute($attrCode)->getFrontend()->getValue($productFactory);
    }

    public function getMatCollection()
    {
        $optionId1 = $this->eavConfig->getAttribute('catalog_product', 'filter_size')->getSource()->getOptionId('Standard');
        $optionId2 = $this->eavConfig->getAttribute('catalog_product', 'filter_size')->getSource()->getOptionId('Oversized');
        $optionId = [$optionId1, $optionId2];

        $productCollection = $this->getProductCollection('Mat', 'filter_size', $optionId, 'mat');
        $finalProducts = array();
        foreach ($productCollection as $item) {
            $product = $this->productFactory->create()->load($item->getId());

            $baseImage = ($product->getImage() && $product->getImage() != "no_selection") ? $this->normaliseProductImagePath($product->getImage()) : null;
            $thumbImage = ($product->getThumbnail() && $product->getThumbnail() != "no_selection") ? $this->normaliseProductImagePath($product->getThumbnail()) : null;

            if ($baseImage && $thumbImage) {
                $finalProduct = array();
                $finalProduct['renderDisplayImage'] = $this->getProductMediaUrl() . $thumbImage;
                $finalProduct['rendererImage'] =  $this->getProductMediaUrl() . $baseImage;
                $finalProduct['m_sku'] = $item->getSku();
                $finalProduct['m_name'] = $item->getName();
                $finalProduct['m_status'] = $item->getStatus();
                $finalProduct['m_filter_size'] = $this->getAttributeValue($item, 'filter_size');
                $finalProduct['m_mat_type'] = $this->getAttributeValue($item, 'filter_type');
                $finalProduct['m_color_mat'] = $this->getAttributeValue($item, 'color_mat');
                $finalProduct['m_color_family'] = $this->getAttributeValue($item, 'color_family_mat');
                $finalProduct['m_filter_thickness'] = $this->getAttributeValue($item, 'filter_thickness');
                $finalProducts[$finalProduct['m_sku']] = $finalProduct;
            }
        }
        $productimizeJsonFolder = $this->getProductimizeJsonPath();
        $file = $productimizeJsonFolder . 'mat.json';

        file_put_contents($file, json_encode($finalProducts));
    }

    public function getLinerCollection()
    {
        $optionId = $this->eavConfig->getAttribute('catalog_product', 'frame_type')->getSource()->getOptionId('Liner');
        $productCollection = $this->getProductCollection('Frame', 'frame_type', $optionId, 'liner');
        $finalProducts = array();

        foreach ($productCollection as $item) {

            $product = $this->productFactory->create()->load($item->getId());


            $cornerImage = ($product->getRendererCorner() && $product->getRendererCorner() != "no_selection")
                ? $this->normaliseProductImagePath($product->getRendererCorner()) : null;
            $lengthImage = ($product->getRendererLength() && $product->getRendererLength() != "no_selection")
                ? $this->normaliseProductImagePath($product->getRendererLength()) : null;
            $thumbImage = ($product->getThumbnail() && $product->getThumbnail() != "no_selection") ?
                $this->normaliseProductImagePath($product->getThumbnail()) : null;

            if ($cornerImage && $lengthImage && $thumbImage) {

                $finalProduct = array();
                $finalProduct['renderCornerImage'] = $this->getProductMediaUrl() . $cornerImage;
                $finalProduct['renderLengthImage'] = $this->getProductMediaUrl() . $lengthImage;
                $finalProduct['thumbnail'] = $this->getProductMediaUrl() . $thumbImage;
                $finalProduct['specificationImage'] = $product->getSpecDetails()
                    ? $this->getProductMediaUrl() . $this->normaliseProductImagePath($product->getSpecDetails()) : null;

                $finalProduct['m_sku'] = $item->getSku();
                $finalProduct['m_name'] = $item->getName();
                $finalProduct['m_liner_type'] = $this->getAttributeValue($item, 'frame_type');
                $finalProduct['m_liner_width'] = $item->getFrameWidth();
                $finalProduct['m_liner_depth'] = $item->getFrameDepth();
                $finalProduct['m_liner_rabbet_depth'] = $item->getFrameRabbetDepth();
                $finalProduct['m_color_liner'] = $this->getAttributeValue($item, 'color_frame');
                $finalProduct['m_color_family'] = $this->getAttributeValue($item, 'color_family_frame');
                $finalProducts[$finalProduct['m_sku']] = $finalProduct;
            }
        }

        $productimizeJsonFolder = $this->getProductimizeJsonPath();
        $file = $productimizeJsonFolder . 'liner.json';
        file_put_contents($file, json_encode($finalProducts));
    }
    public function getEdgeTreatmentCollection()
    {
        $optionId = $this->eavConfig->getAttribute('catalog_product', 'frame_type')->getSource()->getOptionId('Edge Treatment');
        $productCollection = $this->getProductCollection('Frame', 'frame_type', $optionId, 'edge_treatment');
        $finalProducts = array();

        foreach ($productCollection as $item) {

            $product = $this->productFactory->create()->load($item->getId());
            $finalProduct = array();

            $finalProduct['renderLengthImage'] = $product->getRendererLength()
                ? $this->getProductMediaUrl().$this->normaliseProductImagePath($product->getRendererLength()) : null;
            $finalProduct['m_sku'] = $item->getSku();
            $finalProduct['m_name'] = $item->getName();
            $finalProduct['m_type'] = $this->getAttributeValue($item, 'frame_type');
            $finalProduct['m_width'] = $item->getFrameWidth();
            $finalProduct['m_depth'] = $item->getFrameDepth();
            $finalProducts[$finalProduct['m_sku']] = $finalProduct;
        }
        $productimizeJsonFolder = $this->getProductimizeJsonPath();
        $file = $productimizeJsonFolder . 'edge_treatment.json';
        file_put_contents($file, json_encode($finalProducts));
    }

    /**
     * @param $name
     *
     * @return string|void
     */
    public function normaliseProductImagePath($name) {
        if (!empty($name)) {
            $nameArray = explode("/", $name);
            $nameSize = count($nameArray);
            $imageName = $nameArray[$nameSize-1];
            array_pop($nameArray);
            $url = strtolower(implode("/", $nameArray));
            return $url."/".$imageName;
        }

        return $name;
    }
}
