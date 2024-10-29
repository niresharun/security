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

namespace Perficient\RolesPermission\Model\ResourceModel\CompanyRoles;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perficient\RolesPermission\Model\CompanyRoles;
use Perficient\RolesPermission\Model\ResourceModel\CompanyRoles as ResourceModelCompanyRoles;

/**
 * Class Collection
 * @package Perficient\RolesPermission\Model\ResourceModel\CompanyRoles
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'role_id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            CompanyRoles::class,
            ResourceModelCompanyRoles::class
        );
    }
}
