<?php

/**
 * Update Bulk Product Status
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@Perficient.com>
 * @keywords:  Update Products In bulk
 *
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
$CustomerCollection = $objectManager->create('\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory');
$setAssistance = $objectManager->create('\Magento\LoginAsCustomerAssistance\Model\SetAssistance');
$resourceConnection = $objectManager->create('\Magento\Framework\App\ResourceConnection');
$logger = $objectManager->create('\Psr\Log\LoggerInterface');
$registry = [];
$customerData = $CustomerCollection->create();

//Get customer collection

/** Apply filters here */
$customerList = $customerData->addAttributeToSelect('*');
$storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
$storeIds = array_keys($storeManager->getStores());
$total_insert =0;

try {
    foreach ($customerList as $customer) {
        $customerId = $customer->getEntityId();
                    $connection = $resourceConnection->getConnection();
                    $tableName = $resourceConnection->getTableName('login_as_customer_assistance_allowed');

                    $insertQuery = $connection->insertOnDuplicate(
                                $tableName,
                                [
                                    'customer_id' => $customerId
                                ]
                            );
                    if($insertQuery){
                        $total_insert++;
                    }
    }

    $logger->critical("Customer Total Count -". $customerList->count());
    $logger->critical(" Insert Total Count -". $total_insert);

} catch (\Exception $e) {
    $logger->critical($e->getMessage());
}
