<?php
/**
 * CustomerData
 *
 * @author    krn.ppt@gmail.com
 */
declare(strict_types=1);

namespace Perficient\Customer\Plugin\CustomerData;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class CustomerData
 * @package Vendor\ModuleName\Model\Plugin
 */
class CustomerPlugin
{
    /**
     * CustomerData constructor.
     */
    public function __construct(
        protected CustomerSession $customerSession
    )
    {

    }

    /**
     * Add additional required attributes to customerData
     *
     * email
     * mobile_no
     *
     * @param Customer $subject
     * @param $result
     */
    public function afterGetSectionData(Customer $subject, $result): mixed
    {
        if ($this->customerSession->getId()) {
            $result['business_type'] = $this->customerSession->getBusinessType();
        }

        return $result;
    }
}
