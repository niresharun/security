<?php
/**
 * change flatrate Notice
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
 * Patch script to change flatrate price
 */
class FlatRateConfigDisabledMessage implements DataPatchInterface
{

    const PATH_FLATRATE_NOTICE = 'carriers/flatrate/disabled_notice';
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
        $this->setDisabledNotice();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Change Disabled Notice
     */
    private function setDisabledNotice()
    {
        $notice = "* Freight will be calculated at shipping";
        $this->configWriter->save(self::PATH_FLATRATE_NOTICE, $notice, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
