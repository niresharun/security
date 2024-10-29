<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Wendover/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author:
 * @project: Wendover
 * @keywords: Module Wendover_Customer
 */
declare(strict_types=1);

namespace Wendover\Customer\Controller\Account;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\SessionCleanerInterface;
use Magento\Customer\Controller\Account\EditPost as CoreEditPost;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\Customer\Mapper;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Filesystem;

/**
 * Controller for saving company profile.
 */
class EditPost extends CoreEditPost
{
    private $customerMapper;
    /**
     * @var AddressRegistry
     */
    private $addressRegistry;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var SessionCleanerInterface
     */
    private $sessionCleaner;
    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;
    /**
     * @var AccountConfirmation
     */
    private $accountConfirmation;
    /**
     * @var Url
     */
    private Url $customerUrl;
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    public function __construct(
        \Magento\Framework\App\Action\Context                             $context,
        \Magento\Customer\Model\Session                                   $customerSession,
        \Magento\Customer\Api\AccountManagementInterface                  $accountManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface                 $customerRepository,
        \Magento\Framework\Data\Form\FormKey\Validator                    $formKeyValidator,
        \Magento\Customer\Model\CustomerExtractor                         $customerExtractor,
        private readonly \Magento\Customer\Model\CustomerFactory          $customerFactory,
        private readonly \Magento\Framework\Escaper                       $escaper,
        ?\Magento\Customer\Model\AddressRegistry                          $addressRegistry = null,
        ?\Magento\Framework\Filesystem                                    $filesystem = null,
        ?\Magento\Customer\Api\SessionCleanerInterface                    $sessionCleaner = null,
        ?\Magento\Customer\Model\AccountConfirmation                      $accountConfirmation = null,
        ?\Magento\Customer\Model\Url                                      $customerUrl = null
    )
    {
        parent::__construct(
            $context,
            $customerSession,
            $accountManagement,
            $customerRepository,
            $formKeyValidator,
            $customerExtractor,
            $escaper,
            $addressRegistry,
            $filesystem,
            $sessionCleaner,
            $accountConfirmation,
            $customerUrl
        );
        $this->session = $customerSession;
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        $this->addressRegistry = $addressRegistry ?: ObjectManager::getInstance()->get(AddressRegistry::class);
        $this->filesystem = $filesystem ?: ObjectManager::getInstance()->get(Filesystem::class);
        $this->sessionCleaner = $sessionCleaner ?: ObjectManager::getInstance()->get(SessionCleanerInterface::class);
        $this->accountConfirmation = $accountConfirmation ?: ObjectManager::getInstance()
            ->get(AccountConfirmation::class);
        $this->customerUrl = $customerUrl ?: ObjectManager::getInstance()->get(Url::class);
    }

    /**
     * Get authentication
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication()
    {
        if (!($this->authentication instanceof AuthenticationInterface)) {
            return ObjectManager::getInstance()->get(AuthenticationInterface::class);
        } else {
            return $this->authentication;
        }
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     */
    private function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return ObjectManager::getInstance()->get(EmailNotificationInterface::class);
        } else {
            return $this->emailNotification;
        }
    }

    /**
     * Change customer email or password action
     *
     * @return Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws SessionException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if ($validFormKey && $this->getRequest()->isPost()) {
            $customer = $this->getCustomerDataObject($this->session->getCustomerId());
            $customerCandidate = $this->populateNewCustomerDataObject($this->_request, $customer);

            $attributeToDelete = $this->_request->getParam('delete_attribute_value');
            if ($attributeToDelete !== null) {
                $this->deleteCustomerFileAttribute($customerCandidate, $attributeToDelete);
            }

            try {
                // whether a customer enabled change email option
                $isEmailChanged = $this->processChangeEmailRequest($customer);

                // whether a customer enabled change password option
                $isPasswordChanged = $this->changeCustomerPassword($customer->getEmail());

                // No need to validate customer address while editing customer profile
                $this->disableAddressValidation($customerCandidate);

                $customerModel = $this->customerFactory->create();
                $customerModel->load($customerCandidate->getId());
                $customerModel->setFirstname($this->getRequest()->getParam('firstname'));
                $customerModel->setLastname($this->getRequest()->getParam('lastname'));
                if ($this->getRequest()->getParam('change_email')) {
                    $customerModel->setEmail($this->getRequest()->getParam('email'));
                }
                $customerModel->save();
                $updatedCustomer = $this->customerRepository->getById($customerCandidate->getId());
                $this->getEmailNotification()->credentialsChanged(
                    $updatedCustomer,
                    $customer->getEmail(),
                    $isPasswordChanged
                );

                $this->dispatchSuccessEvent($updatedCustomer);
                $this->messageManager->addSuccessMessage(__('You saved the account information.'));
                // logout from current session if password or email changed.
                if ($isPasswordChanged || $isEmailChanged) {
                    $this->session->logout();
                    $this->session->start();
                    $this->addComplexSuccessMessage($customer, $updatedCustomer);

                    return $resultRedirect->setPath('customer/account/login');
                }
                return $resultRedirect->setPath('customer/account');
            } catch (InvalidEmailOrPasswordException $e) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
            } catch (UserLockedException $e) {
                $message = __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                );
                $this->session->logout();
                $this->session->start();
                $this->messageManager->addErrorMessage($message);

                return $resultRedirect->setPath('customer/account/login');
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
                foreach ($e->getErrors() as $error) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($error->getMessage()));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t save the customer.'));
            }

            $this->session->setCustomerFormData($this->getRequest()->getPostValue());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/edit');

        return $resultRedirect;
    }

    /**
     * Adds a complex success message if email confirmation is required
     * @throws LocalizedException
     */
    private function addComplexSuccessMessage(
        CustomerInterface $outdatedCustomer,
        CustomerInterface $updatedCustomer
    ): void {
        if (($outdatedCustomer->getEmail() !== $updatedCustomer->getEmail())
            && $this->accountConfirmation->isCustomerEmailChangedConfirmRequired($updatedCustomer)) {
            $this->messageManager->addComplexSuccessMessage(
                'confirmAccountSuccessMessage',
                ['url' => $this->customerUrl->getEmailConfirmationUrl($updatedCustomer->getEmail())]
            );
        }
    }

    /**
     * Account editing action completed successfully event
     *
     * @return void
     */
    private function dispatchSuccessEvent(CustomerInterface $customerCandidateDataObject)
    {
        $this->_eventManager->dispatch(
            'customer_account_edited',
            ['email' => $customerCandidateDataObject->getEmail()]
        );
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Create Data Transfer Object of customer candidate
     *
     * @param RequestInterface $inputData
     * @param CustomerInterface $currentCustomerData
     * @return CustomerInterface
     */
    private function populateNewCustomerDataObject(
        RequestInterface $inputData,
        CustomerInterface $currentCustomerData
    ) {
        $attributeValues = $this->getCustomerMapper()->toFlatArray($currentCustomerData);
        $customerDto = $this->customerExtractor->extract(
            self::FORM_DATA_EXTRACTOR_CODE,
            $inputData,
            $attributeValues
        );
        $customerDto->setId($currentCustomerData->getId());
        if (!$customerDto->getAddresses()) {
            $customerDto->setAddresses($currentCustomerData->getAddresses());
        }
        if (!$inputData->getParam('change_email')) {
            $customerDto->setEmail($currentCustomerData->getEmail());
        }

        return $customerDto;
    }

    /**
     * Process change email request
     *
     * @return bool
     * @throws InvalidEmailOrPasswordException
     * @throws UserLockedException
     */
    private function processChangeEmailRequest(CustomerInterface $currentCustomerDataObject)
    {
        if ($this->getRequest()->getParam('change_email')) {
            // authenticate user for changing email
            try {
                $this->getAuthentication()->authenticate(
                    $currentCustomerDataObject->getId(),
                    $this->getRequest()->getPost('current_password')
                );
                $this->sessionCleaner->clearFor((int) $currentCustomerDataObject->getId());
                return true;
            } catch (InvalidEmailOrPasswordException) {
                throw new InvalidEmailOrPasswordException(
                    __("The password doesn't match this account. Verify the password and try again.")
                );
            }
        }

        return false;
    }

    /**
     * Get Customer Mapper instance
     *
     * @return Mapper
     */
    private function getCustomerMapper()
    {
        if ($this->customerMapper === null) {
            $this->customerMapper = ObjectManager::getInstance()->get(Mapper::class);
        }
        
        return $this->customerMapper;
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Removes file attribute from customer entity and file from filesystem
     *
     * @param CustomerInterface $customerCandidateDataObject
     * @throws FileSystemException
     */
    private function deleteCustomerFileAttribute(
        CustomerInterface $customerCandidateDataObject,
        string $attributeToDelete
    ) : void {
        $attributes = [];
        if ($attributeToDelete !== '') {
            if (str_contains($attributeToDelete, ',')) {
                $attributes = explode(',', $attributeToDelete);
            } else {
                $attributes[] = $attributeToDelete;
            }
            foreach ($attributes as $attr) {
                $attributeValue = $customerCandidateDataObject->getCustomAttribute($attr);
                if ($attributeValue!== null) {
                    if ($attributeValue->getValue() !== '') {
                        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                        $fileName = $attributeValue->getValue();
                        $path = $mediaDirectory->getAbsolutePath('customer' . $fileName);
                        if ($fileName && $mediaDirectory->isFile($path)) {
                            $mediaDirectory->delete($path);
                        }
                        $customerCandidateDataObject->setCustomAttribute(
                            $attr,
                            ''
                        );
                    }
                }
            }
        }
    }
}
