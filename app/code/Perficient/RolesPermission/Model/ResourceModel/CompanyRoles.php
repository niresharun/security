<?php
/**
 * Created by PhpStorm.
 * User: sandeep.mude
 * Date: 28-08-2020
 * Time: 12:39 PM
 */
declare(strict_types=1);

namespace Perficient\RolesPermission\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CompanyRoles extends AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('perficient_company_roles', 'role_id');
    }

}