<?php
/**
 * Enable PageCache while Customer logged in From admin
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Shajitha Banu<Shajitha.Banu@Perficient.com>
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
class ConfigCustomerLoggedInSession implements DataPatchInterface
{
    /**
     * Constants defined for xpath of system configuration
     */
    final const DISABLE_PAGE_CACHE = 'login_as_customer/general/disable_page_cache';
    final const SCOPE_ID = FALSE;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
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
        $this->disablePageCacheSetting();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enable page cache for Customer logged-in from admin
     */
    public function disablePageCacheSetting(): void
    {
        $this->configWriter->save(self::DISABLE_PAGE_CACHE, "0", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function getAliases(): array
    {
        return [];
    }
}
