<?php
/**
 * Company Assign Customer Email Restriction.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */

namespace Perficient\Company\Model\Email;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Model\Config\EmailTemplate as EmailTemplateConfig;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Company\Model\Email\Sender as ParentSender;
use Magento\Company\Model\Email\Transporter;
use Magento\Company\Model\Email\CustomerData;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sending company related emails.
 */
class Sender extends ParentSender
{
    /**
     * Email template for identity.
     */
    private string $xmlPathRegisterEmailIdentity = 'customer/create_account/email_identity';

    /**
     * Sender constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Transporter $transporter
     * @param CustomerNameGenerationInterface $customerViewHelper
     * @param CustomerData $customerData
     * @param CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        private readonly StoreManagerInterface           $storeManager,
        private readonly ScopeConfigInterface            $scopeConfig,
        private readonly Transporter                     $transporter,
        private readonly CustomerNameGenerationInterface $customerViewHelper,
        private readonly CustomerData                    $customerData,
        private readonly EmailTemplateConfig             $emailTemplateConfig,
        CompanyRepositoryInterface                       $companyRepository
    )
    {
        parent::__construct($storeManager, $scopeConfig, $transporter, $customerViewHelper, $customerData, $emailTemplateConfig, $companyRepository);
    }

    /**
     * Get either first store ID from a set website or the provided as default.
     * @throws LocalizedException
     */
    private function getWebsiteStoreId(\Magento\Customer\Api\Data\CustomerInterface $customer): int
    {
        $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        if ($customer->getWebsiteId() != 0) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Send email to customer after assign company to him.
     *
     * @param int $companyId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function sendCustomerCompanyAssignNotificationEmail(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
                                                     $companyId
    ): static
    {
        // getting custom session value to restrict the email only from frontend customers customer and employee creation
        //$areaCode = $this->session->getData('assignCustomerEmailArea');
        $customerName = $this->customerViewHelper->getCustomerName($customer);
        $companySuperUser = $this->customerData->getDataObjectSuperUser($companyId);
        $mergedCustomerData = $this->customerData->getDataObjectByCustomer($customer, $companyId);

        if ($companySuperUser && $mergedCustomerData) {
            $sender = [
                'name' => $companySuperUser->getName(),
                'email' => $companySuperUser->getEmail()
            ];

            $mergedCustomerData->setData('companyAdminEmail', $companySuperUser->getEmail());
            $this->sendEmailTemplate(
                $customer->getEmail(),
                $customerName,
                $this->emailTemplateConfig->getCompanyCustomerAssignUserTemplateId(
                    ScopeInterface::SCOPE_STORE,
                    $customer->getStoreId()
                ),
                $sender,
                ['customer' => $mergedCustomerData],
                $customer->getStoreId()
            );
        }
        // reset the custom session value
        //$this->session->setData('assignCustomerEmailArea', '');
        return $this;
    }

    /**
     * Notify admin about new company.
     *
     * @param string $companyName
     * @param string $companyUrl
     * @throws LocalizedException
     */
    public function sendAdminNotificationEmail(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
                                                     $companyName,
                                                     $companyUrl
    ): static
    {
        $toCode = $this->emailTemplateConfig->getCompanyCreateRecipient(ScopeInterface::SCOPE_STORE);
        $toEmail = $this->scopeConfig->getValue('trans_email/ident_' . $toCode . '/email', ScopeInterface::SCOPE_STORE);
        $toName = $this->scopeConfig->getValue('trans_email/ident_' . $toCode . '/name', ScopeInterface::SCOPE_STORE);

        $copyTo = $this->emailTemplateConfig->getCompanyCreateCopyTo(ScopeInterface::SCOPE_STORE);
        $copyMethod = $this->emailTemplateConfig->getCompanyCreateCopyMethod(ScopeInterface::SCOPE_STORE);
        $storeId = $customer->getStoreId() ?: $this->getWebsiteStoreId($customer);

        /*Start Fix for ticket WENDOVER-534*/
        $mergedCompanyData = $this->getCustomerCompanyData($customer);
        /*End Fix for ticket WENDOVER-534*/

        $sendTo = [];
        if ($copyTo && $copyMethod == 'copy') {
            $sendTo = explode(',', (string)$copyTo);
        }
        array_unshift($sendTo, $toEmail);

        foreach ($sendTo as $recipient) {
            $this->sendEmailTemplate(
                $recipient,
                $toName,
                $this->emailTemplateConfig->getCompanyCreateNotifyAdminTemplateId(),
                $this->xmlPathRegisterEmailIdentity,
                [
                    'customer' => $customer->getFirstname(),
                    'company' => $companyName,
                    'admin' => $toName,
                    'company_url' => $companyUrl,
                    'companyData' => $mergedCompanyData
                ],
                $storeId,
                ($copyTo && $copyMethod == 'bcc') ? explode(',', (string)$copyTo) : []
            );
        }

        return $this;
    }

    /**
     * Send corresponding email template.
     */
    private function sendEmailTemplate(
        string       $customerEmail,
        string       $customerName,
        string       $templateId,
        string|array $sender,
        array        $templateParams = [],
        int          $storeId = null,
        array        $bcc = []
    ): void
    {
        $from = $sender;
        if (is_string($sender)) {
            $from = $this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId);
        }
        $this->transporter->sendMessage(
            $customerEmail,
            $customerName,
            $from,
            $templateId,
            $templateParams,
            $storeId,
            $bcc
        );
    }

    /**
     * Function to get company / customer data to use in custom email template
     * To notify admin about new company
     * @param $customer
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getCustomerCompanyData($customer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $psrLogger = $objectManager->get(\Psr\Log\LoggerInterface::class);
        try {
            if ($customer->getId()) {
                $companyManagement = $objectManager->get(\Magento\Company\Api\CompanyManagementInterface::class);
                $companyData = $companyManagement->getByCustomerId($customer->getId());

                if (!empty($companyData)) {
                    $companyId = $companyData->getId();
                    $countryInfoProvider = $objectManager->create(
                        'Magento\Company\Model\CountryInformationProvider'
                    );
                    $regionInfoProvider = $objectManager->create('Magento\Directory\Model\Region')
                        ->load($companyData->getRegionId());
                    $mergedCustomerData = $this->customerData->getDataObjectByCustomer($customer, $companyId);

                    if ($mergedCustomerData) {
                        $countryName = $countryInfoProvider->getCountryNameByCode($companyData->getCountryId());
                        $regionName = $regionInfoProvider ? $regionInfoProvider->getName() : null;
                        $street = $companyData->getStreet();
                        $street0 = isset($street[0]) && !empty($street[0]) ? $street[0] : null;
                        $street1 = isset($street[1]) && !empty($street[1]) ? $street[1] : null;

                        $mergedCustomerData->setData('companyEmail', $companyData->getCompanyEmail());
                        $mergedCustomerData->setData(
                            'companyResaleCertificate',
                            $companyData->getresaleCertificateNumber()
                        );
                        $mergedCustomerData->setData('companyWebsite', $companyData->getWebsiteAddress());
                        $mergedCustomerData->setData('companySocialMedia', $companyData->getSocialMediaSite());
                        $mergedCustomerData->setData('companyBusinessType', $companyData->getBusinessType());

                        $mergedCustomerData->setData('billingStreetZero', $street0);
                        $mergedCustomerData->setData('billingStreetOne', $street1);
                        $mergedCustomerData->setData('billingCity', $companyData->getCity());
                        $mergedCustomerData->setData('billingCountry', $countryName);
                        $mergedCustomerData->setData('billingState', $regionName);
                        $mergedCustomerData->setData('billingPostal', $companyData->getPostcode());
                        $mergedCustomerData->setData('billingPhoneNumber', $companyData->getTelephone());

                        $mergedCustomerData->setData('adminEmail', $customer->getEmail());
                        $mergedCustomerData->setData('adminFirstName', $customer->getFirstname());
                        $mergedCustomerData->setData('adminLastName', $customer->getLastname());

                        return $mergedCustomerData;
                    } else {
                        $psrLogger->critical('Company / Customer Data Not Found.' . __FILE__ . ' - ' . __METHOD__);
                    }
                } else {
                    $psrLogger->critical('Company Data Not Found.' . __FILE__ . ' - ' . __METHOD__);
                }
            } else {
                $psrLogger->critical('Customer Data Not Found.' . __FILE__ . ' - ' . __METHOD__);
            }
        } catch (Exception $e) {
            $psrLogger->critical($e);
        }

        return null;
    }
}
