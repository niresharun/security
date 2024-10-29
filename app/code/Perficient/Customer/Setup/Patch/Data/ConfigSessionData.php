<?php
/**
 * Set Session Configuration
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<sachin.badase@Perficient.com>
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Customer\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr config data
 */
class ConfigSessionData implements DataPatchInterface
{
    /**
     * Constants defined for xpath of system configuration
     */
    final const CUSTOMER_COOKIE_LIFETIME = 'web/cookie/cookie_lifetime';
    final const SESSION_REMOTE_ADDR = 'web/session/use_remote_addr';
    final const SESSION_HTTP_VIA = 'web/session/use_http_via';
    final const SESSION_HTTP_X_FORWARDED = 'web/session/use_http_x_forwarded_for';
    final const SESSION_HTTP_USER_AGENT = 'web/session/use_http_user_agent';
    final const SCOPE_ID = FALSE;

    /**
     * ConfigData constructor.
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    )
    {

    }

    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setCustomerSessionValues();
        //$this->setSessionRestrictionValues();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Customer Cookie value increase
     */
    public function setCustomerSessionValues(): void
    {
        $this->configWriter->save(self::CUSTOMER_COOKIE_LIFETIME, "21600", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Session validation values changes
     */
    public function setSessionRestrictionValues()
    {
        /*$this->configWriter->save(self::SESSION_REMOTE_ADDR, "1", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::SESSION_HTTP_VIA , "1", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::SESSION_HTTP_X_FORWARDED , "1", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::SESSION_HTTP_USER_AGENT , "1", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
		*/
    }

    public function getAliases(): array
    {
        return [];
    }
}
