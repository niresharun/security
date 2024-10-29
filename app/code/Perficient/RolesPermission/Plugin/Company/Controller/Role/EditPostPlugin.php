<?php
/**
 * Plugin to save company roles permission
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords: Company template for roles permission
 */
declare(strict_types=1);
namespace Perficient\RolesPermission\Plugin\Company\Controller\Role;

use Magento\Company\Controller\Role\EditPost;
use Psr\Log\LoggerInterface;
use Magento\Company\Api\RoleRepositoryInterface;
use Magento\Company\Api\Data\RoleInterfaceFactory;
use Magento\Company\Model\PermissionManagementInterface;
use Perficient\RolesPermission\Model\CompanyRolesFactory;
use Perficient\RolesPermission\Model\CompanyTemplateFactory;
use Perficient\RolesPermission\Helper\Data;

class EditPostPlugin
{
    /**
     * EditPostPlugin constructor.
     * @param LoggerInterface $logger
     * @param RoleRepositoryInterface $roleRepository
     * @param RoleInterfaceFactory $roleFactory
     * @param PermissionManagementInterface $permissionManagement
     * @param CompanyRolesFactory $companyRolesFactory
     * @param CompanyTemplateFactory $companyTemplateFactory
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly RoleInterfaceFactory $roleFactory,
        private readonly PermissionManagementInterface $permissionManagement,
        private readonly CompanyRolesFactory $companyRolesFactory,
        private readonly CompanyTemplateFactory $companyTemplateFactory,
        private readonly Data $helper
    ) {
    }

    /**
     * @param CreatePost $subject
     */
    public function afterExecute(EditPost $subject, $result)
    {
        $id = $subject->getRequest()->getParam('id');
        $roleName = $subject->getRequest()->getParam('role_name');
        $rolePermissions = $subject->getRequest()->getParam('role_permissions');
        try {
            $customRoleCollection = $this->getCustomRoleIdByName($roleName);
            $customRole = $customRoleCollection->getFirstItem()->getData();
            if(isset($customRole['role_id']) && $customRole['role_id'] > 0) {
                $resources = explode(',', (string) $rolePermissions);
                $permissions = $this->permissionManagement->populatePermissions($resources);

                $permissionCollection = $this->getCustomRolePermissions($customRole['role_id']);
                $template = $permissionCollection->toArray();

                /** update custom table perficient_company_templates templates */
                foreach ($permissions as $permission) {
                    $data = [];
                    $templateModel = $this->companyTemplateFactory->create();
                    if ($templateResult = $this->helper->searchInMultiArray($permission->getResourceId(), $template['items'])) {
                        $data['permission_id'] = $templateResult['permission_id'];
                        $data['role_id'] = $customRole['role_id'];
                        $data['resource_id'] = $permission->getResourceId();
                        $data['permission'] = $permission->getPermission();
                        $data['is_update'] = 1;
                    } else {
                        $data['role_id'] = $customRole['role_id'];
                        $data['resource_id'] = $permission->getResourceId();
                        $data['permission'] = $permission->getPermission();
                        $data['is_update'] = 1;
                    }
                    $templateModel->setData($data);
                    $templateModel->save();
                }

                /** Update other company roles permission */
                // this feature handled by cron
            }
            return $result;

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * @param $roleName
     * @return mixed
     */
    private function getCustomRoleIdByName($roleName)
    {
        return $this->companyRolesFactory->create()->getCollection()
            ->addFieldToSelect('role_id')
            ->addFieldtoFilter('role_name', ['eq' => $roleName]);
    }

    /**
     * @param $roleId
     * @return mixed
     */
    private function getCustomRolePermissions($roleId)
    {
        return $this->companyTemplateFactory->create()->getCollection()
            ->addFieldtoFilter('role_id', ['eq' => $roleId]);
    }

}
