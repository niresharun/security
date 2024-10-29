<?php
/**
 * Display shipping policy on checkout page
 * @category: Magento
 * @package: Perficient/Shipping
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Shipping
 */

namespace Perficient\Shipping\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for shipping config data
 */
class ConfigData implements DataPatchInterface
{
    /**
     * Prefix path for Shipping Policy
     */
    const PATH_PREFIX = 'shipping/shipping_policy/';

    /**
     * Enable shipping policy config
     */
    const ENABLE_SHIPPING_POLICY = 'enable_shipping_policy';

    /**
     * Shipping policy content config
     */
    const SHIPPING_POLICY_CONTENT = 'shipping_policy_content';

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
        $this->enableCatalogPermission();
        $this->setShippingPolicyContent();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enable Shipping Policy
     */
    public function enableCatalogPermission()
    {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        // Enable Shipping Policy
        $pathEnableShippingPolicy = self::PATH_PREFIX . self::ENABLE_SHIPPING_POLICY;
        $valueEnableShippingPolicy = '1';
        $this->configWriter->save($pathEnableShippingPolicy, $valueEnableShippingPolicy, $scope, $scopeId);
    }

    /**
     * Save Shipping policy content
     */
    public function setShippingPolicyContent()
    {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        $pathShippingPolicyContent = self::PATH_PREFIX . self::SHIPPING_POLICY_CONTENT;
        $valueShippingPolicyContent = $this->getShippingPolicyContent();
        $this->configWriter->save($pathShippingPolicyContent, $valueShippingPolicyContent, $scope, $scopeId);
    }

    /**
     * Get Default shipping policy content
     * @return string
     */
    private function getShippingPolicyContent()
    {

        $content = <<<EOT
Free standard shipping is available for all orders within the contiguous U.S.

Free standard shipping is available for orders over $49, that are shipped to Alaska, Hawaii, and Puerto Rico.
EOT;

        return $content;
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
