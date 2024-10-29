<?php
/**
 * Disable Popular Search Terms
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_CatalogPermissions
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for Catalog Popular Search Term
 */
class ConfigData implements DataPatchInterface
{
    /**
     * @const string
     */
    const XML_PATH_SEO_SEARCH_TERMS = 'catalog/seo/search_terms';

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

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->disableCatalogPopularSearchTerm();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enable catalog config
     */
    public function disableCatalogPopularSearchTerm()
    {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        // Disable Popular Search Term
        $this->configWriter->save(self::XML_PATH_SEO_SEARCH_TERMS, 0, $scope, $scopeId);
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
