<?php
/**
 * Update Products In bulk
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@Perficient.com>
 * @keywords:  Update Products In bulk
 */
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;

require './app/bootstrap.php';
$defaultConfLabel = [
    'liner_sku' => 'Liner',
    'frame_default_sku' => 'Frame',
    'top_mat_default_sku' => 'Top Mat',
    'bottom_mat_default_sku' => 'Bottom Mat',
    'side-mark' => 'Side Mark',
    'frame_width' => 'Frame Width',
    'item_height' => 'Item Height',
    'item_width' => 'Item Width',
    'medium' => 'Medium',
    'glass_width' => 'Glass Width',
    'glass_height' => 'Glass Height',
    'liner_width' => 'Liner Width',
    'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
    'bottom_mat_size_left' => 'Bottom Mat Size Left',
    'bottom_mat_size_right' => 'Bottom Mat Size Right',
    'bottom_mat_size_top' => 'Bottom Mat Size Top',
    'image_height' => 'Image Height',
    'image_width' => 'Image Width',
    'top_mat_size_bottom' => 'Top Mat Size Bottom',
    'top_mat_size_left' => 'Top Mat Size Left',
    'top_mat_size_right' => 'Top Mat Size Right',
    'top_mat_size_top' => 'Top Mat Size Top',
    'treatment' => 'Treatment',
    'default_frame_depth' => 'Frame Depth',
    'default_liner_depth' => 'Liner Depth',
    'default_frame_color' => 'Frame Color',
    'default_liner_color'=> 'Liner Color',
    'default_top_mat_color'=> 'Top Mat Color',
    'default_bottom_mat_color'=>'Bottom Mat Color'
];
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('crontab');
$configLoader = $objectManager->get('\Magento\Framework\ObjectManager\ConfigLoaderInterface');
$objectManager->configure($configLoader->load('frontend'));
$productCollection = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
// Use factory to create a new product collection
$pc = $productCollection->create();
/** Apply filters here */
$tt = $pc->addAttributeToSelect('*');
$defaultConfigurationAttributes = array_flip($defaultConfLabel);
//$tt = $pc->addAttributeToSelect('*');
foreach ($tt as $product) {
    $defaultConfigurationValues = [];
    if (!empty($product->getData('liner_sku'))) {
        $defaultConfigurationValues['liner_sku'] = $product->getData('liner_sku') . ":--" . array_search('liner_sku', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('frame_default_sku'))) {
        $defaultConfigurationValues['frame_default_sku'] = $product->getData('frame_default_sku') . ":--" . array_search('frame_default_sku', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('top_mat_default_sku'))) {
        $defaultConfigurationValues['top_mat_default_sku'] = $product->getData('top_mat_default_sku') . ":--" . array_search('top_mat_default_sku', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('bottom_mat_default_sku'))) {
        $defaultConfigurationValues['bottom_mat_default_sku'] = $product->getData('bottom_mat_default_sku') . ":--" . array_search('bottom_mat_default_sku', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('side-mark'))) {
        $defaultConfigurationValues['side-mark'] = $product->getData('side-mark') . ":--" . array_search('side-mark', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('frame_width'))) {
        $defaultConfigurationValues['frame_width'] = $product->getData('frame_width') . ":--" . array_search('frame_width', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('item_height'))) {
        $defaultConfigurationValues['item_height'] = $product->getData('item_height') . ":--" . array_search('item_height', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('medium'))) {
        $defaultConfigurationValues['medium'] = $product->getData('medium') . ":--" . array_search('medium', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('glass_width'))) {
        $defaultConfigurationValues['glass_width'] = $product->getData('glass_width') . ":--" . array_search('glass_width', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('glass_height'))) {
        $defaultConfigurationValues['glass_height'] = $product->getData('glass_height') . ":--" . array_search('glass_height', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('bottom_mat_size_bottom'))) {
        $defaultConfigurationValues['bottom_mat_size_bottom'] = $product->getData('bottom_mat_size_bottom') . ":--" . array_search('bottom_mat_size_bottom', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('bottom_mat_size_left'))) {
        $defaultConfigurationValues['bottom_mat_size_left'] = $product->getData('bottom_mat_size_left') . ":--" . array_search('bottom_mat_size_left', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('bottom_mat_size_right'))) {
        $defaultConfigurationValues['bottom_mat_size_right'] = $product->getData('bottom_mat_size_right') . ":--" . array_search('bottom_mat_size_right', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('bottom_mat_size_top'))) {
        $defaultConfigurationValues['bottom_mat_size_top'] = $product->getData('bottom_mat_size_top') . ":--" . array_search('bottom_mat_size_top', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('image_height'))) {
        $defaultConfigurationValues['image_height'] = $product->getData('image_height') . ":--" . array_search('image_height', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('image_width'))) {
        $defaultConfigurationValues['image_width'] = $product->getData('image_width') . ":--" . array_search('image_width', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('top_mat_size_bottom'))) {
        $defaultConfigurationValues['top_mat_size_bottom'] = $product->getData('top_mat_size_bottom') . ":--" . array_search('top_mat_size_bottom', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('top_mat_size_left'))) {
        $defaultConfigurationValues['top_mat_size_left'] = $product->getData('top_mat_size_left') . ":--" . array_search('top_mat_size_left', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('top_mat_size_right'))) {
        $defaultConfigurationValues['top_mat_size_right'] = $product->getData('top_mat_size_right') . ":--" . array_search('top_mat_size_right', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('top_mat_size_top'))) {
        $defaultConfigurationValues['top_mat_size_top'] = $product->getData('top_mat_size_top') . ":--" . array_search('top_mat_size_top', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('treatment'))) {
        $defaultConfigurationValues['treatment'] = $product->getData('treatment') . ":--" . array_search('treatment', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('default_frame_depth'))) {
        $defaultConfigurationValues['default_frame_depth'] = $product->getData('default_frame_depth') . ":--" . array_search('default_frame_depth', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('default_liner_depth'))) {
        $defaultConfigurationValues['default_liner_depth'] = $product->getData('default_liner_depth') . ":--" . array_search('default_liner_depth', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('default_frame_color'))) {
        $defaultConfigurationValues['default_frame_color'] = $product->getData('default_frame_color') . ":--" . array_search('default_frame_color', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('default_liner_color'))) {
        $defaultConfigurationValues['default_liner_color'] = $product->getData('default_liner_color') . ":--" . array_search('default_liner_color', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('default_top_mat_color'))) {
        $defaultConfigurationValues['default_top_mat_color'] = $product->getData('default_top_mat_color') . ":--" . array_search('default_top_mat_color', $defaultConfigurationAttributes);
    }
    if (!empty($product->getData('default_bottom_mat_color'))) {
        $defaultConfigurationValues['default_bottom_mat_color'] = $product->getData('default_bottom_mat_color') . ":--" . array_search('default_bottom_mat_color', $defaultConfigurationAttributes);
    }
    $cleanedJsonString = '';
    if(!empty($defaultConfigurationValues)){
        $cleanedJsonString = str_replace(':--', ':', json_encode($defaultConfigurationValues));
    }


    /* $_product = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface')->get($product->getSku(),true, 0, true);
    $_product->setCustomAttribute('default_configurations', $cleanedJsonString);
    $_product->save($_product);*/

    /*  echo $product->getId().PHP_EOL;
    $_product = $objectManager->create('\Magento\Catalog\Model\Product');
    $productn = $_product->load($product->getId());
    $productn->setCustomAttribute('default_configurations', $cleanedJsonString,0);
    $productn->save();*/

echo $productRowId = $product->getRowId() . PHP_EOL;

$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$entityAttribute = $objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute');
$attributeId = $entityAttribute->getIdByCode('catalog_product', 'default_configurations');

$sqlDelete_one = "Delete FROM catalog_product_entity_text Where attribute_id = $attributeId and store_id =0 and row_id =" . $productRowId;
$sqlDelete_two = "Delete FROM catalog_product_entity_text Where attribute_id = $attributeId and store_id =1 and row_id =" . $productRowId;

$connection->query($sqlDelete_one);
$connection->query($sqlDelete_two);

if(!empty($cleanedJsonString)){
    $sqlInsert_one = "INSERT INTO catalog_product_entity_text (attribute_id, store_id, value,row_id)
         VALUES ($attributeId, 0, '" . $cleanedJsonString . "','" . $productRowId . "')";
    $connection->query($sqlInsert_one);
}
}
