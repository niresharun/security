<?php
/**
 * Set custom configurations for theme
 * @category: Magento
 * @package: Perficient/Theme
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Daniel Wargolet <Daniel.Wargolet@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Theme
 */
declare(strict_types=1);

namespace Perficient\Theme\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for Theme configuration
 */
class ConfigData implements DataPatchInterface
{
    /**
     * Configuration paths
     * @const string
     */
    final public const XML_PATH_DEFAULT_DESC        = 'design/head/default_description';
    final public const XML_PATH_ROBOTS_INSTRUCTIONS = 'design/search_engine_robots/custom_instructions';
    final public const XML_PATH_LOGO_WIDTH          = 'design/header/logo_width';
    final public const XML_PATH_LOGO_ALT            = 'design/header/logo_alt';

    /**
     * Default Title
     */
    final public const DEFAULT_TITLE = 'Wendover Art Group';

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ConfigData constructor.
     */
    public function __construct(
        WriterInterface $configWriter,
        ModuleDataSetupInterface $moduleDataSetup,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configWriter = $configWriter;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->applyThemeSettings();
        $this->moduleDataSetup->getConnection()->endSetup();
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
     * Apply all of the configurations
     */
    public function applyThemeSettings()
    {
        $this->setDefaultMetaDescription(ScopeInterface::SCOPE_WEBSITES, 1);
        //$this->setRobotsInstruction(ScopeInterface::SCOPE_WEBSITES,1);
        $this->setLogoWidth(280,ScopeInterface::SCOPE_STORES,1);
        $this->setLogoWidth(280,ScopeInterface::SCOPE_WEBSITES,1);
        $this->setLogoAlt(self::DEFAULT_TITLE,ScopeInterface::SCOPE_STORES,1);
        $this->setLogoAlt(self::DEFAULT_TITLE,ScopeInterface::SCOPE_WEBSITES,1);
    }

    /**
     * @param string $path
     * @param string $scope
     * @param int $scopeId
     * @return mixed
     */
    public function checkRecordExists($path, $scope, $scopeId)
    {
        return $this->scopeConfig->getValue($path, $scope, $scopeId);
    }

    /**
     * @param string $scope
     * @param int $scopeId
     */
    private function setDefaultMetaDescription($scope, $scopeId)
    {
        if (!$this->checkRecordExists(self::XML_PATH_DEFAULT_DESC, $scope, $scopeId)) {
            $this->setRecord(self::XML_PATH_DEFAULT_DESC, $this->getDefaultMetaDescriptionVal(), $scope, $scopeId);
        }
    }

    /**
     * @param string $scope
     * @param int $scopeId
     */
    private function setRobotsInstruction($scope, $scopeId)
    {
        /*if (!$this->checkRecordExists(self::XML_PATH_ROBOTS_INSTRUCTIONS, $scope, $scopeId)) {
            $this->setRecord(self::XML_PATH_ROBOTS_INSTRUCTIONS, $this->getRobotsInstructions(), $scope, $scopeId);
        }*/
    }

    /**
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     */
    private function setLogoWidth($value, $scope, $scopeId)
    {
        if (!$this->checkRecordExists(self::XML_PATH_LOGO_WIDTH, $scope, $scopeId)) {
            $this->setRecord(self::XML_PATH_LOGO_WIDTH, $value, $scope, $scopeId);
        }
    }

    /**
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     */
    private function setLogoAlt($value, $scope, $scopeId)
    {
        if (!$this->checkRecordExists(self::XML_PATH_LOGO_ALT, $scope, $scopeId)) {
            $this->setRecord(self::XML_PATH_LOGO_ALT, $value, $scope, $scopeId);
        }
    }

    /**
     * Save record/row
     * @param string $path
     * @param string $scope
     * @param int $scopeId
     */
    private function setRecord($path, mixed $value, $scope, $scopeId)
    {
        $this->configWriter->save($path, $value, $scope, $scopeId);
    }

    /**
     * The default Meta description
     * @return string
     */
    private function getDefaultMetaDescriptionVal()
    {
        return 'Wendover Art Group, creator and manufacturer of distinctive wall decor serving the retail (large and independent), interior design (commercial and residential), hospitality and healthcare industries. .';
    }

    /**
     * Search Engine Robot instructions
     * @return string
     */
    private function getRobotsInstructions()
    {
        /*return 'User-agent: *
					Disallow: /index.php/
					Disallow: /*?
					Disallow: /checkout/
					Disallow: /app/
					Disallow: /lib/
					Disallow: /*.php$
					Disallow: /pkginfo/
					Disallow: /report/
					Disallow: /var/
					Disallow: /catalog/
					Disallow: /customer/
					Disallow: /sendfriend/
					Disallow: /review/
					Disallow: /*SID=
					Disallow: /customer/account/login/
					Disallow: filter_size
					';
		*/
    }
}
