<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for recaptcha config data
 */
class FlatrateConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const CARRIERS_FLATRATE_ACTIVE = 'carriers/flatrate/active';

    final const SCOPE_ID = 0;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setShippingFlatrateActive();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Set Shipping Flatrate Active
     */
    public function setShippingFlatrateActive(): void
    {
        $this->configWriter->save(self::CARRIERS_FLATRATE_ACTIVE, 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
