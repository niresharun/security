<?php
/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_RequisitionList
 */

namespace Perficient\RequisitionList\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class ConfigData
 * @package Perficient\RequisitionList\Setup\Patch\Data
 */
class ConfigData implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const REQUISITION_LIST_ACTIVE = 'btob/website_configuration/requisition_list_active';

    const SCOPE_ID = 0;

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
        $pathEnable = self::REQUISITION_LIST_ACTIVE;
        $valueEnable = true;
        $this->configWriter->save($pathEnable, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}