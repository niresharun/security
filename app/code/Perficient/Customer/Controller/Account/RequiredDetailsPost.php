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

namespace Perficient\Customer\Controller\Account;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Company\Model\CompanyProfile;
use Magento\Company\Api\CompanyRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;

/**
 * Class RequiredDetailsPost
 * @package Perficient\Customer\Controller\Account
 */
class RequiredDetailsPost implements ActionInterface
{
    final const ATTRIBUTE_STATUS_INACTIVE = 'no';

    /**
     * Save constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Session $customerSession
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param CompanyManagementInterface $companyManagement
     * @param Validator $formKeyValidator
     * @param CompanyProfile $companyProfile
     * @param CompanyRepositoryInterface $companyRepository
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        PageFactory                                  $resultPageFactory,
        RedirectFactory                              $resultRedirectFactory,
        Session                                      $customerSession,
        RequestInterface                             $request,
        Http                                         $requestHttp,
        private readonly LoggerInterface             $logger,
        private readonly CompanyManagementInterface  $companyManagement,
        private readonly Validator                   $formKeyValidator,
        private readonly CompanyProfile              $companyProfile,
        private readonly CompanyRepositoryInterface  $companyRepository,
        protected MessageManagerInterface            $messageManager,
        private readonly CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\DataObjectHelper      $objectHelper
    )
    {
        $this->requestHttp = $requestHttp;
        $this->objectHelper = $objectHelper;
    }

    public function execute(): \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
    {
        if ($this->customerSession->isLoggedIn()) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/');
            return $resultRedirect;
        }

        $resultRedirect = $this->resultRedirectFactory->create()->setPath('customer/account/login');
        $request = $this->request;

        if ($this->requestHttp->getMethod() === 'POST') {
            if (!$this->formKeyValidator->validate($request)) {
                return $resultRedirect;
            }

            try {
                $customerId = $this->request->getParam('cid', null);

                if ($customerId) {
                    $company = $this->companyManagement->getByCustomerId($customerId);

                    if ($company && $company->getId()) {
                        $companyData = $request->getParam('company');
                        $this->addCustomData($companyData, $company);
                        $this->companyProfile->populate($company, $companyData);
                        $this->objectHelper->populateWithArray(
                            $company,
                            $companyData,
                            \Magento\Company\Api\Data\CompanyInterface::class
                        );
                        $this->companyRepository->save($company);

                        $customerObj = $this->customerRepository->getById($customerId);
                        $customerData = $request->getParam('customer');
                        $customerObj->setEmail($customerData['email']);
                        $customerObj->setFirstname($customerData['firstname']);
                        $customerObj->setLastname($customerData['lastname']);
                        $this->customerRepository->save($customerObj);

                        $this->messageManager->addSuccessMessage(
                            __('Details have been successfully saved.')
                        );
                        return $resultRedirect;
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('You must fill in all required fields before you can continue.'));
                $this->logger->critical($e);
                return $resultRedirect->setPath('customer/account/requireddetails');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred on the server. Your changes have not been saved.')
                );
                $this->logger->critical($e);
                return $resultRedirect->setPath('customer/account/requireddetails');
            }
        }

        return $resultRedirect;
    }


    /**
     * @param $postData
     * @param $company
     * @return CompanyRepositoryInterface
     */
    private function addCustomData($postData, $company)
    {
        $customFields = [
            'newsletter',
            'is_dba',
            'first_name',
            'last_name',
            'dba_name',
            'resale_certificate_number',
            'website_address',
            'social_media_site',
            'business_type',
            'no_of_stores',
            'sq_ft_per_store',
            'type_of_projects',
            'no_of_jobs_per_year'
        ];

        foreach ($customFields as $attribute) {
            if (array_key_exists($attribute, $postData)) {
                $value = $postData[$attribute];
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $company->setData($attribute, $value);
            } else {
                $company->setData($attribute, self::ATTRIBUTE_STATUS_INACTIVE);

            }
        }
        return $company;
    }
}
