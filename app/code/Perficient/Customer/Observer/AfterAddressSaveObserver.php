<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\Customer\Observer;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\CustomerFactory;

/**
 * Customer Observer Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AfterAddressSaveObserver implements ObserverInterface
{
    public function __construct(
        protected DateTime\DateTime $_date,
        protected CustomerFactory $customerFactory
    )
    {

    }

    /**
     * Address after save event handler
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getCustomerAddress();

        $customer = $customerAddress->getCustomer();
        $customerId = $customer->getId();
        $customerData = $this->customerFactory->create()->load($customerId);
        $customerDataModel = $customer->getDataModel();
        $customerDataModel->setUpdatedAt($this->_date->gmtDate());
        $customerData->updateData($customerDataModel);
       // $customerData->save();
    }
}
