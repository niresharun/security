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

namespace Perficient\Company\Model\Customer;

use Magento\Company\Api\Data\CompanyCustomerInterface;
use Magento\Company\Model\Customer\Company as CoreCompany;
use Magento\Company\Model\ResourceModel\Customer;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\LoginAsCustomerAssistance\Model\SetAssistance;

/**
 * Class for creating new company for customer.
 */
class Company extends CoreCompany
{
    /**
     * @param CompanyCustomerInterface $customerAttributes
     * @param Customer $customerResource
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct (
        private readonly \Magento\Company\Api\Data\CompanyInterfaceFactory $companyFactory,
        private readonly \Magento\Company\Api\CompanyRepositoryInterface   $companyRepository,
        private readonly \Magento\Company\Model\Company\Structure          $companyStructure,
        private readonly CompanyCustomerInterface                          $customerAttributes,
        private readonly Customer                                          $customerResource,
        private readonly GroupManagementInterface                          $groupManagement,
        private readonly SetAssistance                                     $setAssistance
    ) {
        parent::__construct($companyFactory, $companyRepository, $companyStructure, $customerAttributes, $customerResource, $groupManagement);
    }

    /**
     * Create company.
     *
     * @throws LocalizedException
     * @throws InputException
     * @throws CouldNotSaveException
     */
    public function createCompany(CustomerInterface $customer, array $companyData, $jobTitle = null): CompanyInterface
    {
        foreach ($companyData as $key => $value) {
            if (is_array($value)) {
                /*Start fix for WENDOVER-531 parent WNDOVER-501 : Where street0 and street1 was getting
                in same street0 because of ','*/
                if ($key == CompanyInterface::STREET) {
                    $companyData[$key] = trim(implode("\n", $value));
                    continue;
                }
                /*End fix for WENDOVER-531 parent WNDOVER-501*/
                $companyData[$key] = implode(',', $value);
            }
        }
        $companyDataObject = $this->companyFactory->create(['data' => $companyData]);
        if ($companyDataObject->getCustomerGroupId() === null) {
            $companyDataObject->setCustomerGroupId($this->groupManagement->getDefaultGroup()->getId());
        }
        $companyDataObject->setSuperUserId($customer->getId());
        $this->companyRepository->save($companyDataObject);
        $this->customerAttributes
            ->setCompanyId($companyDataObject->getId())
            ->setCustomerId($customer->getId());
        if ($jobTitle) {
            $this->customerAttributes->setJobTitle($jobTitle);
        }
        $this->customerResource->saveAdvancedCustomAttributes($this->customerAttributes);
        if ($customer->getId()) {
            $customerId =  $customer->getId();
            $this->setAssistance->execute( $customerId,true);
        }
        return $companyDataObject;
    }
}
