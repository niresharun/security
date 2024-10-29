<?php
/**
 * @author Perficient Team
 * @copyright Copyright (c) 2021 Perficient
 * @package Perficient_Gdpr
 */

declare(strict_types=1);

namespace Perficient\Gdpr\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Config extends \Amasty\Gdpr\Model\Config
{
    /**
     * Variable
     */
    final public const ENABLED_WENDOVER_CONTACT_BLOCK = 'customer_access_control/show_wendover_contact_block';
    final public const WENDOVER_CONTACT_BLOCK = 'customer_access_control/wendover_contact_block';

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param DateTime $dateTime
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        DateTime $dateTime
    ) {
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
