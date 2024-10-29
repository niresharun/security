<?php
/**
 * Admin configuration of GoogleTagManager
 * @category: Magento
 * @package: Perficient/GoogleTagManager
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_GoogleTagManager
 */
declare(strict_types=1);

namespace Perficient\GoogleTagManager\Plugin;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Model\Session;
use Magento\GoogleTagManager\Block\Ga;

/**
 * Plugin class to modify the Order Data
 */
class GtmPurchase
{
    /**
     * GtmPurchase constructor.
     */

    public function __construct(
        private readonly CompanyManagementInterface $companyManagement,
        private readonly Session $customerSession){
    }

    /**
     * @param array $result
     * @return array
     */
    public function afterGetOrdersDataArray(Ga $subject, $result)
    {
        if(empty($result) || !is_array($result)) {
            return $result;
        }

        $businessType = $this->getBusinessType();
        foreach ($result as $index => $data) {
            $result[$index]['ecommerce']['purchase']['actionField']['business_type'] = $businessType;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getBusinessType() {
        $customerId = $this->customerSession->getCustomer()->getId();
        if(empty($customerId)) {
            return '';
        }

        $company = $this->companyManagement->getByCustomerId($customerId);
        if(empty($company)) {
            return '';
        }

        return $company->getBusinessType();
    }
}
