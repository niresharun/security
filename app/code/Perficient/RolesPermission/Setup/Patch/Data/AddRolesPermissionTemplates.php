<?php
/**
 * Add global template of roles permission
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
use Perficient\RolesPermission\Model\CompanyRolesFactory;

class AddRolesPermissionTemplates implements DataPatchInterface
{
    /**
     * AddRolesPermissionTemplates constructor.
     * @param CompanyRolesFactory $companyRolesFactory
     * @param ModuleDataSetupInterface $setup
     */
    public function __construct(
        CompanyRolesFactory $companyRolesFactory,
        private readonly ModuleDataSetupInterface $setup
    ) {
        $this->companyRolesFactory = $companyRolesFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->setup->getConnection()->startSetup();
        $this->insertDefaultValuesInCompanyRolesTable($this->setup);
        $this->insertDefaultValuesInCompanyTemplateTable($this->setup);
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
        return '2.0.0';
    }

    /**
     * @param $setup
     */
    private function insertDefaultValuesInCompanyRolesTable($setup)
    {
        $conn = $setup->getConnection();
        $tableName = $setup->getTable(Data::TABLE_PERFICIENT_COMPANY_ROLES);

        $data = [
            //['role_name' => Data::CUSTOMER_ADMIN_ROLE_NAME],
            ['role_name' => Data::CUSTOMER_CUSTOMER_ROLE_NAME],
            ['role_name' => Data::CUSTOMER_EMPLOYEE_ROLE_NAME]
        ];
        foreach($data as $row) {
            $conn->insert($tableName, $row);
        }

    }

    /**
     * @param $setup
     */
    private function insertDefaultValuesInCompanyTemplateTable($setup)
    {
        $conn = $setup->getConnection();
        $tableName = $setup->getTable(Data::TABLE_PERFICIENT_COMPANY_TEMPLATES);

        $roles = $this->companyRolesFactory->create();
        $rolesCollection = $roles->getCollection();
        foreach($rolesCollection->getData() as $rolesData)
        {
            /*if ($rolesData['role_name'] == Data::CUSTOMER_ADMIN_ROLE_NAME) {
                $data = [
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::index', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::all', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::place_order', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::payment_account', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::view_orders', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::view_orders_sub', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::all', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::view_quotes', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::manage', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::checkout', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::view_quotes_sub', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::all', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders_for_subordinates', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders_for_company', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::autoapprove_purchase_order', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::super_approve_purchase_order', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::view_approval_rules', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::manage_approval_rules', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view_account', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::edit_account', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view_address', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::edit_address', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::contacts', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::payment_information', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::user_management', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::roles_view', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::roles_edit', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::users_view', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::users_edit', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::credit', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::credit_history', 'permission' => 'allow']

                ];
                foreach($data as $row) {
                    $conn->insert($tableName, $row);
                }
            }*/
            if ($rolesData['role_name'] == Data::CUSTOMER_EMPLOYEE_ROLE_NAME) {
                $data = [
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::index', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::all', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::place_order', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::payment_account', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::view_orders', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::view_orders_sub', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::all', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::view_quotes', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::manage', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::checkout', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::view_quotes_sub', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::all', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders_for_subordinates', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders_for_company', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::autoapprove_purchase_order', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::super_approve_purchase_order', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::view_approval_rules', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::manage_approval_rules', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view_account', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::edit_account', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view_address', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::edit_address', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::contacts', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::payment_information', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::user_management', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::roles_view', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::roles_edit', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::users_view', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::users_edit', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::credit', 'permission' => 'allow'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::credit_history', 'permission' => 'allow']

                ];
                foreach($data as $row) {
                    $conn->insert($tableName, $row);
                }

            }
            if ($rolesData['role_name'] == Data::CUSTOMER_CUSTOMER_ROLE_NAME) {
                $data = [
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::index', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::all', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::place_order', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::payment_account', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::view_orders', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Sales::view_orders_sub', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::all', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::view_quotes', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::manage', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::checkout', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_NegotiableQuote::view_quotes_sub', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::all', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders_for_subordinates', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::view_purchase_orders_for_company', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrder::autoapprove_purchase_order', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::super_approve_purchase_order', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::view_approval_rules', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_PurchaseOrderRule::manage_approval_rules', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view_account', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::edit_account', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::view_address', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::edit_address', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::contacts', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::payment_information', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::user_management', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::roles_view', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::roles_edit', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::users_view', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::users_edit', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::credit', 'permission' => 'deny'],
                    [ 'role_id' => $rolesData['role_id'], 'resource_id' => 'Magento_Company::credit_history', 'permission' => 'deny']

                ];
                foreach($data as $row) {
                    $conn->insert($tableName, $row);
                }
            }
        }
    }

}
