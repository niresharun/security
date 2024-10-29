<?php
/**
 * Disable Popular Search Terms
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<Sachin.Badase@Perficient.com>
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
class UrlRestructureConfigData implements DataPatchInterface
{
    /**
     * @const string
     */
    const STORES_SCOPE = 'stores';
    const WEBSITES_SCOPE = 'websites';
    const DEFAULT_SCOPE = 'default';
    const XML_PATH_PRODUCT_URL_SUFFIX = 'catalog/seo/product_url_suffix';
    const XML_PATH_CATEGORY_URL_SUFFIX = 'catalog/seo/category_url_suffix';
    const TRUE_SCOPE_ID = true;
    const FALSE_SCOPE_ID = false;

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
        $this->urlRestructureConfigData();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enable catalog config
     */
    public function urlRestructureConfigData()
    {

        $this->configWriter->save(self::XML_PATH_PRODUCT_URL_SUFFIX, '', self::DEFAULT_SCOPE, self::FALSE_SCOPE_ID);
        $this->configWriter->save(self::XML_PATH_PRODUCT_URL_SUFFIX, '', self::STORES_SCOPE, self::TRUE_SCOPE_ID);
        $this->configWriter->save(self::XML_PATH_PRODUCT_URL_SUFFIX, '', self::WEBSITES_SCOPE, self::TRUE_SCOPE_ID);
        $this->configWriter->save(self::XML_PATH_CATEGORY_URL_SUFFIX, '', self::DEFAULT_SCOPE, self::FALSE_SCOPE_ID);
        $this->configWriter->save(self::XML_PATH_CATEGORY_URL_SUFFIX, '', self::STORES_SCOPE, self::TRUE_SCOPE_ID);
        $this->configWriter->save(self::XML_PATH_CATEGORY_URL_SUFFIX, '', self::WEBSITES_SCOPE, self::TRUE_SCOPE_ID);
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
