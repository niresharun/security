<?php
/**
 * change flatrate Method Name and Title
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
 * Patch script to change flatrate name and title
 */
class UpdateFlatRateMethodName implements DataPatchInterface
{

    const PATH_FLATRATE_NAME = 'carriers/flatrate/name';
    const PATH_FLATRATE_TITLE = 'carriers/flatrate/title';
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
        $this->changeFlatRateName();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Change Flatrate name and title
     */
    private function changeFlatRateName()
    {
        $title = "To Be Determined";
        $name = '';
        $this->configWriter->save(self::PATH_FLATRATE_NAME, $name, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PATH_FLATRATE_TITLE, $title, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
