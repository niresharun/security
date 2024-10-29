<?php

declare(strict_types=1);

namespace Wendover\Company\Plugin\Model\Action\Customer;

use Magento\LoginAsCustomerAssistance\Model\SetAssistance;

class Create
{


    public function __construct(
        private readonly  SetAssistance     $setAssistance
    ) {
    }


    public function afterExecute(\Magento\Company\Model\Action\Customer\Create $subject, $customer)
    {
        if($customer->getId())
        {
            $customerId = (int)  $customer->getId();
            $this->setAssistance->execute( $customerId, true);
        }
        return $customer;

    }


}
