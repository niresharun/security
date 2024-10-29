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

namespace Perficient\Customer\Controller\Account;

use Magento\Customer\Controller\Account\LoginPost as MagentoLoginPost;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Company\Api\CompanyManagementInterface;
use Perficient\Company\Helper\Data as PerfCompanyHelper;
use Magento\Company\Api\AclInterface;
use Perficient\Company\Plugin\Company\Model\Action\SaveCustomer as PerfCompanySaveCustomer;

/**
 * Post login customer action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends MagentoLoginPost
{
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param Validator $formKeyValidator
     * @param ScopeConfigInterface $scopeConfig
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CookieManagerInterface $cookieMetadataManager
     * @param CompanyManagementInterface $companyManagement
     * @param AclInterface $userRoleManagement
     */
    public function __construct(
        Context                                     $context,
        Session                                     $customerSession,
        AccountManagementInterface                  $customerAccountManagement,
        CustomerUrl                                 $customerHelperData,
        Validator                                   $formKeyValidator,
        AccountRedirect                             $accountRedirect,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly PerfCompanyHelper          $perfCompanyHelper,
        private readonly ScopeConfigInterface       $scopeConfig,
        private CookieMetadataFactory               $cookieMetadataFactory,
        private CookieManagerInterface              $cookieMetadataManager,
        private readonly CustomerUrl                $customerUrl,
        private readonly AclInterface               $userRoleManagement
    )
    {
        parent::__construct(
            $context,
            $customerSession,
            $customerAccountManagement,
            $customerHelperData,
            $formKeyValidator,
            $accountRedirect
        );
    }

    /**
     * Login post action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(): Redirect|ResponseInterface
    {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $customerId = $customer->getId();
                    $company = $this->companyManagement->getByCustomerId($customer->getId());

                    if (!empty($company) && $company->getStatus() == 1) {
                        $superUserId = $company->getSuperUserId();
                        if ($superUserId == $customerId) {
                            $isValidCompanyData = $this->perfCompanyHelper->validateCompanyData($company);
                            if (!$isValidCompanyData) {
                                $params = ['flpc' => true, 'cid' => $customerId];
                                return $this->_redirect(
                                    'customer/account/requireddetails',
                                    ['_query' => $params]
                                );
                            }
                        }

                        $customerRoles = $this->userRoleManagement->getRolesByUserId($customerId);
                        $userRoleName = null;
                        foreach ($customerRoles as $customerRole) {
                            $userRoleName = $customerRole->getRoleName();
                            break;
                        }

                        if ($userRoleName == PerfCompanySaveCustomer::CUSTOMER_EMPLOYEE) {
                            $isValidCompanyData = $this->perfCompanyHelper->validateCompanyData($company);
                            if (!$isValidCompanyData) {
                                $params = ['flpc' => true, 'cid' => $customerId];
                                return $this->_redirect(
                                    'customer/account/requireddetails',
                                    ['_query' => $params]
                                );
                            }
                        }
                    }

                    $this->session->setCustomerDataAsLoggedIn($customer);
                    if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                        $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                    }
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                    if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {
                        $this->accountRedirect->clearRedirectCookie();
                        $resultRedirect = $this->resultRedirectFactory->create();
                        // URL is checked to be internal in $this->_redirect->success()
                        $resultRedirect->setUrl($this->_redirect->success('/'));
                        return $resultRedirect;
                    }
                } catch (EmailNotConfirmedException $e) {
                    $this->messageManager->addComplexErrorMessage(
                        'confirmAccountErrorMessage',
                        ['url' => $this->customerUrl->getEmailConfirmationUrl($login['username'])]
                    );
                    $this->session->setUsername($login['username']);
                } catch (AuthenticationException $e) {
                    $message = __(
                        'The account sign-in was incorrect or your account is disabled temporarily. '
                        . 'Please wait and try again later.'
                    );
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addErrorMessage(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addErrorMessage($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('A login and a password are required.'));
            }
        }

        return $this->accountRedirect->getRedirect();
    }

    /**
     * Get scope config
     * @deprecated 100.0.10
     */
    private function getScopeConfig(): ScopeConfigInterface
    {
        if (!($this->scopeConfig instanceof ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        } else {
            return $this->scopeConfig;
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return \Magento\Framework\Stdlib\CookieManagerInterface
     * @deprecated 100.1.0
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(CookieManagerInterface::class);
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     * @deprecated 100.1.0
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->cookieMetadataFactory;
    }
}

