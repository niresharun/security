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
declare(strict_types=1);

namespace Perficient\Company\Observer;

use Magento\Backend\Model\Session;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Perficient\Company\Helper\Data as CompanyHelper;
use Perficient\Company\Plugin\Company\Model\Action\SaveCustomer;
use Magento\Company\Model\RoleManagement;
use Magento\Company\Model\UserRoleManagement;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\App\ResourceConnection;
use Perficient\Rabbitmq\Model\CompanyUpdateMagentoToErp;
use Psr\Log\LoggerInterface;
use Perficient\Rabbitmq\Model\CompanyUpdate as CompanyUpdateSync;

/**
 * Observer for adminhtml_company_save_after event. Save additional fields of form.
 */
class SaveCustomCompanyAttributes implements ObserverInterface
{
    const ATTRIBUTE_STATUS_INACTIVE = 'no';

    protected array $attributesToUpdate = ['discount_type'];

    /**
     * SaveCustomCompanyAttributes constructor.
     * @param CompanyRepositoryInterface $companyRepository
     * @param RoleManagement $roleManagement
     * @param UserRoleManagement $userRoleManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupManagement $groupManagement
     * @param ResourceConnection $resourceConnection
     * @param CompanyUpdateMagentoToErp $companyUpdateMagentoToErp
     * @param Session $_session
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CompanyRepositoryInterface  $companyRepository,
        private readonly RoleManagement              $roleManagement,
        private readonly UserRoleManagement          $userRoleManagement,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly GroupManagement             $groupManagement,
        private readonly ResourceConnection          $resourceConnection,
        private readonly CompanyUpdateMagentoToErp   $companyUpdateMagentoToErp,
        protected Session                            $_session,
        protected LoggerInterface                    $logger,
        protected CompanyUpdateSync                  $companyUpdateSync
    )
    {
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $params = $observer->getRequest()->getParams();
        $company = $observer->getCompany();

        // Unset the taxvat field.
        if (isset($params['company_admin']) && isset($params['company_admin']['taxvat'])) {
            unset($params['company_admin']['taxvat']);
        }

        if (!empty($params['information'])) {
            //Save Company Data
            $companyInfo = $params['information'];
            $customFields = ['newsletter', 'is_dba', 'first_name',
                'last_name', 'dba_name', 'resale_certificate_number',
                'website_address', 'social_media_site', 'business_type', 'no_of_stores', 'sq_ft_per_store',
                'type_of_projects', 'no_of_jobs_per_year', 'discount_rate', 'mark_pos', 'discount_markup', 'discount_application_type', 'discount_value', 'syspro_customer_id'];
            foreach ($customFields as $attribute) {
                if (array_key_exists($attribute, $companyInfo)) {
                    $value = $companyInfo[$attribute];
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $company->setData($attribute, $value);
                } else {
                    $company->setData($attribute, self::ATTRIBUTE_STATUS_INACTIVE);

                }
            }
            $this->companyRepository->save($company);

            //Customer Saving Logic Below
            //For some reason syspro_customer_id was not saving so added this hack.
            //Ideally everything should be pulled from company and stored against child customer.
            //But since site is live not making major changes and adding above hack.

            //Get role ids form company id
            $compUsers = $companyEmployee = $companyCustomersCustomers = [];
            $customersCustomerRoleId = null;
            $employeeRoleId = null;
            $companyRoles = $this->roleManagement->getRolesByCompanyId($company->getId());
            foreach ($companyRoles as $companyRole) {
                if ($companyRole->getRoleName() == SaveCustomer::CUSTOMER_EMPLOYEE) {
                    $employeeRoleId = $companyRole->getId();
                    if ($employeeRoleId) {
                        $companyEmployee = $this->userRoleManagement->getUsersByRoleId($employeeRoleId);
                    }
                } elseif ($companyRole->getRoleName() == SaveCustomer::CUSTOMER_CUSTOMER) {
                    $customersCustomerRoleId = $companyRole->getId();
                    if ($customersCustomerRoleId) {
                        $companyCustomersCustomers = $this->userRoleManagement->getUsersByRoleId($customersCustomerRoleId);
                    }
                }
            }
            $compUsers = array_merge($companyEmployee, $companyCustomersCustomers);

            //Get customer id from role id
            $customersCustomerIds = [];
            if ((is_countable($companyCustomersCustomers) ? count($companyCustomersCustomers) : 0) > 0) {
                foreach ($companyCustomersCustomers as $companyCustomersCustomer) {
                    $customersCustomerIds[] = $companyCustomersCustomer->getId();
                }
            }

            //Get company admin user
            $companyUsers = [];
            $updatedCompany = $this->companyRepository->get($company->getId());
            $companyAdmin = $this->customerRepository->getById($updatedCompany->getSuperUserId());
            $companyAdmin->setCustomAttribute('syspro_customer_id', $updatedCompany->getSysproCustomerId());
            $this->customerRepository->save($companyAdmin);

            //Combined list of company admin, employee and customer's customer
            $companyUsers[] = $companyAdmin;
            if (count($compUsers) > 0) {
                foreach ($compUsers as $companyUser) {
                    $companyUsers[] = $this->customerRepository->getById($companyUser->getId());
                }
            }

            //Updating employee customers with specific attribute values same as company admin
            //if ($employeeRoleId) {
            //$employeeUsers = $this->userRoleManagement->getUsersByRoleId($employeeRoleId);
            //$companyAdmin = $this->customerRepository->getById($company->getSuperUserId());
            if (count($companyUsers) > 0) {
                foreach ($companyUsers as $compUser) {
                    //$compUser = $this->customerRepository->getById($companyUser->getId());
                    //$logger->info('customer id = '.$compUser->getId());
                    if ($companyAdmin && $compUser) {
                        try {
                            // getting attributes list same as syspro sync attributes list
                            $attributesSync = $this->companyUpdateSync->customerAttributes;
                            $attributesToUpdate = array_merge($attributesSync, $this->attributesToUpdate);
                            foreach ($attributesToUpdate as $attribute) {
                                //$logger->info($attribute);
                                if (
                                    in_array($attribute, $this->companyUpdateSync->skipCustomerCustomerAttr) &&
                                    in_array($compUser->getId(), $customersCustomerIds)
                                ) {
                                    //$logger->info('in continue');
                                    continue;
                                } elseif ('vat_tax_id' == $attribute) {
                                    $compUser->setTaxvat($companyAdmin->getTaxvat());
                                } elseif ('customer_group_id' == $attribute) {
                                    $compUser->setGroupId($companyAdmin->getGroupId());
                                } else {
                                    $attrValue = $companyAdmin->getCustomAttribute($attribute);
                                    if (!empty($attrValue)) {
                                        //$logger->info($attrValue->getValue());
                                        $compUser->setCustomAttribute($attribute, $attrValue->getValue());
                                        if ($attribute == 'is_b2c_customer' && $attrValue->getValue() == '1') {
                                            $compUser->setCustomAttribute('discount_type', 'post-discounted');
                                        }
                                    } else {
                                        $compUser->setCustomAttribute($attribute, '');
                                    }
                                }
                            }
                            $this->customerRepository->save($compUser);
                            //$logger->info('-------------------------');
                        } catch (LocalizedException $exception) {
                            $this->logger->error($exception);
                        }
                    }

                }
            }
            //}
        }
        /**
         * set employee role for previous admin user if company admin changed
         */
        $previousAdminId = $this->_session->getData('previousCompanyAdminId');
        try {
            if ($previousAdminId != '' && $previousAdminId != $company->getSuperUserId()) {
                $roles = $this->roleManagement->getRolesByCompanyId($company->getId(), false);
                $currentCustomerRole = [];
                foreach ($roles as $role) {
                    if ($role->getRoleName() == CompanyHelper::COMPANY_EMPLOYEE) {
                        $currentCustomerRole = $role;
                        break;
                    }
                }
                if (!empty($currentCustomerRole)) {
                    $this->userRoleManagement->assignRoles($previousAdminId, [$currentCustomerRole]);
                }
            }
        } catch (\Exception) {
            $outputStr = 'User - ' . $previousAdminId . ' Role is not assigned properly';
            $this->logger->critical($outputStr);
        }
        if ($this->_session->getData('previousCompanyAdminId')) {
            $this->_session->setData('previousCompanyAdminId', '');
        }
        /**
         * Send the company information from Magento to ERP.
         */
        $this->companyUpdateMagentoToErp->updateCompanyDataFromMagentoToERP($company);
    }
}
