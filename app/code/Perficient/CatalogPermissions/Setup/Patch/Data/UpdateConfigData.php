<?php
/**
 * Disable add to cart button and product price configuration for all users.
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani<Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_CatalogPermissions
 */

namespace Perficient\CatalogPermissions\Setup\Patch\Data;

use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for Catalog Permissions
 */
class UpdateConfigData implements DataPatchInterface
{
    /**
     * Prefix path for CatalogPermission
     */
    final public const PATH_PREFIX = 'catalog/magento_catalogpermissions/';

    /**
     * Disable add to cart config for all group
     */
    final public const GRANT_CHECKOUT_ITEMS_GROUPS = 'grant_checkout_items_groups';

    /**
     * Disable price config for all group
     */
    final public const GRANT_CATALOG_PRODUCT_PRICE_GROUPS = 'grant_catalog_product_price_groups';


    /**
     * ConfigData constructor.
     */
    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->disableCatalogPermission();
        $this->unsetAddToCartConfig();
        $this->unsetPriceVisibilityConfig();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Disable catalog permission config
     */
    public function disableCatalogPermission() {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        // Disable catalog permission.
        $pathDisableCatalogPermission = ConfigInterface::XML_PATH_ENABLED;
        $valueDisableCatalogPermission = 0;
        $this->configWriter->save($pathDisableCatalogPermission, $valueDisableCatalogPermission, $scope, $scopeId);
    }

    /**
     * Save allow add to cart config to all
     */
    public function unsetAddToCartConfig() {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        // Save Allow Adding to Cart Permission to all
        $pathAddToCart = ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS;
        $valueAddToCart = ConfigInterface::GRANT_ALL;
        $this->configWriter->save($pathAddToCart, $valueAddToCart, $scope, $scopeId);

        // Save Allow Adding to Cart Permission to all groups
        $pathAddToCartGroup = self::PATH_PREFIX . self::GRANT_CHECKOUT_ITEMS_GROUPS;
        $this->configWriter->save($pathAddToCartGroup, '', $scope, $scopeId);
    }

    /**
     * Save Display price config to all
     */
    public function unsetPriceVisibilityConfig() {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        // Save Display Product Prices Permission to all
        $pathDisplayPrice = ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE;
        $valueDisplayPrice = ConfigInterface::GRANT_ALL;
        $this->configWriter->save($pathDisplayPrice, $valueDisplayPrice, $scope, $scopeId);

        // Save Display Product Prices to all groups
        $pathDisplayPriceGroup = self::PATH_PREFIX . self::GRANT_CATALOG_PRODUCT_PRICE_GROUPS;
        $this->configWriter->save($pathDisplayPriceGroup, '', $scope, $scopeId);
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
