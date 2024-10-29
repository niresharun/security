<?php
/**
 * Create Company and assign to company admin
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Shajitha Banu <shajitha.banu@Perficient.com>
 * @keywords:  Create Company and assign to company admin
 */
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Api\Data\RoleInterfaceFactory;
use Magento\Company\Api\RoleRepositoryInterface;
use Magento\Company\Model\PermissionManagementInterface;

require './app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('global');

$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$companyAdminEmail = "company.admin@wendover.com";

// Get Customer Details

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);

$companyAdminUser = $customerRepository->get($companyAdminEmail);

$companyModel = $objectManager->create('Magento\Company\Model\Customer\Company');


if($companyAdminUser) {
    // Create New Company
    $validCompanyData = [
        'company_name' => 'Wendover Company',
        'legal_name' => 'Wendover Company',
        'company_email' => 'company@wendover.com',
        'job_title' => '',
        'street' => [
            0 => 'West Wendover',
            1 => ''
        ],
        'city' => 'Nevada',
        'country_id' => 'US',
        'region_id' => 2,
        'region' => '',
        'postcode' => '89883',
        'telephone' => '0123456789'
    ];

    $company = $companyModel->createCompany($companyAdminUser, $validCompanyData);
    echo "Company Created - ".$company->getId().PHP_EOL;
}
// Assigning Roles
if($company->getId()){
    $companyId = $company->getId();
    $customerResource = [];
    $empResource = [
        'Magento_Company::index',
        'Magento_Sales::all',
        'Magento_Sales::place_order',
        'Magento_Sales::payment_account',
        'Magento_Sales::view_orders',
        'Magento_Sales::view_orders_sub',
        'Magento_NegotiableQuote::all',
        'Magento_NegotiableQuote::view_quotes',
        'Magento_NegotiableQuote::manage',
        'Magento_NegotiableQuote::checkout',
        'Magento_NegotiableQuote::view_quotes_sub',
        'Magento_PurchaseOrder::all',
        'Magento_PurchaseOrder::view_purchase_orders',
        'Magento_PurchaseOrder::view_purchase_orders_for_company',
        'Magento_PurchaseOrder::autoapprove_purchase_order',
        'Magento_PurchaseOrderRule::super_approve_purchase_order',
        'Magento_PurchaseOrderRule::view_approval_rules',
        'Magento_PurchaseOrderRule::manage_approval_rules',
        'Magento_Company::view',
        'Magento_Company::view_account',
        'Magento_Company::edit_account',
        'Magento_Company::view_address',
        'Magento_Company::edit_address',
        'Magento_Company::contacts',
        'Magento_Company::payment_information',
        'Magento_Company::user_management',
        'Magento_Company::users_view',
        'Magento_Company::credit',
        'Magento_Company::credit_history'
    ];


    /** Company Employee Resource */

    $roleFactory = $objectManager->create('Magento\Company\Api\Data\RoleInterfaceFactory');
    $role = $roleFactory->create();
    $permissionManagement = $objectManager->get('Magento\Company\Model\PermissionManagementInterface');

    $role->setRoleName('Customer Employee');
    $role->setCompanyId($companyId);
    $role->setPermissions($permissionManagement->populatePermissions($empResource));
    $role->save();

    /** Customer's Customer Resource */
    $roleCustomer = $roleFactory->create();
    $roleCustomer->setRoleName("Customer's Customer");
    $roleCustomer->setCompanyId($companyId);
    $roleCustomer->setPermissions($permissionManagement->populatePermissions($customerResource));
    $roleCustomer->save();
    echo "Role Assigned Successfully!!".PHP_EOL;
}

echo "Success".PHP_EOL;



