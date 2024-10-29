<?php
/**
 * @author Perficient Team
 * @copyright Copyright (c) 2021 Perficient
 * @package Perficient_Ccpa
 */

namespace Perficient\Ccpa\Block;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey as FormKey;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Perficient\Ccpa\Model\Config;


class Settings extends \Amasty\Ccpa\Block\Settings
{
    public function __construct(
        Template\Context            $context,
        Registry                    $registry,
        FormKey                     $formKey,
        protected Config            $configProvider,
        CustomerRepositoryInterface $customerRepository,
        Session                     $session,
        array                       $data = []
    ){
        parent::__construct($context, $registry, $formKey, $configProvider, $customerRepository, $session);
    }

    /**
     * @return string
     */
    public function getWendoverContactBlock()
    {
        if ($this->configProvider->isEnabledContactBlock(Config::ENABLED_WENDOVER_CONTACT_BLOCK)) {
            return $this->configProvider->allowWendoverContactBlock(Config::WENDOVER_CONTACT_BLOCK);
        }
        return '';
    }
}
