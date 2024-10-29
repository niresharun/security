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
namespace Perficient\RolesPermission\Model;

use Magento\Framework\Model\AbstractModel;
use Perficient\RolesPermission\Model\ResourceModel\CompanyRoles as ResourceCompanyRoles;

class CompanyRoles extends AbstractModel
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceCompanyRoles::class);
    }

}