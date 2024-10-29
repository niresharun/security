<?php
/**
 * Controller to save customer price multiplier value
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: price multiplier custom customer attribute values in session
 */
declare(strict_types=1);

namespace Perficient\PriceMultiplier\Controller\Index;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Customer\Model\CustomerFactory;

class Save implements ActionInterface
{
    /**
     * Save constructor.
     * @param JsonFactory $resultJsonFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $customerSession
     * @param Cart $cart
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly JsonFactory                 $resultJsonFactory,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Session                     $customerSession,
        private readonly Cart                        $cart,
        protected RequestInterface                   $request,
        protected CustomerFactory                    $customerFactory
    )
    {

    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $multiplier = $this->request->getParam('price_multiplier');
        $discountType = $this->request->getParam('discount_type');

        /** @var JsonFactory $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        try {
            $customerId = $this->customerSession->getId();
            $customer = $this->customerFactory->create()->load($customerId);
            $customerDataModel = $customer->getDataModel();
            $customerDataModel->setCustomAttribute("price_multiplier", $multiplier);
            $customerDataModel->setCustomAttribute("discount_type", $discountType);
            $customer->updateData($customerDataModel);
            $customer->save();

            if ($multiplier == 0) {
                $quote = $this->cart->getQuote();
                $quote->removeAllItems()->save();
            }
            $this->customerSession->setMultiplier($multiplier);
            $this->customerSession->setDiscountType($discountType);

            return $resultJson->setData(
                [
                    'status' => 1,
                    'message' => __("Your price setting information has been saved.")
                ]
            );
        } catch (\Exception) {
            return $resultJson->setData(
                [
                    'message' => __('Something went wrong. We are unable to process your request.')
                ]
            );
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
