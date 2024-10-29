<?php
/**
 * Module to customize customer related features
 *
 * @category: PHP
 * @package: Perficient/Customer
 * @copyright:
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suraj Jaiswal <suraj.jaiswal@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Customer
 */

declare(strict_types=1);

namespace Perficient\Customer\Block;

use Exception;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Company\Model\CountryInformationProvider;
use Magento\Customer\Helper\Address;
use Magento\Framework\DataObject;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Perficient\Company\Model\Config\Source\BusinessType;
use Perficient\Company\Model\Config\Source\Projects;
use Perficient\Company\Model\Config\Source\NumberOfJobsPerYear;

/**
 * Class RequiredDetails
 * @package Perficient\Customer\Block
 */
class RequiredDetails extends \Magento\Framework\View\Element\Template
{
    /**
     * RequiredDetails constructor.
     *
     * @param Context $context
     * @param CountryInformationProvider $countryInformationProvider
     * @param Address $addressHelper ,
     * @param CompanyManagementInterface $companyManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param BusinessType $businessType
     * @param Projects $projects
     * @param NumberOfJobsPerYear $numberOfJobsPerYear
     */
    public function __construct(
        Context                                      $context,
        private readonly CountryInformationProvider  $countryInformationProvider,
        private readonly Address                     $addressHelper,
        private readonly CompanyManagementInterface  $companyManagement,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CustomerInterfaceFactory    $customerDataFactory,
        private readonly BusinessType                $businessType,
        private readonly Projects                    $projects,
        private readonly NumberOfJobsPerYear         $numberOfJobsPerYear,
        array                                        $data = []
    )
    {
        $data['directoryDataHelper'] = ObjectManager::getInstance()->get(Data::class);
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * get customer id
     */
    public function getCustomerId()
    {
        return $this->getRequest()->getParam('cid', null);
    }

    /**
     * get company by customer id
     */
    public function getCompany(): ?\Magento\Company\Api\Data\CompanyInterface
    {
        $company = null;
        $customerId = $this->getRequest()->getParam('cid', null);

        if ($customerId) {
            $company = $this->companyManagement->getByCustomerId($customerId);
        }

        return $company;
    }


    /**
     * Get config
     */
    public function getConfig(string $path): ?string
    {
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve form posting url
     *
     */
    public function getPostActionUrl(): string
    {
        return $this->getUrl('*/account/requireddetailspost');
    }

    /**
     * Get countries list
     */
    public function getCountriesList(): array
    {
        return $this->countryInformationProvider->getCountriesList();
    }

    /**
     * Retrieve form data
     *
     * @throws Exception
     */
    public function getFormData(): mixed
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $customerId = $this->getRequest()->getParam('cid', null);
            $customer = $this->customerRepository->getById($customerId);

            $data = new DataObject();
            $data->setData('email', $customer->getEmail());
            $data->setData('firstname', $customer->getFirstname());
            $data->setData('lastname', $customer->getLastname());

            $this->setData('form_data', $data);
        }

        return $data;
    }

    /**
     * Get default country id
     *
     */
    public function getDefaultCountryId(): string
    {
        return $this->_scopeConfig->getValue(
            'general/country/default',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get attribute validation class
     *
     * @throws Exception
     */
    public function getAttributeValidationClass(string $attributeCode): string
    {
        return $this->addressHelper->getAttributeValidationClass($attributeCode);
    }

    /**
     * Retrieve customer form data
     *
     * @throws Exception
     */
    protected function getCustomerFormData(): array
    {
        $data = $this->getData('customer_form_data');
        if ($data === null) {
            $customerId = $this->getRequest()->getParam('cid', null);
            $customer = $this->customerRepository->getById($customerId);
            $formData = $customer->__toArray();

            $data = [];
            if ($formData) {
                $data['data'] = $formData;
                $data['customer_data'] = 1;
            }
            $this->setData('customer_form_data', $data);
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getBusinessType()
    {
        return $this->businessType->toOptionArray();
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects->toOptionArray();
    }

    /**
     * @return array
     */
    public function getNumberOfJobsPerYear()
    {
        return $this->numberOfJobsPerYear->toOptionArray();
    }
}

