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

namespace Perficient\MyDisplayInformation\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Perficient\MyDisplayInformation\Helper\Data;

/**
 * Class CustomSection
 * @package Perficient\MyDisplayInformation\CustomerData
 */
class CustomSection implements SectionSourceInterface
{
    const COMPANY_EMPLOYEE = 'Customer Employee';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const COMPANY_ADMINISTRATOR = "Company Administrator";
    const PREVIEW = 'self';

    /**
     * CustomSection constructor.
     */

    public function __construct(
       private readonly Data $data
    ) {
    }

    /**
     * @return array
     */
    public function getSectionData()
    {
        $currentUserRole = $this->data->getCurrentUserRole();
        if (isset($currentUserRole[0])){
            if ($currentUserRole[0] == self::COMPANY_EMPLOYEE || $currentUserRole[0] == self::COMPANY_ADMINISTRATOR) {
                return [
                    'header_mydisplayinformation' => '',
                    'body_mydisplayinformation' => '',
                    'footer_mydisplayinformation' => '',
                ];
            }
        }
        return [
            'header_mydisplayinformation' => $this->data->getCurrentUserParentHeaderMsg(),
            'body_mydisplayinformation' => $this->data->getCurrentUserWelcomeMsg(),
            'footer_mydisplayinformation' => $this->data->getCurrentUserParentFooterMsg(),
        ];
    }
}
