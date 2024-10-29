<?php
/**
 * Create new order from magento to syspro
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Perficient\Rabbitmq\Helper\Data as RabbitMQHelper;
//use Perficient\Rabbitmq\Model\MagentoToErp;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface as Logger;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\ResourceModel\Region as RegionResourceModel;

/**
 * Class CompanyUpdateMagentoToErp
 * @package Perficient\Rabbitmq\Model
 */
class CompanyUpdateMagentoToErp extends AbstractModel
{
    /**
     * @var $isDataSubmitted
     */
    private $isDataSubmitted;

    /**
     * CompanyUpdateMagentoToErp constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param PublisherInterface $publisher
     * @param Json $jsonSerializer
     * @param CustomerRepositoryInterface $customerRepository
     * @param Region $region
     * @param CustomerFactory $customerFactory
     * @param GroupRepositoryInterface $customerGroupRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,        
        private readonly RabbitMQHelper $rabbitMqHelper,
        private readonly PublisherInterface $publisher,
        private readonly Logger $logger,
        private readonly MagentoToErp $magentoToErp,
        private readonly Json $jsonSerializer,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Region $region,
        private readonly RegionResourceModel $regionResourceModel,
        private readonly CustomerFactory $customerFactory,
        private readonly GroupRepositoryInterface $customerGroupRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Method used to send the company information from Magento to ERP.
     * @param \Magento\Company\Model\Company $company
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCompanyDataFromMagentoToERP($company)
    {
        $companyData = null;
        try {
            $customerGroup = $this->customerGroupRepository->getById($company->getCustomerGroupId());
            $companyData = [
                'company' => [
                    'syspro_customer_id'         => $company->getSysproCustomerId(),
                    'web_company_id'            => $company->getId(),
                    'company_name'              => $company->getCompanyName(),
                    //'company_email'             => $company->getCompanyEmail(),
                    'street'                    => $company->getStreet(),
                    'city'                      => $company->getCity(),
                    'country_id'                => $company->getCountryId(),
                    'region_id'                 => $this->getRegionCode($company->getRegionId()),
                    'postcode'                  => $company->getPostcode(),
                    'telephone'                 => $company->getTelephone(),
                    'customer_group_id'         => $customerGroup->getCode(),
                    'dba_name'                  => $company->getDbaName(),
                    'resale_certificate_number' => $company->getResaleCertificateNumber(),
                    'website_address'           => $company->getWebsiteAddress(),
                    'social_media_site'         => $company->getSocialMediaSite(),
                    'business_type'             => $company->getBusinessType(),
                    'no_of_stores'              => $company->getNoOfStores(),
                    'sq_ft_per_store'           => $company->getSqFtPerStore(),
                    'type_of_projects'          => $company->getTypeOfProjects(),
                    'no_of_jobs_per_year'       => $company->getNoOfJobsPerYear(),
                    'discount_markup'           => $company->getDiscountMarkup(),
                    'discount_value'            => $company->getDiscountValue(),
                    'discount_application_type' => $company->getDiscountApplicationType(),
                    'discount_available'        => '',
                    'surcharge_status'          => '',
                    'credit_terms_group'        => '',
                    'sales_rep_name'            => '',
                    'cam_name'                  => '',
                    'sales_rep_phone'           => '',
                    'cam_phone'                 => '',
                    'sales_rep_email'           => '',
                    'cam_email'                 => '',
                    'vat_tax_id'                => ''
                ]
            ];

            // Get the additional information from customer.
            $this->getCustomerInformation($company->getSuperUserId(), $companyData);

            /**
             * Send data to ERP.
             */
            $this->magentoToErp->sendDataFromMagentoToERP(
                RabbitMQHelper::TOPIC_MAGENTO_COMPANY_UPDATE,
                $this->jsonSerializer->serialize($companyData)
            );
        } catch (\LogicException|\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::COMPANY_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage());
                $logger->debug($company->getData());
            }
            $publishData = ['error' => $e->getMessage(), 'message' => $companyData];
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MAGENTO_COMPANY_UPDATE , $this->jsonSerializer->serialize($publishData));
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Company Update From Magento to Syspro'),
                $companyData
            );
        }
    }

    /**
     * Method used to get additional information
     *
     * @param $customerId
     * @param $companyData
     */
    private function getCustomerInformation($customerId, &$companyData)
    {
        try {
            $customer = $this->customerFactory->create()->load($customerId);
            $companyData['company']['discount_available'] = $customer->getData('discount_available');
            $companyData['company']['surcharge_status'] = $customer->getData('surcharge_status');
            $companyData['company']['credit_terms_group'] = $this->getOptionLabelById($customer, 'credit_terms_group');
            $companyData['company']['sales_rep_name'] = $customer->getData('sales_rep_name');
            $companyData['company']['cam_name'] = $customer->getData('cam_name');
            $companyData['company']['sales_rep_phone'] = $customer->getData('sales_rep_phone');
            $companyData['company']['cam_phone'] = $customer->getData('cam_phone');
            $companyData['company']['sales_rep_email'] = $customer->getData('sales_rep_email');
            $companyData['company']['cam_email'] = $customer->getData('cam_email');
            $companyData['company']['vat_tax_id'] = $customer->getData('taxvat');
            $companyData['company']['qualifies_for_free_freight'] = $customer->getData('qualifies_for_free_freight');
            $companyData['company']['requires_individ_boxing'] = $customer->getData('requires_individ_boxing');
            $companyData['company']['is_b2c_customer'] = $customer->getData('is_b2c_customer');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $regionId
     * @return string
     */
    private function getRegionCode($regionId)
    {
        $region = '';
        try {
            $this->regionResourceModel->load($this->region, $regionId);
            $region = !empty($this->region) ? $this->region->getCode() : '';
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $region;
    }

    /**
     * @param $customer
     * @param $attributeCode
     * @return string
     */
    private function getOptionLabelById($customer, $attributeCode)
    {
        $isAttributeExist = $customer->getResource()->getAttribute($attributeCode);
        $optionLabel = '';
        $optionId = $customer->getData($attributeCode);

        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            foreach ($isAttributeExist->getOptions() as $option) {
                if ($optionId == $option->getValue()) {
                    $optionLabel = $option->getLabel();
                    break;
                }
            }
        }

        return $optionLabel;
    }
}
