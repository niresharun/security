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

namespace Perficient\Company\Plugin\Company\Model\Action;

use Magento\Company\Api\RoleRepositoryInterface as MagentoRolesFactory;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Company\Block\Company\CompanyProfile;
use Magento\Company\Model\Action\SaveCustomer as ParentClass;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use \Magento\Company\Model\ResourceModel\UserRole\CollectionFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Perficient\Company\Ui\Component\Listing\Column\CompanyUsersActions;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Customer\Model\GroupManagement;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Model\RoleManagement;
use Magento\Company\Model\UserRoleManagement;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Perficient\Rabbitmq\Model\CompanyUpdate as CompanyUpdateSync;


/**
 * Class SaveCustomer
 * @package Perficient\Company\Plugin\Company\Model\Action
 */
class SaveCustomer
{
    const STATUS = 1;
    const COMPANY_ADMINISTRATOR = 'Company Administrator';
    const CUSTOMER_EMPLOYEE = 'Customer Employee';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const CUSTOMER_ADMIN = 'Customer Admin';

    /**
     * @var array
     */
    protected $attributesToUpdate = ['discount_type'];
    protected $attributesForCustomers = ['syspro_customer_id'];

    /**
     * SaveCustomer constructor.
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Session $currentCustomerSession
     * @param CompanyProfile $companyProfile
     * @param CollectionFactory $userRoleCollectionFactory
     * @param CompanyRepositoryInterface $companyRepository
     * @param GroupManagement $groupManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param RoleManagement $roleManagement
     * @param UserRoleManagement $userRoleManagement
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly RoleInfo                    $roleInfo,
        private readonly Escaper                     $escaper,
        private readonly MagentoRolesFactory         $magentoRolesFactory,
        private readonly SearchCriteriaBuilder       $searchCriteriaBuilder,
        private readonly Session                     $currentCustomerSession,
        private readonly CompanyProfile              $companyProfile,
        private readonly CollectionFactory           $userRoleCollectionFactory,
        private readonly CompanyUsersActions         $companyUsersActions,
        CompanyRepositoryInterface                   $companyRepository,
        private readonly GroupManagement             $groupManagement,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly RoleManagement              $roleManagement,
        private readonly UserRoleManagement          $userRoleManagement,
        private readonly ResourceConnection          $resourceConnection,
        private readonly LoggerInterface             $logger,
        protected CompanyUpdateSync                  $companyUpdateSync
    )
    {
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws LocalizedException
     */
    public function beforeCreate(ParentClass $subject, RequestInterface $request)
    {
        $this->currentCustomerSession->setData('assignCustomerEmailArea', 'frontend');
        return $this->modifyFormRequest($subject, $request);
    }


    /**
     * @param $result
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterCreate(ParentClass $subject, $result)
    {
        $companyData = $this->companyProfile->getCustomerCompany();
        $employeeRoleId = $customerRoleId = null;
        $employeeUsers = $customerUsers = null;
        $companyRoles = $this->roleManagement->getRolesByCompanyId($companyData->getId());
        foreach ($companyRoles as $companyRole) {
            if ($companyRole->getRoleName() == SaveCustomer::CUSTOMER_EMPLOYEE) {
                $employeeRoleId = $companyRole->getId();
                continue;
            }
            if ($companyRole->getRoleName() == SaveCustomer::CUSTOMER_CUSTOMER) {
                $customerRoleId = $companyRole->getId();
                continue;
            }
        }
        if ($employeeRoleId) {
            $employeeUsers = $this->userRoleManagement->getUsersByRoleId($employeeRoleId);
        }
        if ($customerRoleId) {
            $customerUsers = $this->userRoleManagement->getUsersByRoleId($customerRoleId);
        }
        // get company admin attribute details to map
        $superUserId = $companyData->getSuperUserId();
        $companyAdmin = $this->customerRepository->getById($superUserId);
        foreach ($employeeUsers as $employeeUser) {
            if ($result->getId() == $employeeUser->getId()) {
                $customer = $this->customerRepository->getById($result->getId());
                if ($customer && $companyAdmin) {
                    try {
                        // getting attributes list same as syspro sync attributes list
                        $attributesSync = $this->companyUpdateSync->customerAttributes;
                        $attributesToUpdate = array_merge($attributesSync, $this->attributesToUpdate);
                        foreach ($attributesToUpdate as $attribute) {
                            if ('vat_tax_id' == $attribute) {
                                $customer->setTaxvat($companyAdmin->getTaxvat());
                            } elseif ('customer_group_id' == $attribute) {
                                $customer->setGroupId($companyAdmin->getGroupId());
                            } else {
                                $attrValue = $companyAdmin->getCustomAttribute($attribute);
                                if (!empty($attrValue)) {
                                    $customer->setCustomAttribute($attribute, $attrValue->getValue());
                                }
                            }
                        }
                        $this->customerRepository->save($customer);
                    } catch (LocalizedException $exception) {
                        $this->logger->error($exception);
                    }
                }
            }
        }
        // update syspro customer id to customers customer
        foreach ($customerUsers as $custUser) {
            if ($result->getId() == $custUser->getId()) {
                $customer = $this->customerRepository->getById($result->getId());
                if ($customer && $companyAdmin) {
                    try {
                        foreach ($this->attributesForCustomers as $attribute) {
                            $attrValue = $companyAdmin->getCustomAttribute($attribute);
                            if (!empty($attrValue)) {
                                $customer->setCustomAttribute($attribute, $attrValue->getValue());
                            }
                        }
                        $this->customerRepository->save($customer);
                    } catch (LocalizedException $exception) {
                        $this->logger->error($exception);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $customerId
     * @return CustomerInterface|null
     */
    private function getCustomerById($customerId): ?CustomerInterface
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (LocalizedException $exception) {
            $customer = null;
            $this->logger->error($exception);
        }

        return $customer;
    }

    public function getCompanyById(int $companyId)
    {
        try {
            $company = $this->companyRepository->get($companyId);
        } catch (NoSuchEntityException) {
            $company = null;
        }
        return $company;
    }

    /**
     * @param RequestInterface $request
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function modifyFormRequest(ParentClass $subject, RequestInterface $request): array
    {
        $currentLoggedInUserId = $this->currentCustomerSession->getCustomerData()->getId();
        $existingRequest = $request->getParams();
        $getCurrentCompanyRolesArray = [];
        $currentUserCompanyId = $this->companyProfile->getCustomerCompany()->getId();
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter(
            'company_id',
            $currentUserCompanyId,
            'eq'
        )->create();
        $getCurrentCompanyRoles = $this->magentoRolesFactory->getList($searchCriteriaBuilder)
            ->getItems();
        foreach ($getCurrentCompanyRoles as $key => $value) {
            $getCurrentCompanyRolesArray[$value->getRoleName()] = $value->getId();
        }
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if ($currentUserRole[0] == self::CUSTOMER_EMPLOYEE) {
            $existingRequest['role'] = $getCurrentCompanyRolesArray[self::CUSTOMER_CUSTOMER];
        }
        $checkForUserTypeLink = $this->companyUsersActions->checkForUserTypeLink();
        if ($checkForUserTypeLink === false) {
            $existingRequest['role'] = $getCurrentCompanyRolesArray[self::CUSTOMER_CUSTOMER];
        } else {
            $existingRequest['role'] = $getCurrentCompanyRolesArray[self::CUSTOMER_EMPLOYEE];
        }
        $existingRequest['extension_attributes']['company_attributes']['status'] = self::STATUS;
        if (isset($existingRequest['customer_id'])
            && !empty($existingRequest['customer_id'])) {
            $userRoleCollection = $this->userRoleCollectionFactory->create();
            $userRoleCollection->addFieldToFilter('user_id', ['eq' => $existingRequest['customer_id']])->load();
            $userRoles = $userRoleCollection->getItems();
            foreach ($userRoles as $userRole) {
                $existingRequest['role'] = $userRole->getRoleId();
            }
        } else {
            $existingRequest['user_actual_parent_id'] = $currentLoggedInUserId;
        }
        $request->setParams($existingRequest);
        return [$request];
    }

    /**
     * @param RequestInterface $request
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeUpdate(ParentClass $subject, RequestInterface $request): array
    {
        return $this->modifyFormRequest($subject, $request);
    }
}
