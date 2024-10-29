<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
/**
 * Patch script for minicart data
 */
class MiniCartConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const SIDEBAR_COUNT = 'sidebar/count';

    final const PATH_PREFIX = 'checkout/';

    final const SCOPE_ID = 0;

    final const SIDEBAR_COUNT_VALUE = 2;

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
        $this->setSidebarCount();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Number of Items to Display Scrollbar
     */
    public function setSidebarCount(): void
    {
        $pathSidebarCount = self::PATH_PREFIX . self::SIDEBAR_COUNT;
        $this->configWriter->save($pathSidebarCount, self::SIDEBAR_COUNT_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
