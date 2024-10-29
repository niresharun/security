<?php
/**
 * Wendover Custom attributes
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Block\Account\Dashboard;

use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;

/**
 * Block Class for My Dashboard
 */
class DashboardInfo extends Template
{
    const CUSTOMER_CUSTOMER = "Customer's Customer";

    /**
     * DashboardInfo constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $customerSession ,
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     */
    public function __construct(
        Context                                      $context,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Session                     $customerSession,
        private readonly LoggerInterface             $logger,
        private readonly RoleInfo                    $roleInfo,
        private readonly Escaper                     $escaper,
        array                                        $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * Get Wendover Cam Contact
     */
    public function getCamContact(): \Magento\Framework\DataObject|\Magento\Framework\Api\AttributeInterface|string|null
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $camName = $camPhone = $camEmail = '';
        $camDetails = [];
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            if (!empty($customer->getCustomAttribute('cam_name'))) {
                $camName = $customer->getCustomAttribute('cam_name')->getValue();
            }
            if (!empty($customer->getCustomAttribute('cam_phone'))) {
                $camPhone = $customer->getCustomAttribute('cam_phone')->getValue();
            }
            if (!empty($customer->getCustomAttribute('cam_email'))) {
                $camEmail = $customer->getCustomAttribute('cam_email')->getValue();
            }
        }

        $this->logger->debug($customerId . ' ' . $camName);

        $camDetails['name'] = $camName;
        $camDetails['phone'] = $camPhone;
        $camDetails['email'] = $camEmail;
        $camObj = new \Magento\Framework\DataObject();
        $camObj->setData('cam', $camDetails);
        return $camObj;
    }

    /**
     * Get Wendover Sales Representative
     */
    public function getSalesRepresentativeContact(): \Magento\Framework\DataObject|\Magento\Framework\Api\AttributeInterface|string|null
    {
        $customerId = $this->customerSession->getCustomer()->getId();

        $salesRepresentative = $salesrepPhone = $salesrepEmail = '';
        $salesrepDetails = [];
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            if (!empty($customer->getCustomAttribute('sales_rep_name'))) {
                $salesRepresentative = $customer->getCustomAttribute('sales_rep_name')->getValue();
            }
            if (!empty($customer->getCustomAttribute('sales_rep_phone'))) {
                $salesrepPhone = $customer->getCustomAttribute('sales_rep_phone')->getValue();
            }
            if (!empty($customer->getCustomAttribute('sales_rep_email'))) {
                $salesrepEmail = $customer->getCustomAttribute('sales_rep_email')->getValue();
            }
        }
        $this->logger->debug($customerId . ' ' . $salesRepresentative);
        $salesrepDetails['name'] = $salesRepresentative;
        $salesrepDetails['phone'] = $salesrepPhone;
        $salesrepDetails['email'] = $salesrepEmail;
        $salesrepObj = new \Magento\Framework\DataObject();
        $salesrepObj->setData('salesrep', $salesrepDetails);
        return $salesrepObj;
    }

    public function getCurrentUserRole(): mixed
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);

        return $currentUserRole[0] ?? '';
    }
}
