<?php
/**
 * Add template company for roles permission
 *
 * @category: Magento
 * @package: Perficient/RolesPermission
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Wendover
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @project: Wendover
 * @keywords: global template of roles permission
 */
declare(strict_types=1);
namespace Perficient\RolesPermission\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Perficient\RolesPermission\Helper\Data;
use Magento\Framework\App\State;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Company\Model\Customer\Company;
use Magento\Company\Api\Data\RoleInterfaceFactory;
use Magento\Company\Api\RoleRepositoryInterface;
use Magento\Company\Model\PermissionManagementInterface;


class CreateCompany implements DataPatchInterface
{
    /**
     * CreateCompany constructor.
     * @param ModuleDataSetupInterface $setup
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param DataObjectHelper $objectHelper
     * @param AccountManagementInterface $customerAccountManagement
     * @param Company $customerCompany
     * @param RoleInterfaceFactory $roleFactory
     * @param RoleRepositoryInterface $roleRepository
     * @param PermissionManagementInterface $permissionManagement
     * @param State $appState
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $setup,
        private readonly CustomerInterfaceFactory $customerDataFactory,
        private readonly DataObjectHelper $objectHelper,
        private readonly AccountManagementInterface $customerAccountManagement,
        private readonly Company $customerCompany,
        private readonly RoleInterfaceFactory $roleFactory,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly PermissionManagementInterface $permissionManagement,
        private readonly State $appState
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        //$this->appState->setAreaCode('global');
        $this->setup->getConnection()->startSetup();

        //$customerData = $this->createCompany();
        //$this->assignRolesPermission($customerData);

        $this->setup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.1';
    }

    private function createCompany()
    {
        $customer = $this->customerDataFactory->create();
        $post = [
            'company' => [
                'company_name' => Data::COMPANY_NAME,
                'legal_name' => Data::COMPANY_NAME,
                'company_email' => Data::COMPANY_EMAIL,
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
            ],
            'customer' => [
                'email' => Data::WENDOVER_COMPANY_ADMIN_EMAIL,
                'firstname' => 'Company',
                'lastname' => 'Admin'
            ]
        ];
        $customerData = $post['customer'];
        $companyData = $post['company'];
        try {

        $this->objectHelper->populateWithArray(
            $customer,
            $customerData,
            \Magento\Customer\Api\Data\CustomerInterface::class
        );
        $customer = $this->customerAccountManagement->createAccount($customer);
        $jobTitle = $companyData['job_title'] ?? null;
        $companyDataObject = $this->customerCompany->createCompany($customer, $companyData, $jobTitle);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
        return ['customer_id' => $customer->getId(), 'company_id' => $companyDataObject->getId()];
    }

    private function assignRolesPermission($companyData)
    {
        /*$adminResource = [
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
            'Magento_PurchaseOrder::view_purchase_orders_for_subordinates',
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
            'Magento_Company::users_edit',
            'Magento_Company::credit',
            'Magento_Company::credit_history'
        ];*/
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

        $customerResource = [];
        /** Company Admin Resource */
        /*$role = $this->roleFactory->create();
        $companyId = $companyData['company_id'];
        $role->setRoleName(Data::CUSTOMER_ADMIN_ROLE_NAME);
        $role->setCompanyId($companyId);
        $role->setPermissions($this->permissionManagement->populatePermissions($adminResource));
        $this->roleRepository->save($role);*/

        /** Company Employee Resource */
        $role = $this->roleFactory->create();
        $companyId = $companyData['company_id'];
        $role->setRoleName(Data::CUSTOMER_EMPLOYEE_ROLE_NAME);
        $role->setCompanyId($companyId);
        $role->setPermissions($this->permissionManagement->populatePermissions($empResource));
        $this->roleRepository->save($role);

        /** Customer's Customer Resource */
        $role = $this->roleFactory->create();
        $companyId = $companyData['company_id'];
        $role->setRoleName(Data::CUSTOMER_CUSTOMER_ROLE_NAME);
        $role->setCompanyId($companyId);
        $role->setPermissions($this->permissionManagement->populatePermissions($customerResource));
        $this->roleRepository->save($role);
    }
}
