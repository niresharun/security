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

namespace Perficient\Company\Plugin\Company\Model;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Api\CompanyRepositoryInterface as CompanyRepositoryModel;
use Magento\Company\Api\Data\CompanyExtensionFactory;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Company\Api\RoleRepositoryInterface as MagentoRolesFactory;
use Magento\Company\Model\Company;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Perficient\RolesPermission\Model\CompanyRolesFactory;
use Perficient\RolesPermission\Model\CompanyTemplateFactory;
use Magento\Company\Api\Data\RoleInterfaceFactory;
use Magento\Company\Api\RoleRepositoryInterface;
use Magento\Company\Model\PermissionManagementInterface;
use Magento\Newsletter\Model\SubscriptionManager as SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * CompanyRepository plugin for saving purchase order company config
 */
class CustomAttributes
{
    const CUSTOMER_EMPLOYEE = 'Customer Employee';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const CUSTOMER_ADMIN = 'Customer Admin';
    const ALLOW = 'allow';
    const DO_SUBSCRIBE = 'yes';
    const COMPANY_ADMINISTRATOR = 'Company Administrator';
    const AREA_CODE = \Magento\Framework\App\Area::AREA_ADMINHTML;

    /**
     * Constants for business types.
     */
    const TYPE_RETAILER = 'Retailer';
    const TYPE_RETAILER_INTERIOR = 'Retailer + Interior Design';

    /**
     * Business types.
     */
    private array $businessTypes = [
        'Designer',
        'Commercial FF&E',
        'Commercial Purchasing Firm',
        'Commercial Property Owner',
    ];

    /**
     * CustomAttributes constructor.
     * @param CompanyRolesFactory $companyRolesFactory
     * @param CompanyTemplateFactory $companyTemplateFactory
     * @param RoleRepositoryInterface $magentoRolesFactory
     * @param RoleInterfaceFactory $roleFactory
     * @param RoleRepositoryInterface $roleRepository
     * @param PermissionManagementInterface $permissionManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param Http $request
     * @param State $state
     */
    public function __construct(
        private readonly CompanyRepositoryInterface    $companyRepository,
        private readonly CompanyRolesFactory           $companyRolesFactory,
        private readonly CompanyTemplateFactory        $companyTemplateFactory,
        private readonly MagentoRolesFactory           $magentoRolesFactory,
        private readonly RoleInterfaceFactory          $roleFactory,
        private readonly RoleRepositoryInterface       $roleRepository,
        private readonly PermissionManagementInterface $permissionManagement,
        private readonly SearchCriteriaBuilder         $searchCriteriaBuilder,
        private readonly SubscriberFactory             $subscriberFactory,
        protected StoreManagerInterface                $storeManager,
        protected Session                              $customerSession,
        private readonly RoleInfo                      $roleInfo,
        private readonly Escaper                       $escaper,
        private readonly Http                          $request,
        private readonly State                         $state
    )
    {
    }

    /**
     * @param Company $company
     * @throws CouldNotSaveException
     */
    public function beforeSave(CompanyRepositoryModel $subject, Company $company): array
    {
        $extensionAttributes = $company->getExtensionAttributes();
        if ($extensionAttributes) {
            // Newsletter
            if (!empty($extensionAttributes->getNewsletter())
                && empty($company->getNewsletter())) {
                $company->setNewsletter($extensionAttributes->getNewsletter());
            } else {
                // throw new CouldNotSaveException(__('Could not save company. Please select newsletter.'));
            }

            // Is DBA
            if (!empty($extensionAttributes->getIsDba())
                && empty($company->getIsDba())) {
                // DBA Name
                if (!empty($extensionAttributes->getDbaName())
                    && empty($company->getDbaName())) {
                    $company->setDbaName($extensionAttributes->getDbaName());
                } else if (!empty($extensionAttributes->getIsDba())
                    && strtolower((string)$extensionAttributes->getIsDba()) == 'no') {
                    $company->setDbaName('');
                } else {
                    //  throw new CouldNotSaveException(__('Could not save company. Please enter company DBA name.'));
                }
                $company->setIsDba($extensionAttributes->getIsDba());
            } else {
                // throw new CouldNotSaveException(__('Could not save company. Please select whether company have DBA.'));
            }
            if (!empty($company->getIsDba()) && strtolower((string)$company->getIsDba()) == 'no') {
                $company->setDbaName('');
            }

            // Resale Certificate Number
            // Commented for ticket WENDM2-2333 : Empty/Blank value not updating for Resale Certificate Number
            /*if (!empty($extensionAttributes->getResaleCertificateNumber())
                && empty($company->getResaleCertificateNumber())) {
                $company->setResaleCertificateNumber($extensionAttributes->getResaleCertificateNumber());
            } else {
               // throw new CouldNotSaveException(__('Could not save company. Please enter resale certificate number.'));
            }*/

            // Firstname
            if (!empty($extensionAttributes->getFirstName())
                && empty($company->getFirstName())) {
                $company->setFirstName($extensionAttributes->getFirstName());
            } else {
                //  throw new CouldNotSaveException(__('Could not save company. Please enter first name.'));
            }

            // Lastname
            if (!empty($extensionAttributes->getLastName())
                && empty($company->getLastName())) {
                $company->setLastName($extensionAttributes->getLastName());
            } else {
                //  throw new CouldNotSaveException(__('Could not save company. Please enter last name.'));
            }

            // Business Type
            $businessType = $extensionAttributes->getBusinessType();
            if (!empty($businessType)
                && empty($company->getBusinessType())) {
                $company->setBusinessType($businessType);
            } else {
                // throw new CouldNotSaveException(__('Could not save company. Please select business type.'));
            }

            if (self::TYPE_RETAILER == $businessType || self::TYPE_RETAILER_INTERIOR == $businessType) {
                // Number of Stores
                if (!empty($extensionAttributes->getNoOfStores())
                    && empty($company->getNoOfStores())) {
                    $company->setNoOfStores($extensionAttributes->getNoOfStores());
                } else {
                    //  throw new CouldNotSaveException(__('Could not save company. Please enter number of stores.'));
                }

                // Square Footage per Store
                if (!empty($extensionAttributes->getSqFtPerStore())
                    && empty($company->getSqFtPerStore())) {
                    $company->setSqFtPerStore($extensionAttributes->getSqFtPerStore());
                } else {
                    /* throw new CouldNotSaveException(
                         __('Could not save company. Please enter square footage per store.')
                     );
                     */
                }

                if (self::TYPE_RETAILER_INTERIOR == $businessType) {
                    // Type of Project
                    if (!empty($extensionAttributes->getTypeOfProjects())
                        && empty($company->getTypeOfProjects())) {
                        $company->setTypeOfProjects($extensionAttributes->getTypeOfProjects());
                    } else {
                        //  throw new CouldNotSaveException(__('Could not save company. Please select types of projects.'));
                    }

                    // Number of Jobs per Year
                    if (!empty($extensionAttributes->getNoOfJobsPerYear())
                        && empty($company->getNoOfJobsPerYear())) {
                        $company->setNoOfJobsPerYear($extensionAttributes->getNoOfJobsPerYear());
                    } else {
                        /* throw new CouldNotSaveException(
                             __('Could not save company. Please select number of jobs per year.')
                         );
                         */
                    }
                }
            }

            if (in_array($businessType, $this->businessTypes)) {
                // Type of Project
                if (!empty($extensionAttributes->getTypeOfProjects())
                    && empty($company->getTypeOfProjects())) {
                    $company->setTypeOfProjects($extensionAttributes->getTypeOfProjects());
                } else {
                    // throw new CouldNotSaveException(__('Could not save company. Please select types of projects.'));
                }

                // Number of Jobs per Year
                if (!empty($extensionAttributes->getNoOfJobsPerYear())
                    && empty($company->getNoOfJobsPerYear())) {
                    $company->setNoOfJobsPerYear($extensionAttributes->getNoOfJobsPerYear());
                } else {
                    /* throw new CouldNotSaveException(
                         __('Could not save company. Please select number of jobs per year.')
                     ); */
                }
            }

            // Social Media Site
            if (!empty($extensionAttributes->getSocialMediaSite())
                && empty($company->getSocialMediaSite())) {
                $company->setSocialMediaSite($extensionAttributes->getSocialMediaSite());
            }

            // Website Address
            if (!empty($extensionAttributes->getWebsiteAddress())
                && empty($company->getWebsiteAddress())) {
                $company->setWebsiteAddress($extensionAttributes->getWebsiteAddress());
            }
        }
        return [$company];
    }

    /**
     * @param Company $company
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterSave(CompanyRepositoryModel $subject, $result, Company $company)
    {
        $areaCode = $this->state->getAreaCode();
        $currentStoreId = (int)$this->storeManager->getStore()->getId();
        if ($areaCode == self::AREA_CODE) {
            $currentCustomerId = $company->getSuperUserId();
            $this->subscriberStatusUpdate($company, $currentCustomerId, $currentStoreId);

            return $result;
        }
        $addRole = [ucwords(self::CUSTOMER_EMPLOYEE) => self::CUSTOMER_EMPLOYEE
            , ucwords(self::CUSTOMER_CUSTOMER) => self::CUSTOMER_CUSTOMER
            , ucwords(self::CUSTOMER_ADMIN) => self::CUSTOMER_ADMIN];
        $companyId = $company->getId();
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter(
            'company_id',
            $companyId,
            'eq'
        )->create();
        $getCompanyRoles = $this->magentoRolesFactory->getList($searchCriteriaBuilder);
        if ($getCompanyRoles->getTotalCount() > 0) {
            foreach ($getCompanyRoles->getItems() as $key => $value) {
                if (in_array($value->getRoleName(), $addRole)) {
                    unset($addRole[$value->getRoleName()]);
                }
            }
            if (is_array($addRole) && !empty($addRole)) {
                foreach ($addRole as $key => $role) {
                    $roleData = $this->getCustomRoleIdByName($role);
                    $roleData = $roleData->getData();
                    if (isset($roleData[0]) && isset($roleData[0]['role_id'])) {
                        $wndRoleId = $roleData[0]['role_id'];
                        $resources = $this->getCustomRolePermissions($wndRoleId);
                        $role = $this->roleFactory->create();
                        $role->setRoleName($key);
                        $role->setCompanyId($companyId);
                        if (is_array($resources) && !empty($resources)) {
                            $role->setPermissions($this->permissionManagement->populatePermissions($resources));
                        }
                        $this->roleRepository->save($role);
                    }
                }
            }
        }

        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if (isset($currentUserRole[0]) && $currentUserRole[0] == self::COMPANY_ADMINISTRATOR) {
            $currentCustomerId = $this->customerSession->getCustomer()->getId();

            $this->subscriberStatusUpdate($company, $currentCustomerId, $currentStoreId);
        }

        return $result;
    }


    public function subscriberStatusUpdate($company, $currentCustomerId, $currentStoreId)
    {
        if ($company->getNewsletter() == self::DO_SUBSCRIBE) {
            $this->subscriberFactory->subscribeCustomer((int)$currentCustomerId, (int)$currentStoreId);
        } else {
            $this->subscriberFactory->unsubscribeCustomer((int)$currentCustomerId, (int)$currentStoreId);
        }
    }

    /**
     * @param $roleName
     */
    private function getCustomRoleIdByName($roleName): mixed
    {
        return $this->companyRolesFactory->create()->getCollection()
            ->addFieldToSelect('role_id')
            ->addFieldtoFilter('role_name', ['eq' => $roleName]);
    }

    /**
     * @param $roleId
     */
    private function getCustomRolePermissions($roleId): array
    {
        $allowedResources = $this->companyTemplateFactory->create()->getCollection()
            ->addFieldToSelect('resource_id')
            ->addFieldtoFilter('role_id', ['eq' => $roleId])
            ->addFieldtoFilter('permission', ['eq' => self::ALLOW]);
        $allowedArea = $allowedResources->getData();
        $assignRoles = [];
        if (is_array($allowedArea) && !empty($allowedArea)) {
            foreach ($allowedArea as $value) {
                $assignRoles[] = $value['resource_id'];
            }
        }
        return $assignRoles;
    }

    /**
     * @param $resultCompany
     */
    public function afterGetList(
        CompanyRepositoryInterface $subject,
                                   $resultCompany
    ): mixed
    {
        /** @var CompanyInterface $company */
        foreach ($resultCompany->getItems() as $company) {
            $this->afterGet($subject, $company);
        }
        return $resultCompany;
    }

    /**
     * @param CompanyInterface $resultEntity
     */
    public function afterGet(
        CompanyRepositoryInterface $subject,
        CompanyInterface           $resultEntity
    ): CompanyInterface
    {
        /** @var CompanyInterface $extensionAttributes */
        $extensionAttributes = $resultEntity->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->companyExtensionFactory->create();
        }
        $extensionAttributes->setNewsletter($resultEntity->getNewsletter());
        $extensionAttributes->setIsDba($resultEntity->getIsDba());
        $extensionAttributes->setFirstName($resultEntity->getFirstName());
        $extensionAttributes->setLastName($resultEntity->getLastName());
        $extensionAttributes->setDbaName($resultEntity->getDbaName());
        $extensionAttributes->setResaleCertificateNumber($resultEntity->getResaleCertificateNumber());
        $extensionAttributes->setWebsiteAddress($resultEntity->getWebsiteAddress());
        $extensionAttributes->setSocialMediaSite($resultEntity->getSocialMediaSite());

        /**
         * Fixes for issue #WENDM2-603
         */
        // Business Type
        $businessType = $resultEntity->getBusinessType();
        $extensionAttributes->setBusinessType($businessType);

        if (self::TYPE_RETAILER == $businessType || self::TYPE_RETAILER_INTERIOR == $businessType) {
            // Number of Stores
            $extensionAttributes->setNoOfStores($resultEntity->getNoOfStores());

            // Square Footage per Store
            $extensionAttributes->setSqFtPerStore($resultEntity->getSqFtPerStore());

            if (self::TYPE_RETAILER_INTERIOR == $businessType) {
                // Type of Project
                $extensionAttributes->setTypeOfProjects($resultEntity->getTypeOfProjects());

                // Number of Jobs per Year
                $extensionAttributes->setNoOfJobsPerYear($resultEntity->getNoOfJobsPerYear());
            }
        }

        if (in_array($businessType, $this->businessTypes)) {
            // Type of Project
            $extensionAttributes->setTypeOfProjects($resultEntity->getTypeOfProjects());

            // Number of Jobs per Year
            $extensionAttributes->setNoOfJobsPerYear($resultEntity->getNoOfJobsPerYear());
        }


        $resultEntity->setExtensionAttributes($extensionAttributes);

        return $resultEntity;
    }
}
