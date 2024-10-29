<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Plugin;

use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class LinkTitle
 * @package Perficient\MyDisplayInformation\Plugin
 */
class LinkTitle
{
    const TITLE_MY_DISPLAY_INFORMATION = "My Display Information";
    const CUSTOMER_CUSTOMER = "Customer's Customer";

    /**
     * LinkTitle constructor.
     */
    public function __construct(
        private readonly RoleInfo $roleInfo,
        private readonly Escaper $escaper,
        private readonly CustomerSession $customerSession
    )
    {
    }

    /**
     * @param \Magento\Customer\Block\Account\Navigation $subject
     * @param $result
     * @return mixed
     */
    public function afterGetLinks(\Magento\Customer\Block\Account\Navigation $subject, $result)
    {

        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if (isset($currentUserRole[0])) {
            $currentUserRoleText = html_entity_decode((string) $currentUserRole[0], ENT_QUOTES);
        }
        $isB2Customer = $this->customerSession->getIsBtocCustomer();
        foreach ($result as $key => $item) {
            $currentLinkTitle = $item->getLabel();
            if (((isset($currentUserRoleText) &&
                        $currentUserRoleText == self::CUSTOMER_CUSTOMER) || $isB2Customer
                )
                && $currentLinkTitle == self::TITLE_MY_DISPLAY_INFORMATION) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
