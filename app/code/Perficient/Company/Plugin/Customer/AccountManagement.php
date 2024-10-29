<?php
/**
 * Update customer attributes when customer get registered.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Plugin\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Perficient\Company\Helper\Data as CompanyHelper;
use Magento\Company\Api\RoleRepositoryInterface;

/**
 * Plugin for AccountManagement. Saving customer attributes.
 */
class AccountManagement
{
    public function __construct(
        protected \Magento\Framework\App\Request\Http $request,
        protected CompanyHelper                       $companyHelper,
        private readonly RoleRepositoryInterface      $roleRepository
    )
    {
    }

    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface          $customer, $password = null, $redirectUrl = ''
    ): array
    {
        /*To set price multiplier as 1x and surcharge status as yes on registration*/
        $param = $this->request->getParams();
        $priceMultiplier = $customer->getCustomAttribute('price_multiplier');
        $surchargeStatus = $customer->getCustomAttribute('surcharge_status');

        if (empty($priceMultiplier)) {
            $customer->setCustomAttribute('price_multiplier', '1.00');
        }
        if (isset($param['role']) && !empty($param['role'])) {
            $customerRole = $this->roleRepository->get($param['role'])->getRoleName();
            if (empty($surchargeStatus) && CompanyHelper::CUSTOMER_CUSTOMER != $customerRole) {
                $customer->setCustomAttribute('surcharge_status', 1);
            }
        } elseif (empty($surchargeStatus)) {
            $customer->setCustomAttribute('surcharge_status', 1);
        }

        if (isset($param['company']) && !empty($param['company'])) {
            $companyData = $param['company'];
            if (is_array($companyData) && !empty($companyData)) {
                if (isset($companyData['business_type']) && $companyData['business_type'] == CompanyHelper::BUSINESS_TYPE_HEALTHCARE) {
                    $customer->setCustomAttribute('is_b2c_customer', 1);
                    $customer->setCustomAttribute('discount_type', 'post-discounted');
                }
            }
        }
        if ($this->companyHelper->isB2cCustomer()) {
            $customer->setCustomAttribute('price_multiplier', '1.00');
            $customer->setCustomAttribute('is_b2c_customer', 1);
            $customer->setCustomAttribute('discount_type', 'post-discounted');
        }

        return [$customer, $password, $redirectUrl];
    }
}
