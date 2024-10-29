<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */

namespace Perficient\Company\Model;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Model\RoleManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Api\Data\UserInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Model\ResourceModel\Customer as CustomerResource;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Company\Model\Email\Sender;
use Magento\Company\Model\CompanyManagement as BaseCompanyManagement;
use Perficient\Company\Helper\Data as CompanyHelper;

/**
 * Handle various customer account actions.
 */
class CompanyManagement extends BaseCompanyManagement
{
    /**
     * CompanyManagement constructor.
     * @param CompanyRepositoryInterface $companyRepository
     * @param UserInterfaceFactory $userFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Sender $companyEmailSender
     * @param RoleManagement $roleManagement
     */
    public function __construct(
        private readonly CompanyRepositoryInterface        $companyRepository,
        private readonly UserInterfaceFactory              $userFactory,
        private readonly CustomerRepositoryInterface       $customerRepository,
        private readonly CustomerResource                  $customerResource,
        private readonly PsrLogger                         $logger,
        private readonly Sender                            $companyEmailSender,
        private readonly \Magento\Company\Api\AclInterface $userRoleManagement,
        protected RoleManagement                           $roleManagement
    )
    {
        parent::__construct($companyRepository, $userFactory, $customerRepository, $customerResource, $logger, $companyEmailSender, $userRoleManagement);
    }

    /**
     * {@inheritdoc}
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function assignCustomer($companyId, $customerId): void
    {
        $customer = $this->customerRepository->getById($customerId);
        if ($customer->getExtensionAttributes() !== null
            && $customer->getExtensionAttributes()->getCompanyAttributes() !== null) {
            $companyAttributes = $customer->getExtensionAttributes()->getCompanyAttributes();
            $companyAttributes->setCustomerId($customerId);
            $companyAttributes->setCompanyId($companyId);
            $this->customerResource->saveAdvancedCustomAttributes($companyAttributes);
            $company = $this->companyRepository->get($companyId);
            if ($customer->getId() != $company->getSuperUserId()) {
                // logic to retain the customer existing role when changing company association
                $userRoles = $this->userRoleManagement->getRolesByUserId($customerId);
                $userRoleName = null;
                foreach ($userRoles as $customerRole) {
                    $userRoleName = $customerRole->getRoleName();
                    break;
                }
                if ($userRoleName) {
                    // get available roles for the company
                    $roles = $this->roleManagement->getRolesByCompanyId($companyId, false);
                    $currentCustomerRole = [];
                    foreach ($roles as $role) {
                        if ($role->getRoleName() == $userRoleName) {
                            $currentCustomerRole = $role;
                        }
                    }
                    if (!empty($currentCustomerRole)) {
                        $this->userRoleManagement->assignRoles($customerId, [$currentCustomerRole]);
                    } else {
                        $this->userRoleManagement->assignUserDefaultRole($customerId, $companyId);
                    }
                } else {
                    $this->userRoleManagement->assignUserDefaultRole($customerId, $companyId);
                }
                $this->companyEmailSender->sendCustomerCompanyAssignNotificationEmail($customer, $companyId);
            }
        }
    }
}
