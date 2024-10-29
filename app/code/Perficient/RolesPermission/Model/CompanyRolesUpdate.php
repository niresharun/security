<?php
/**
 * Custom company roles permission update via cron
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @keywords: Company role updates for roles permission
 */
declare(strict_types=1);

namespace Perficient\RolesPermission\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Company\Model\PermissionManagementInterface;
use Perficient\RolesPermission\Model\CompanyRolesFactory;
use Psr\Log\LoggerInterface as Logger;
use Perficient\RolesPermission\Model\CompanyTemplateFactory;
use Perficient\RolesPermission\Model\ResourceModel\CompanyTemplate as CompanyTemplateResourceModel;
use Magento\Company\Api\Data\RoleInterfaceFactory;


/**
 * Class CompanyRolesUpdate
 * @package Perficient\RolesPermission\Model
 */
class CompanyRolesUpdate
{
    /**
     * company permission table
     */
    const COMPANY_PERMISSIONS_TABLE = 'company_permissions';

    /**
     * CompanyRolesUpdate constructor.
     * @param ResourceConnection $resourceConnection
     * @param PermissionManagementInterface $permissionManagement
     * @param RoleInterfaceFactory $roleFactory
     */
    public function __construct(
        protected ResourceConnection $resourceConnection,
        protected PermissionManagementInterface $permissionManagement,
        protected CompanyRolesFactory $companyRolesFactory,
        protected CompanyTemplateFactory $companyTemplateFactory,
        protected CompanyTemplateResourceModel $companyTemplateResource,
        protected Logger $logger,
        protected RoleInterfaceFactory $roleFactory
    ) {
    }

    /**
     * updating company permissions table with resource id and permission for all company roles based on the role id
     */
    public function updateCompanyPermissionByQuery()
    {
        //getting available perficient custom company roles
        $existingRoles = $this->getAvailableRoles();
        if($existingRoles) {
            foreach ($existingRoles as $comRole) {
                $updatedRolesPermissions = $this->getCustomRoleUpdatedPermissions($comRole['role_id']);
                // getting all the allowed roles with the required format
                if (!empty($updatedRolesPermissions->getData())) {
                    try {
                        $updatedRolesPermissions = $this->getCustomRoleUpdatedPermissions($comRole['role_id']);
                        $permissions = array_column($updatedRolesPermissions->getData(), 'resource_id');
                        //populating resource permissions for all allow and deny
                        $updatedpermissions = $this->permissionManagement->populatePermissions($permissions);
                        $allowPermission = [];
                        // getting all the role ids by the specific role name like employee or customers customer
                        $roles = $this->getRoleByName($comRole['role_name']);
                        $roleIds = array_column($roles->getData(), 'role_id');
                        $roleIdsInt = array_map('intval', $roleIds);
                        foreach ($updatedpermissions as $key => $updatedpermission) {
                            $allowPermission[$updatedpermission->getPermission()][$key] = $updatedpermission->getResourceId();
                        }
                        // update company permission table with resource permission for all role id specific to this role.
                        $table = self::COMPANY_PERMISSIONS_TABLE;
                        $connection = $this->resourceConnection->getConnection();
                        $data = ["permission"=> new \Zend_Db_Expr('CASE
                        WHEN resource_id IN ("'.implode('", "', $allowPermission["allow"]).'") THEN "allow"
                        WHEN resource_id IN ("'.implode('", "', $allowPermission["deny"]).'") THEN "deny"
                        ELSE permission
                        END')];
                        $where = ['role_id IN (?)' => $roleIdsInt];
                        $connection->update($table, $data, $where);
                        $this->companyTemplateResource->resetCompanyTemplateFlag($comRole['role_id']);
                    } catch (\Exception $e){
                        $this->logger->critical($e);
                    }
                }
            }
        }
    }
    /**
     * @param $role_id
     * @return mixed
     */
    public function getCustomRoleUpdatedPermissions($role_id)
    {
        $companyTemplateUpdates = $this->companyTemplateFactory->create()->getCollection()
            ->addFieldtoFilter('role_id', $role_id)
            ->addFieldtoFilter('permission', ['eq' => 'allow'])
            ->addFieldtoFilter('is_update', ['eq' => 1]);
        return $companyTemplateUpdates;
    }
    /**
     * @return mixed
     */
    protected function getAvailableRoles() {
        $roles = $this->companyRolesFactory->create()->getCollection()->getData();
        return $roles;
    }
    /**
     * @param $roleName
     * @return mixed
     */
    public function getRoleByName($roleName)
    {
        return $this->roleFactory->create()->getCollection()
            ->addFieldToSelect('role_id')
            ->addFieldToSelect('company_id')
            ->addFieldtoFilter('role_name', ['eq' => $roleName]);
    }
}
