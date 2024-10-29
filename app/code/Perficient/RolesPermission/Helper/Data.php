<?php
/**
 * This helper file contains the common code.
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

namespace Perficient\RolesPermission\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const TABLE_PERFICIENT_COMPANY_TEMPLATES= 'perficient_company_templates';
    const TABLE_PERFICIENT_COMPANY_ROLES= 'perficient_company_roles';
    const SUPER_COMPANY_WENDOVER = 'Wendover';

    const CUSTOMER_ADMIN_ROLE_NAME = 'Customer Admin';
    const CUSTOMER_EMPLOYEE_ROLE_NAME = 'Customer Employee';
    const CUSTOMER_CUSTOMER_ROLE_NAME = "Customer's Customer";

    const WENDOVER_COMPANY_ADMIN_EMAIL = 'company.admin@wendover.com';
    const COMPANY_NAME = 'Wendover Company';
    const COMPANY_EMAIL = 'company@wendover.com';

    /**
     * @param $needle
     * @param $haystack
     * @param bool $strict
     * @return bool
     */
    public function searchInMultiArray($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->searchInMultiArray($needle, $item, $strict))) {
                return $item;
            }
        }
        return false;
    }




}