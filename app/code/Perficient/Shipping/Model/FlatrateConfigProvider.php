<?php
/**
 * File used to hide flatrate from frontend.
 *
 * @category: Magento
 * @package: Perficient/Shipping
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Tahir Aziz <tahir.aziz@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Shipping
 */
declare(strict_types=1);

namespace Perficient\Shipping\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class FlatrateConfigProvider implements ConfigProviderInterface
{
    const FLATRATE_HIDE_IN_FRONT_PATH = 'carriers/flatrate/hide_in_frontend';
    const FLATRATE_DISABLED_NOTICE_PATH = 'carriers/flatrate/disabled_notice';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    )
    {
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $config["flatrate_hide_in_frontend"] = (int)$this->scopeConfig->getValue(
            self::FLATRATE_HIDE_IN_FRONT_PATH,
            ScopeInterface::SCOPE_STORE
        );
        $config["flatrate_disabled_notice"] = $this->scopeConfig->getValue(
            self::FLATRATE_DISABLED_NOTICE_PATH,
            ScopeInterface::SCOPE_STORE
        );

        return $config;
    }
}
