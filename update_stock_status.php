<?php
/**
 * Update Bulk Product Status
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

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('crontab');
$configLoader = $objectManager->get('\Magento\Framework\ObjectManager\ConfigLoaderInterface');
$objectManager->configure($configLoader->load('frontend'));
$productCollection = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
// Use factory to create a new product collection
$action = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Action');
$pc = $productCollection->create();
/** Apply filters here */ 
$tt = $pc->addAttributeToSelect('*');
$storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
$storeIds = array_keys($storeManager->getStores());
foreach ($tt as $product) {
	if($product->getSku() == 'package'){
		continue;
	}
    $attrSet = $product->getAttributeSetId();
    /*if ($attrSet == '16' || $attrSet == '16') {
        continue;
    }*/
    echo $product->getSku(). PHP_EOL;
    $sourceObj = $objectManager->create('\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory');
    $sourceItem  = $sourceObj->create();
    $sourceItem->setSourceCode('default');
    $sourceItem->setSku($product->getSku());
    $sourceItem->setQuantity(0);
    $sourceItem->setStatus(1);
    $sourceItemSaveObj = $objectManager->create('\Magento\InventoryApi\Api\SourceItemsSaveInterface');
    $sourceItemSaveObj->execute([$sourceItem]);
    foreach ($storeIds as $storeId) {
        $updateAttributes['is_quick_ship'] = 0;
        $action->updateAttributes([$product->getId()], $updateAttributes, $storeId);
    }
}
