<?php
/**
 * Model for custom company roles permission
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
use Perficient\RolesPermission\Model\ResourceModel\CompanyTemplate as ResourceCompanyTemplate;

/**
 * Class CompanyTemplate
 * @package Perficient\RolesPermission\Model
 */
class CompanyTemplate extends AbstractModel
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceCompanyTemplate::class);
    }

}
