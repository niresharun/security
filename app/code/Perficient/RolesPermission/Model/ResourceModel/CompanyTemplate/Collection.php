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

namespace Perficient\RolesPermission\Model\ResourceModel\CompanyTemplate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perficient\RolesPermission\Model\CompanyTemplate;
use Perficient\RolesPermission\Model\ResourceModel\CompanyTemplate as ResourceModelCompanyTemplate;

/**
 * Class Collection
 * @package Perficient\RolesPermission\Model\ResourceModel\CompanyTemplate
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            CompanyTemplate::class,
            ResourceModelCompanyTemplate::class
        );
    }
}
