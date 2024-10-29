<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for in-store delivery config data
 */
class CashondeliveryConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const MODULE_ENABLED = 'active';
    final const PATH_PREFIX = 'payment/cashondelivery/';
    final const SCOPE_ID = 0;
    /**#@-*/

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
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setEnabled();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enabled for Checkout
     */
    public function setEnabled(): void
    {
        $pathEnable = self::PATH_PREFIX . self::MODULE_ENABLED;
        $valueEnable = 1;
        $this->configWriter->save($pathEnable, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
