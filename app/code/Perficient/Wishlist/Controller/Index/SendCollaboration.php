<?php
/**
 * Created by PhpStorm.
 * User: sandeep.mude
 * Date: 13-01-2021
 * Time: 01:04 PM
 */

namespace Perficient\Wishlist\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\Generic as WishlistSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout as ResultLayout;
use Magento\Captcha\Helper\Data as CaptchaHelper;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\Escaper;
use Magento\Framework\Controller\Result\Redirect;

//use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Captcha\Model\DefaultModel as CaptchaModel;

//use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Customer;
use Perficient\Wishlist\Helper\Data;

class SendCollaboration extends \Magento\Wishlist\Controller\AbstractIndex implements Action\HttpPostActionInterface
{
    private readonly CaptchaHelper $captchaHelper;

    /**
     * @var CaptchaStringResolver
     */
    private $captchaStringResolver;

    /**
     * SendCollaboration constructor.
     * @param Action\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CaptchaStringResolver|null $captchaStringResolver
     */
    public function __construct(
        Action\Context                                                   $context,
        protected \Magento\Framework\Data\Form\FormKey\Validator         $_formKeyValidator,
        protected \Magento\Customer\Model\Session                        $_customerSession,
        protected \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        protected \Magento\Wishlist\Model\Config                         $_wishlistConfig,
        protected \Magento\Framework\Mail\Template\TransportBuilder      $_transportBuilder,
        protected \Magento\Framework\Translate\Inline\StateInterface     $inlineTranslation,
        protected \Magento\Customer\Helper\View                          $_customerHelperView,
        protected WishlistSession                                        $wishlistSession,
        protected ScopeConfigInterface                                   $scopeConfig,
        protected StoreManagerInterface                                  $storeManager,
        private readonly \Magento\Wishlist\Model\ResourceModel\Wishlist  $wishlistResource,
        private readonly Data                                            $helper,
        private readonly Escaper                                         $escaper,
        ?CaptchaHelper                                                   $captchaHelper = null,
        ?CaptchaStringResolver                                           $captchaStringResolver = null
    )
    {
        $this->captchaHelper = $captchaHelper ?: ObjectManager::getInstance()->get(CaptchaHelper::class);
        $this->captchaStringResolver = $captchaStringResolver ?: ObjectManager::getInstance()->get(CaptchaStringResolver::class);
        parent::__construct($context);
    }

    /**
     * Share wishlist
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $captchaForName = 'share_wishlist_form';
        /** @var CaptchaModel $captchaModel */
        $captchaModel = $this->captchaHelper->getCaptcha($captchaForName);

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        $isCorrectCaptcha = $this->validateCaptcha($captchaModel, $captchaForName);
        $this->logCaptchaAttempt($captchaModel);

        if (!$isCorrectCaptcha) {
            $this->messageManager->addErrorMessage(__('Incorrect CAPTCHA'));
            $resultRedirect->setPath('*/*/collaborate');
            return $resultRedirect;
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $sharingLimit = $this->_wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->_wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();

        $emails = $this->getRequest()->getPost('emails');
        $emails = empty($emails) ? $emails : explode(',', (string)$emails);

        $error = false;
        $customerIds = '';
        $message = (string)$this->getRequest()->getPost('message');
        if (strlen($message) > $textLimit) {
            $error = __('Message length must not exceed %1 symbols', $textLimit);
        } else {
            $message = nl2br((string)$this->escaper->escapeHtml($message));
            if (empty($emails)) {
                $error = __('Please enter an email address.');
            } else {
                if ((is_countable($emails) ? count($emails) : 0) > $emailsLeft) {
                    $error = __('Maximum of %1 emails can be sent.', $emailsLeft);
                } else {
                    foreach ($emails as $index => $email) {
                        $email = trim((string)$email);
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $error = __('Please enter a valid email address.');
                            break;
                        }
                        $emails[$index] = $email;
                    }
                    $customerIds = $this->helper->getCustomerIdsFromEmails($emails);
                }
            }
        }

        if ($error) {
            $this->messageManager->addErrorMessage($error);
            $this->wishlistSession->setSharingForm($this->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/collaborate');
            return $resultRedirect;
        }
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $this->addLayoutHandles($resultLayout);
        $this->inlineTranslation->suspend();

        $sent = 0;
        try {
            $customer = $this->_customerSession->getCustomerDataObject();
            $customerName = $this->_customerHelperView->getCustomerName($customer);

            $message .= $this->getRssLink($wishlist->getId(), $resultLayout);
            $emails = array_unique($emails);
            $sharingCode = $wishlist->getSharingCode();

            try {
                foreach ($emails as $email) {
                    $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->scopeConfig->getValue(
                            'collaboration/email/template',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'salable' => $wishlist->isSalable() ? 'yes' : '',
                            'items' => $this->getWishlistItems($resultLayout),
                            'viewOnSiteLink' => $this->_url->getUrl('*/index/collaboration', ['wishlist_id' => $wishlist->getId()]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
                    )->setFromByScope(
                        $this->scopeConfig->getValue(
                            'collaboration/email/identity',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $email
                    )->getTransport();

                    $transport->sendMessage();
                    $sent++;
                }
            } catch (\Exception $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $existingCollabIds = $wishlist->getCollaborationIds();
                if ($existingCollabIds) {
                    $existingCollabArray = explode(",", (string)$existingCollabIds);
                    $customerIdsArray = explode(",", $customerIds);
                    $collaboratedCustIds = array_unique([...$existingCollabArray, ...$customerIdsArray]);
                    $customerIds = implode(",", $collaboratedCustIds);
                }
                $wishlist->setCollaborationIds($customerIds);
                $this->wishlistResource->save($wishlist);
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $existingCollabIds = $wishlist->getCollaborationIds();
            if ($existingCollabIds) {
                $existingCollabArray = explode(",", (string)$existingCollabIds);
                $customerIdsArray = explode(",", $customerIds);
                $collaboratedCustIds = array_unique([...$existingCollabArray, ...$customerIdsArray]);
                $customerIds = implode(",", $collaboratedCustIds);
            }
            $wishlist->setCollaborationIds($customerIds);
            $this->wishlistResource->save($wishlist);

            $this->inlineTranslation->resume();

            $this->messageManager->addSuccessMessage(__('Your wish list collaboration has been sent.'));
            $resultRedirect->setPath('*/*', ['wishlist_id' => $wishlist->getId()]);
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->wishlistSession->setSharingForm($this->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/collaborate');
            return $resultRedirect;
        }
    }

    /**
     * Prepare to load additional email blocks
     *
     * Add 'wishlist_email_rss' layout handle.
     * Add 'wishlist_email_items' layout handle.
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return void
     */
    protected function addLayoutHandles(ResultLayout $resultLayout)
    {
        if ($this->getRequest()->getParam('rss_url')) {
            $resultLayout->addHandle('wishlist_email_rss');
        }
        $resultLayout->addHandle('wishlist_email_items');
    }

    /**
     * Retrieve RSS link content (html)
     *
     * @param int $wishlistId
     */
    protected function getRssLink($wishlistId, ResultLayout $resultLayout)
    {
        if ($this->getRequest()->getParam('rss_url')) {
            return $resultLayout->getLayout()
                ->getBlock('wishlist.email.rss')
                ->setWishlistId($wishlistId)
                ->toHtml();
        }
    }

    /**
     * Retrieve wishlist items content (html)
     *
     * @return string
     */
    protected function getWishlistItems(ResultLayout $resultLayout)
    {
        return str_replace('qty=', 'proceed=collaboration&amp;qty=', (string)$resultLayout->getLayout()
            ->getBlock('wishlist.email.items')
            ->toHtml());
    }

    /**
     * Log customer action attempts
     */
    private function logCaptchaAttempt(CaptchaModel $captchaModel): void
    {
        /** @var  Customer $customer */
        $customer = $this->_customerSession->getCustomer();
        $email = '';

        if ($customer->getId()) {
            $email = $customer->getEmail();
        }

        $captchaModel->logAttempt($email);
    }

    /**
     * Captcha validate logic
     */
    private function validateCaptcha(CaptchaModel $captchaModel, string $captchaFormName): bool
    {
        if ($captchaModel->isRequired()) {
            $word = $this->captchaStringResolver->resolve(
                $this->getRequest(),
                $captchaFormName
            );

            if (!$captchaModel->isCorrect($word)) {
                return false;
            }
        }

        return true;
    }
}
