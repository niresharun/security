<?php
/**
 * options for user role column in customer grid
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Customer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Perficient\RolesPermission\Model\CompanyRoles;

/**
 * Class UserRoles
 * @package Perficient\Customer\Model\Config\Source
 */
class UserRoles implements OptionSourceInterface
{
    /**
     * UserRoles constructor.
     */
    public function __construct(
        protected CompanyRoles $companyRoles
    )
    {

    }
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    public function getOptions(): array
    {
        $companyRoles = $this->companyRoles->getCollection();
        $rolesArr = [];
        foreach ($companyRoles as $role) {
            $rolesArr[$role['role_name']] = $role['role_name'];
        }
        return $rolesArr;
    }
}
