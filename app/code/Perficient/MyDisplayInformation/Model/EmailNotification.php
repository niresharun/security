<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Customer\Model\EmailNotification as CoreEmailNotification;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Company\Ui\Component\Listing\Column\CompanyUsersActions;
use Perficient\MyDisplayInformation\Helper\Data;

/**
 * Customer email notification
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EmailNotification extends CoreEmailNotification
{
    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;
    /**
     * @var DataObjectProcessor
     */
    protected $dataProcessor;
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    const COMPANY_EMPLOYEE = 'Customer Employee';
    const COMPANY_ADMINISTRATOR = "Company Administrator";
    const COMPANY_CUSTOMER = "Customers Customer";

    /**
     * @param CustomerRegistry $customerRegistry
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CustomerViewHelper $customerViewHelper
     * @param DataObjectProcessor $dataProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface|null $senderResolver
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        CustomerViewHelper $customerViewHelper,
        DataObjectProcessor $dataProcessor,
        ScopeConfigInterface $scopeConfig,
        private readonly Data $myDisplayInformationHelper,
        private readonly CompanyUsersActions $companyUsersActions,
        SenderResolverInterface $senderResolver = null,
    ) {
        parent::__construct(
            $customerRegistry,
            $storeManager,
            $transportBuilder,
            $customerViewHelper,
            $dataProcessor,
            $scopeConfig,
            $senderResolver
        );
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->customerViewHelper = $customerViewHelper;
        $this->dataProcessor = $dataProcessor;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver ?? ObjectManager::getInstance()->get(SenderResolverInterface::class);
    }

    /**
     * @param CustomerInterface $customer
     * @param string $type
     * @param string $backUrl
     * @param null $storeId
     * @param null $sendemailStoreId
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function newAccount(
        CustomerInterface $customer,
        $type = self::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = null,
        $sendemailStoreId = null
    ): void
    {
        $types = self::TEMPLATE_TYPES;
        if (!isset($types[$type])) {
            throw new LocalizedException(
                __('The transactional account email type is incorrect. Verify and try again.')
            );
        }
        if ($storeId === null) {
            $storeId = $this->getWebsiteStoreId($customer, $sendemailStoreId);
        }
        $store = $this->storeManager->getStore($customer->getStoreId());
        $customerEmailData = $this->getFullCustomerObject($customer);
        $emailCompleteVariables = ['customer' => $customerEmailData, 'back_url' => $backUrl, 'store' => $store];
        $currentUserRole = $this->myDisplayInformationHelper->getCurrentUserRole();
		if(isset($currentUserRole[0])){
			  if ($currentUserRole[0] == self::COMPANY_EMPLOYEE || $currentUserRole[0] == self::COMPANY_ADMINISTRATOR) {
            $currentLoggedInCustomerId = $this->myDisplayInformationHelper->getCurrentUserId();
            $inviterData = $this->myDisplayInformationHelper->emailData($currentLoggedInCustomerId);
            // getting resource type
                  $resourceType = $this->companyUsersActions->checkForUserTypeLink();
                  if($resourceType == false) {
                      $inviterData['userrole'] = self::COMPANY_CUSTOMER;
                  } else {
                      $inviterData['userrole'] = 0;
                  }
            $emailCompleteVariables = ['customer' => $customerEmailData, 'back_url' => $backUrl, 'store' => $store, 'inviter' => $inviterData];
        }
		}

        $this->sendEmailTemplate(
            $customer,
            $types[$type],
            self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            $emailCompleteVariables,
            $storeId
        );
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     */
    private function getWebsiteStoreId($customer, $defaultStoreId = null): int
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param CustomerInterface $customer
     * @return CustomerSecure
     */
    private function getFullCustomerObject($customer): CustomerSecure
    {
// No need to flatten the custom attributes or nested objects since the only usage is for email templates and
// object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

    /**
     * Send corresponding email template
     *
     * @param CustomerInterface $customer
     * @param string $template configuration path of email template
     * @param string $sender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $email
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    private function sendEmailTemplate(
        $customer,
        $template,
        $sender,
        $templateParams = [],
        $storeId = null,
        $email = null
    ): void
    {
        $templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
        if ($email === null) {
            $email = $customer->getEmail();
        }
        /** @var array $from */
        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId),
            $storeId
        );
        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($from)
            ->addTo($email, $this->customerViewHelper->getCustomerName($customer))
            ->getTransport();
        $transport->sendMessage();
    }
}
