<?php
/**
 * Block to get customer price multiplier value
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: price multiplier custom customer attribute values in session
 */
declare(strict_types=1);
namespace Perficient\PriceMultiplier\Block\Account\Dashboard;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Perficient\Company\Helper\Data;

/**
 * Class Multiplier
 * @package Perficient\PriceMultiplier\Block\Account\Dashboard
 */
class Multiplier extends Template
{
    /**
     * Multiplier constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param Data $helperData
     */
    public function __construct(
        Context                  $context,
        private readonly Session $customerSession,
        private readonly Data    $helperData,
        array                    $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getMultiplierPriceValue(): mixed
    {
        return $this->customerSession->getMultiplier();
    }

    public function getDiscountTypeValue(): mixed
    {
        return $this->customerSession->getDiscountType();
    }

    public function getDiscountAvailableValue(): mixed
    {
        return $this->customerSession->getDiscountAvailable();
    }

    public function isAllowedMultiplier(): bool
    {
        return $this->helperData->isAllowedMultiplier();
    }

    public function getPriceMultiplierValues(): array
    {
        return $this->helperData->getPriceMultiplierValues();
    }

    public function getDiscountTypeValues(): array
    {
        return $this->helperData->getDiscountTypeValues();
    }
}
