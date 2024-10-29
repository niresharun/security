<?php
/**
 * Update company information in Magento from SysPro.
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Perficient, Inc.
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_Rabbitmq Syspro
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Model\CompanyProfile;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Perficient\Rabbitmq\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Company\Model\ResourceModel\Customer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\Region;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Company\Model\RoleManagement;
use Magento\Company\Model\UserRoleManagement;
use Perficient\Company\Plugin\Company\Model\Action\SaveCustomer;
use Magento\LoginAsCustomerAssistance\Model\ResourceModel\SaveLoginAsCustomerAssistanceAllowed;

/**
 * Class CompanyUpdate
 * @package Perficient\Rabbitmq\Model
 */
class CompanyUpdate
{
    /**
     * Constant for country.
     */
    const COUNTRY_CODE = 'US';

    private array $dropdownFields = [
        'credit_terms_group'
    ];

    private array $ignoreBlankFields = [
        'surcharge_status',
        'discount_available',
        'qualifies_for_free_freight',
        'requires_individ_boxing',
		'is_b2c_customer',
        'sales_rep_name',
        'cam_name',
        'sales_rep_phone',
        'cam_phone',
        'sales_rep_email',
        'cam_email',
        'syspro_customer_id'
    ];

    public array $skipNodes = [
        'resale_certificate_number',
        'website_address',
        'social_media_site',
        'business_type',
        'no_of_stores',
        'sq_ft_per_store',
        'type_of_projects',
        'no_of_jobs_per_year'
    ];

    public array $customerAttributes = [
        //'custom_price_multiplier',
        //'discount_type',
        'discount_available',
        //'price_multiplier',
        'surcharge_status',
        //'credit_terms_descr',
        //'social_media_address',
        'credit_terms_group',
        //'credit_terms_code',
        //'is_customer_of',
        //'uuid',
        //'is_vip',
        //'source_id',
        'sales_rep_name',
        'cam_name',
        'sales_rep_phone',
        'cam_phone',
        'sales_rep_email',
        'cam_email',
        'vat_tax_id',
        'customer_group_id',
        'syspro_customer_id',
        'qualifies_for_free_freight',
        'requires_individ_boxing',
		'is_b2c_customer',
        'company_branch'
    ];

    public array $skipCustomerCustomerAttr = [
        'is_b2c_customer'
    ];

    /**
     * @var
     */
    private $errorLogger;

    /**
     * @var
     */
    private $messageArray;

    /**
     * CompanyUpdate constructor.
     *
     * @param Json $jsonSerializer
     * @param CompanyProfile $companyProfile
     * @param CompanyRepositoryInterface $companyRepository
     * @param CustomerFactory $customerFactory
     * @param Customer $customerResourceModel
     * @param CustomerRepositoryInterface $customerRepository
     * @param Region $region
     * @param GroupRepositoryInterface $customerGroupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RoleManagement $roleManagement
     * @param UserRoleManagement $userRoleManagement
     */
    public function __construct(
        private readonly Data $rabbitMqHelper,
        private readonly Json $jsonSerializer,
        private readonly CompanyProfile $companyProfile,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CustomerFactory $customerFactory,
        private readonly Customer $customerResourceModel,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Region $region,
        private readonly GroupRepositoryInterface $customerGroupRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly RoleManagement $roleManagement,
        private readonly UserRoleManagement $userRoleManagement,
        private readonly SaveLoginAsCustomerAssistanceAllowed $saveLoginAsCustomerAssistanceAllowed
    ) {
    }

    /**
     * Method used to update company.
     *
     * @param $message
     */
    public function updateCompany($message): void
    {
        //Log the incoming message
        $this->rabbitMqHelper->logRabbitMqMessage($message);

        $this->errorLogger = $this->rabbitMqHelper->getRabbiMqLogger(
            Data::COMPANY_UPDATE_ERROR_LOG_FILE
        );
		try {
        /*// Validate the request body.
        $requestBody = $message->getBody();
        $requestJson = $this->isValidJson($requestBody);

        if (!$requestBody || !$requestJson) {
            $this->errorLoggerForCompanyUpdateProcess(
                $message->getBody(),
                __('Empty request or error in company update request data.')
            );
            return;
        }

        $messageArray = $this->jsonSerializer->unserialize($requestBody);*/

            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);

        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::COMPANY_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }

            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_COMPANY_UPDATE, $jsonData);

            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Company Update'),
                $message->getBody()
            );
        }
        try {
        /*if (!isset($messageArray['company'])) {
            $messageArray = $this->jsonSerializer->unserialize(trim($messageArray));
        }*/
        $messageArray = $this->messageArray;

        // Check for the company information in request.
        if (isset($messageArray['company'])) {
            $companyData = $messageArray['company'];
            $company = $this->getCompanyById($companyData['web_company_id']);
            // If company does not exists then log and send error email.
            if (!$company) {
                $this->errorLoggerForCompanyUpdateProcess(
                    $message->getBody(),
                    __('Company does not exists in Magento.')
                );
                return;
            } else {
                // Try to update all the company information.

                $country_id = '';
                try {
                    foreach ($companyData as $field => $value) {
                        /* If you need to replace the attributes with blank values if we get it blank from clarity message then follow below steps.
                         * a) comment this if condition below.
                         * b) need to add below check in company save plugin
                         * app/code/Perficient/Company/Plugin/Company/Model/CustomAttributes.php to avoid company value reset to old one
                         * $params = $this->request->getPost();
                         * if ($extensionAttributes && count($params)) {
                        */
                        // Skip the field, if value is empty.
                        if ((empty($value) && !in_array($field, $this->ignoreBlankFields)) ||
                            in_array($field, $this->skipNodes)) {
                            unset($companyData[$field]);
                            continue;
                        }
                        if (is_array($value) && $field != 'street') {
                            $value = implode(',', $value);
                        }
                        if ('country_id' == $field && !empty($value) ) {
                            $country_id = $value;
                        }
                        if ('region_id' == $field && !empty($country_id)) {
                            $value = $this->getRegionIdByCode($value, $country_id);
                            $companyData[$field] = $value;
                        } elseif ('customer_group_id' == $field) {
                            $value = $this->getCustomerGroupIdByName($value);
                            $companyData[$field] = $value;
                        } elseif ('dba_name' == $field) {
                            if (!empty($value)) {
                                $company->setData('is_dba', 'yes');
                            } else {
                                $company->setData('is_dba', 'no');
                            }
                        }
                        $company->setData($field, $value);
                    }
                    // Loop on all the drop-down fields to get the ids of the label.
                    foreach ($this->dropdownFields as $attributeCode) {
                        if (isset($companyData[$attributeCode])) {
                            $optionId = $this->getOptionIdByLabel($attributeCode, $companyData[$attributeCode]);
                            if (!empty($optionId)) {
                                $company->setData($attributeCode, $optionId);
                                $companyData[$attributeCode] = $optionId;
                            }
                        }
                    }
                    // Set the data as per required by company module and save it.
                    $this->companyProfile->populate($company, $companyData);
                    $this->companyRepository->save($company);
                    // Update all company users.
                    $this->updateAllCompanyUsers($company, $companyData);

                    // Set the success message.
                    $this->errorLogger->debug(
                        __('Company %1 is updated successfully in Magento.', $company->getCompanyName())
                    );
                } catch (\Exception $e) {
                    /*$this->errorLoggerForCompanyUpdateProcess(
                        $message->getBody(),
                        $e->getMessage()
                    );*/
                        if ($this->rabbitMqHelper->isLoggingEnabled()) {
                            $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                                Data::COMPANY_UPDATE_ERROR_LOG_FILE
                            );
                            $logger->debug($e->getMessage() . "::" . $message->getBody());
                        }
                        $publishData = ['error' => $e->getMessage()];
                        $jsonData = $this->jsonSerializer->serialize($publishData);
                        $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                        $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_COMPANY_UPDATE, $jsonData);
                        $this->rabbitMqHelper->sendErrorEmail(
                            $e->getMessage(),
                            __('Company Update'),
                            $message->getBody()
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::COMPANY_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_COMPANY_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Company Update'),
                $message->getBody()
            );
        }
    }

    /**
     * Method used to log message and send email.
     *
     * @param $data
     * @param $message
     */
    private function errorLoggerForCompanyUpdateProcess($data, $message): void
    {
        try {
        	if ($this->rabbitMqHelper->isLoggingEnabled()) {
            	$this->errorLogger->debug($message);
        	}
            $publishData = ['error' => $message];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$data.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_COMPANY_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $message,
                __('Company Update'),
                $this->jsonSerializer->serialize($data)
            );
        } catch (\Exception $e) {
            $this->errorLogger->debug($e->getMessage());
        }
    }

    /**
     * @param $companyId
     * @return \Magento\Company\Api\Data\CompanyInterface|null
     */
    private function getCompanyById($companyId)
    {
        $company = null;
        try {
            $company = $this->companyRepository->get($companyId);
        } catch (NoSuchEntityException $e) {
            $this->errorLogger->debug($e->getMessage());
        }

        return $company;
    }

    /**
     * Validate Json
     * @param $rawJson
     * @return bool
     */
    private function isValidJson($rawJson): bool
    {
        $unSerializedData = $this->jsonSerializer->unserialize($rawJson);
        return ($unSerializedData == null) ? false : true;
    }

    /**
     * Method used to get the option-id by label.
     *
     * @param $attributeCode
     * @param $optionLabel
     * @return string
     */
    private function getOptionIdByLabel($attributeCode, $optionLabel)
    {
        $customer = $this->customerFactory->create();
        $isAttributeExist = $customer->getResource()->getAttribute($attributeCode);
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
        }
        return $optionId;
    }

    /**
     * Method used to update all company users.
     *
     * @param $company
     */
    private function updateAllCompanyUsers($company, $companyData)
    {
        try {
            // Get Customer's Customer for this company
            $customersCustomerIds = $this->getCompanyUserFromCompany($company);
            // Get all the customer-ids of the company
            $allCustomers = $this->customerResourceModel->getCustomerIdsByCompanyId($company->getId());
            // Check, if there is any customer align to the company.
            if (is_countable($allCustomers) ? count($allCustomers) : 0) {
                // Then, loop on them and set the customer attributes received from SysPro.
                foreach ($allCustomers as $customerId) {
                    $customer = $this->customerRepository->getById($customerId);
                    foreach ($this->customerAttributes as $attribute) {
                        if (array_key_exists($attribute, $companyData)) {
                            if (
                                in_array($attribute, $this->skipCustomerCustomerAttr) &&
                                in_array($customerId, $customersCustomerIds)
                            ) {
                                continue;
                            } elseif ('vat_tax_id' == $attribute) {
                                if ($customer->getId() == $company->getSuperUserId()) {
                                    $customer->setTaxvat($company->getData($attribute));
                                }
                                $attribute = 'taxvat';
                            } elseif ('customer_group_id' == $attribute) {
                                $customer->setGroupId($company->getData($attribute));
                                $attribute = 'group_id';
                            }
                            if (isset($companyData[$attribute])) {
                                if (!empty($companyData[$attribute])) {
                                    $customer->setCustomAttribute($attribute, trim((string) $companyData[$attribute]));
                                    if ($attribute == 'is_b2c_customer' && $companyData[$attribute] == '1'){
                                        $customer->setCustomAttribute('discount_type', 'post-discounted');
                                    }
                                } else {
                                    $customer->setCustomAttribute($attribute, $companyData[$attribute]);
                                }
                                $allowedCustomerId =(int)$customerId;
                                $this->saveLoginAsCustomerAssistanceAllowed->execute($allowedCustomerId);
                            }
                        }
                    }
                    $this->customerRepository->save($customer);
                    if ($this->rabbitMqHelper->isCustomLogEnabled()) {
                        $this->errorLogger->debug(
                            __(
                                'Customer %1 and Price multiplier is (%2) updated successfully in Magento.',
                                $customer->getEmail(),
                                $customer->getCustomAttribute('price_multiplier')->getValue()
                            )
                        );
                    }
                    $this->errorLogger->debug(
                        __(
                            'Customer %1 (#%2) is updated successfully in Magento.',
                            $customer->getEmail(),
                            $customer->getId()
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            $this->errorLogger->debug($e->getMessage());
        }
    }

    /**
     * @param $company
     * @return array
     */
    private function getCompanyUserFromCompany($company)
    {
        $customersCustomerIds = [];

        //Get role ids from the company
        $customersCustomers = [];
        $customersCustomerRoleId = null;
        $companyRoles = $this->roleManagement->getRolesByCompanyId($company->getId());
        foreach ($companyRoles as $companyRole) {
            if ($companyRole->getRoleName() == SaveCustomer::CUSTOMER_CUSTOMER) {
                $customersCustomerRoleId = $companyRole->getId();
                if ($customersCustomerRoleId) {
                    $customersCustomers = $this->userRoleManagement->getUsersByRoleId($customersCustomerRoleId);
                }
            }
        }

        //Get customer ids from role id
        if ((is_countable($customersCustomers) ? count($customersCustomers) : 0) > 0) {
            foreach ($customersCustomers as $customersCustomer) {
                $customersCustomerIds[] = $customersCustomer->getId();
            }
        }
        return $customersCustomerIds;
    }

    /**
     * Get Region-Id by Region-Code.
     *
     * @param $regionCode
     * @return string
     */
    private function getRegionIdByCode($regionCode, $countryCode)
    {
        $regionId = '';
        try {
            $regionId = $this->region->loadByCode($regionCode, $countryCode)->getId();
        } catch (\Exception $e) {
            $this->errorLogger->debug($e->getMessage());
        }
        return $regionId;
    }

    /**
     * @param $groupName
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerGroupIdByName($groupName)
    {
        $this->searchCriteriaBuilder->addFilter(
            'customer_group_code',
            trim((string)$groupName)
        );

        $customerGroups = $this->customerGroupRepository->getList(
            $this->searchCriteriaBuilder
                ->setPageSize(1)
                ->create()
        )->getItems();

        $customerGroupId = null;
        if ($customerGroups) {
            foreach ($customerGroups as $customerGroup) {
                $customerGroupId = $customerGroup->getId();
                break;
            }
        }
        return $customerGroupId;
    }
}
