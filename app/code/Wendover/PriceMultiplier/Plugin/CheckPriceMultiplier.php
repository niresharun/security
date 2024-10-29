<?php

namespace Wendover\PriceMultiplier\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Psr\Log\LoggerInterface;

/**
 * Class CustomerAfterSave
 * @package Wendover\PriceMultiplier\Plugin
 */
class CheckPriceMultiplier
{
    /**
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepositoryInterface,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param CustomerRepository $subject
     * @param $savedCustomer
     * @return mixed
     */
    public function afterSave(CustomerRepository $subject, $savedCustomer)
    {
        if (empty($savedCustomer->getCustomAttribute('price_multiplier'))) {
            $this->setPriceMultiplierValue($savedCustomer);
        }
        $priceMultiplier = $savedCustomer->getCustomAttribute('price_multiplier')->getValue();
        if ($priceMultiplier === "1x") {
            $this->setPriceMultiplierValue($savedCustomer);
        }

        return $savedCustomer;
    }

    /**
     * @param $savedCustomer
     * @return void
     */
    public function setPriceMultiplierValue($savedCustomer)
    {
        $savedCustomer->setCustomAttribute('price_multiplier', '1.00');
        try {
            $this->customerRepositoryInterface->save($savedCustomer);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
