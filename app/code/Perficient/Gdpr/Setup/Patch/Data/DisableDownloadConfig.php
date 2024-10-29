<?php
/**
 * Admin configuration of Gdpr
 * @category: Magento
 * @package: Perficient/Gdpr
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Daniel Wargolet <Daniel.Wargolet@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Gdpr
 */

namespace Perficient\Gdpr\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Patch script for gdpr config data
 */
class DisableDownloadConfig implements DataPatchInterface
{
    /**
     * Configuration paths
     */
    final public const XML_GDPR_DOWNLOAD_PATH = 'amasty_gdpr/customer_access_control/download';
    final public const XML_CCPA_DOWNLOAD_PATH = 'amasty_ccpa/customer_access_control/download';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ConfigInterface $resourceConfig,
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setDownloadConfigs(0,ScopeConfigInterface::SCOPE_TYPE_DEFAULT,0);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Set GDPR configuration
     * @param $value
     * @param $scope
     * @param $scopeId
     */
    private function setGDPRDownloadConfig($value, $scope, $scopeId)
    {
        $this->updateRecord(self::XML_GDPR_DOWNLOAD_PATH, $value, $scope, $scopeId);
    }

    /**
     * Set CCPA configuration
     * @param $value
     * @param $scope
     * @param $scopeId
     */
    private function setCCPADownloadConfig($value, $scope, $scopeId)
    {
        $this->updateRecord(self::XML_CCPA_DOWNLOAD_PATH, $value, $scope, $scopeId);
    }

    /**
     * Set configurations
     * @param $value
     * @param $scope
     * @param $scopeId
     */
    private function setDownloadConfigs($value, $scope, $scopeId)
    {
        $this->setGDPRDownloadConfig($value, $scope, $scopeId);
        $this->setCCPADownloadConfig($value, $scope, $scopeId);
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

    /**
     * Update an existing configuration
     * @param $path
     * @param $value
     * @param $scope
     * @param $scopeId
     */
    private function updateRecord($path, $value, $scope, $scopeId)
    {
        $this->resourceConfig->saveConfig(
            $path,
            $value,
            $scope,
            $scopeId
        );
    }
}
