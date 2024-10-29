<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\Customer\Observer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
/**
 * Customer Observer Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BeforeAddressDeleteObserver implements ObserverInterface
{
    /**
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        protected DateTime\DateTime          $_date,
        protected AddressRepositoryInterface $_addressRepository,
        protected CustomerFactory            $customer
    )
    {
    }

    /**
     * Address after save event handler
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        /** @var $customerAddress Address */
        $addressId = $observer->getEvent()->getRequest()->getParam('id', false);
        if ($addressId) {
            $address = $this->_addressRepository->getById($addressId);
            if ($address && $address->getCustomerId()) {
                $customer = $this->customer->create()->load($address->getCustomerId());
                if ($customer) {
                    $customer->setUpdatedAt($this->_date->gmtDate());
                    $customer->save();
                }
            }
        }
    }
}
