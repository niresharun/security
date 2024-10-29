<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */

namespace Perficient\Company\Plugin;

use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Customer\Block\Account\Navigation;
use Magento\Framework\Escaper;

/**
 * Class LinkTitle
 * @package Perficient\Company\Plugin
 */
class LinkTitle
{
    const MAGENTO_TITLE = 'Company Users';
    const COMPANY_EMPLOYEE = 'Manage Customers';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const CUSTOMER_EMPLOYEE = 'Customer Employee';
    const COMPANY_MASTER_ROLE = "Company Administrator";
    const TITLE_EMPLOYEE_LOGINS = "Employee Logins";
    const TITLE_COMPANY_PROFILE = "Company Profile";

    /* Restricted Areas For Customer's Customer*/
    private array $restrictedAreasForCustomersCustomer = [
        'Your Display Information',
        'My Requisition Lists',
        'Address Book',
        'Payment Options',
        'Orders',
        'Shipped Orders',
        'Market Scans',
        'Customers',
        'My Catalogs',
        'Create a Catalog'
    ];

    /**
     * LinkTitle constructor.
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     */
    public function __construct(
        private readonly RoleInfo $roleInfo,
        private readonly Escaper  $escaper
    )
    {
    }

    /**
     * @param Navigation $subject
     * @param $result
     */
    public function afterGetLinks(\Magento\Customer\Block\Account\Navigation $subject, $result): mixed
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if (!isset($currentUserRole[0])) {
            return $result;
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $currentUserRole[0] = html_entity_decode((string)$currentUserRole[0], ENT_QUOTES);
        foreach ($result as $key => $item) {
            $currentLinkTitle = $item->getLabel();
            if ($currentLinkTitle == self::MAGENTO_TITLE) {
                $item->setLabel(self::COMPANY_EMPLOYEE);
            }
            if (isset($currentUserRole[0]) && $currentUserRole[0] == self::CUSTOMER_CUSTOMER &&
                $currentLinkTitle == self::TITLE_COMPANY_PROFILE) {
                unset($result[$key]);
            }
            if (isset($currentUserRole[0]) && $currentUserRole[0] != self::COMPANY_MASTER_ROLE &&
                $currentLinkTitle == self::TITLE_EMPLOYEE_LOGINS) {
                unset($result[$key]);
            }
            if (isset($currentUserRole[0])
                && $currentUserRole[0] == self::CUSTOMER_CUSTOMER
                && in_array($currentLinkTitle, $this->restrictedAreasForCustomersCustomer)) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
