<?php
/**
 * Update Products In bulk
 * @copyright: Copyright © 2021 Perficient, Inc. All rights reserved.
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
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

$sqlGetCustomerEmployeeUserEditCurrentPermission = "SELECT permission_id FROM `company_permissions` WHERE role_id 
IN (SELECT role_id FROM `company_roles` WHERE role_name = 'Customer Employee') AND resource_id = 'Magento_Company::users_edit'";

$customerEmployeeUserEditCurrentPermissionData = $connection->fetchCol($sqlGetCustomerEmployeeUserEditCurrentPermission);

$customerEmployeeUserEditCurrentPermissionString = implode(',', $customerEmployeeUserEditCurrentPermissionData);
$sqlUpdateCustomerEmployeeUserEditPermission = "UPDATE `company_permissions` SET permission = 'allow' WHERE permission_id IN (" . $customerEmployeeUserEditCurrentPermissionString . ")";

$connection->query($sqlUpdateCustomerEmployeeUserEditPermission);
