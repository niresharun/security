<?php
/**
 * Custom company roles permission
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords: Company template for roles permission
 */
declare(strict_types=1);

namespace Perficient\RolesPermission\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CompanyTemplate
 * @package Perficient\RolesPermission\Model\ResourceModel
 */
class CompanyTemplate extends AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('perficient_company_templates', 'permission_id');
    }

    /**
     * @param $roleId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resetCompanyTemplateFlag($roleId)
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();
        $data = ["is_update"=>0];
        $where = ['role_id = ?' => (int)$roleId];
        $result = $connection->update($tableName, $data, $where);
        return $result;
    }
}
