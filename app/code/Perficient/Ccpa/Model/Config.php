<?php
/**
 * @author Perficient Team
 * @copyright Copyright (c) 2021 Perficient
 * @package Perficient_Ccpa
 */

declare(strict_types=1);

namespace Perficient\Ccpa\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;

class Config extends \Amasty\Ccpa\Model\Config
{
    /**
     * Variable
     */
    final public const ENABLED_WENDOVER_CONTACT_BLOCK = 'customer_access_control/show_wendover_contact_block';
    final public const WENDOVER_CONTACT_BLOCK = 'customer_access_control/wendover_contact_block';

    /**
     * Config constructor.
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        DateTime                       $dateTime
    ){
        parent::__construct($scopeConfig, $dateTime);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function isEnabledContactBlock($key)
    {
        return $this->scopeConfig->getValue(self::PATH_PREFIX . '/' . $key, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $key
     * @param null $scopeCode
     * @param $scopeType
     * @return mixed
     */
    public function allowWendoverContactBlock($key)
    {
        return $this->scopeConfig->getValue(self::PATH_PREFIX . '/' . $key, ScopeInterface::SCOPE_STORE);
    }
}
