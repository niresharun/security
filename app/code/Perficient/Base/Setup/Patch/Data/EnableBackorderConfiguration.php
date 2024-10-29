<?php
/**
 * Enable Backorde Configuration
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vijayashanthi<v.murugesan@Perficient.com>
 * @keywords: Module Perficient_Base
 */

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr config data
 */
class EnableBackorderConfiguration implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const ENABLE_BACKORDER = 'cataloginventory/item_options/backorders';

    final const SCOPE_ID = FALSE;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->enableBackorderConfiguration();;
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enable backorder
     */
    public function enableBackorderConfiguration()
    {
        $this->configWriter->save(self::ENABLE_BACKORDER, "1", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
