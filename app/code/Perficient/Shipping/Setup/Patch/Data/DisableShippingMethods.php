<?php
/**
 * Disable shipping methods
 * @category: Magento
 * @package: Perficient/Shipping
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Tahir Aziz<tahir.aziz@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Shipping
 */


namespace Perficient\Shipping\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\Config\Backend\Encrypted;

/**
 * Patch script to disable UPS and BlueShip config data
 */
class DisableShippingMethods implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const MODULE_ENABLED = 'active';

    const PATH_UPS_PREFIX = 'carriers/ups/';

    const PATH_BLUESHIP_PREFIX = 'carriers/blueship/';

    const SCOPE_ID = 0;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Encrypted $encrypted
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Encrypted                $encrypted
    )
    {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setDisableUps();
        $this->setDisableBlueship();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Disable UPS for Checkout
     */
    private function setDisableUps()
    {
        $pathUSPEnable = self::PATH_UPS_PREFIX . self::MODULE_ENABLED;
        $valueEnable = '0';
        $this->configWriter->save($pathUSPEnable, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Disable UPS for Checkout
     */
    private function setDisableBlueship()
    {
        $pathBlueshipEnable = self::PATH_BLUESHIP_PREFIX . self::MODULE_ENABLED;
        $valueEnable = '0';
        $this->configWriter->save($pathBlueshipEnable, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
