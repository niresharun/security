<?php
/**
 * Extend ParadoxLabs TokenBase for removing payment address save validations
 * @category: Magento
 * @package: Perficient/ParadoxLabsTokenBase
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_ParadoxLabsTokenBase
 */
namespace Perficient\ParadoxLabsTokenBase\Controller\Paymentinfo;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Save the card create/edit form
 */
class Save extends \ParadoxLabs\TokenBase\Controller\Paymentinfo\Save
{
    /**
     * @var \Magento\Quote\Model\Quote\PaymentFactory
     */
    protected $paymentFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @param Context $context
     * @param Session $customerSession *Proxy
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Registry $registry
     * @param \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession *Proxy
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Registry $registry,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Payment\Helper\Data $paymentHelper
    ) {
        $this->paymentFactory   = $paymentFactory;
        $this->checkoutSession  = $checkoutSession;
        $this->paymentHelper = $paymentHelper;

        parent::__construct(
            $context,
            $customerSession,
            $resultPageFactory,
            $formKeyValidator,
            $registry,
            $cardFactory,
            $cardRepository,
            $helper,
            $addressHelper,
            $paymentFactory,
            $checkoutSession,
            $paymentHelper
        );
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id            = $this->getRequest()->getParam('id');
        $method        = $this->getRequest()->getParam('method');

        if ($this->formKeyIsValid() === true && $this->methodIsValid() === true) {
            /**
             * Convert inputs into an address and payment object for storage.
             */
            try {
                /**
                 * Load the card and verify we are actually the cardholder before doing anything.
                 */

                /** @var \ParadoxLabs\TokenBase\Model\Card $card */
                if (!empty($id)) {
                    $card = $this->cardRepository->getByHash($id);
                } else {
                    $card = $this->cardFactory->create();
                    $card->setMethod($card->getMethod() ?: $method);
                }

                $card       = $card->getTypeInstance();
                $customer   = $this->helper->getCurrentCustomer();

                if ($card && (empty($id) || ($card->getHash() == $id && $card->hasOwner($customer->getId())))) {

                    /**
                     * Process address data
                     */
                    $newAddrId    = (int)$this->getRequest()->getParam('billing_address_id');

                    if ($newAddrId > 0) {

                        // Existing address
                        $newAddr = $this->addressHelper->repository()->getById($newAddrId);

                        if ($newAddr->getCustomerId() != $customer->getId()) {
                            throw new LocalizedException(__('An error occurred. Please try again.'));
                        }
                    } else {

                        // New address
                        $newAddr = $this->addressHelper->buildAddressFromInput(
                            $this->getRequest()->getParam('billing', []),
                            is_array($card->getAddress()) ? $card->getAddress() : []
                        );

                    }

                    /**
                     * Process payment data
                     */
                    $cardData = $this->getRequest()->getParam('payment');
                    $cardData['method']     = $method;
                    $cardData['card_id']    = $card->getId() > 0 ? $card->getHash() : '';

                    if (isset($cardData['cc_number'])) {
                        $cardData['cc_last4'] = substr((string) $cardData['cc_number'], -4);
                        $cardData['cc_bin']   = substr((string) $cardData['cc_number'], 0, 6);
                    }

                    /** @var \Magento\Quote\Model\Quote\Payment $newPayment */
                    $newPayment = $this->paymentFactory->create();
                    $newPayment->setQuote($this->checkoutSession->getQuote());
                    $newPayment->getQuote()->getBillingAddress()->setCountryId($newAddr->getCountryId());
                    $newPayment->importData($cardData);

                    $paymentMethod = $this->paymentHelper->getMethodInstance($card->getMethod());
                    $paymentMethod->setInfoInstance($newPayment);
                    $paymentMethod->validate();

                    /**
                     * Save payment data
                     */
                    $card->setMethod($method);
                    $card->setActive(1);
                    $card->setCustomer($customer);
                    $card->setAddress($newAddr);
                    $card->importPaymentInfo($newPayment);

                    $card = $this->cardRepository->save($card);

                    $this->session->unsData('tokenbase_form_data');

                    $this->messageManager->addSuccessMessage(__('Payment data saved successfully.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Invalid Request.'));
                }
            } catch (\Exception $e) {
                $this->session->setData('tokenbase_form_data', $this->getRequest()->getParams());

                $this->helper->log($method, (string)$e);
                $this->messageManager->addErrorMessage(__($e->getMessage()));

                $this->recordSessionFailure($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid Request.'));
        }

        $resultRedirect->setPath('*/*', ['method' => $method, '_secure' => true]);
        return $resultRedirect;
    }

    /**
     * Record each save failure on their session. If they fail too many times in a given period, block access. This is
     * to help prevent credit card validation abuse, trying to store CCs until one works.
     *
     * @param \Exception $e
     * @return void
     */
    protected function recordSessionFailure(\Exception $e)
    {
        $failures = $this->session->getData('tokenbase_failures');
        if (is_array($failures) === false) {
            $failures = [];
        }

        $failures[time()] = $e->getMessage();

        $this->session->setData('tokenbase_failures', $failures);
    }
}
