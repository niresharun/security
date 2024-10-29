<?php
/**
 * Enable Cookie Restriction Mode
 * @category: Magento
 * @package: Perficient/Seo
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<sreedevi.selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Seo
 */



namespace Perficient\Seo\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\Config\Backend\Encrypted;
/**
 * Patch script to disable UPS and BlueShip config data
 */
class CookieBrowserUpdates implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const COOKIE_RESTRICTION = 'web/cookie/cookie_restriction';
    const COOKIE_LOCALSTORAGE = 'web/browser_capabilities/local_storage';
    const SCOPE_ID = 0;
    /**#@-*/

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Encrypted $encrypted
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Encrypted $encrypted
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setEnableCookiesRestriction();
        $this->setEnableCookiesLocalStorage();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function setEnableCookiesRestriction() {
        $pathCookie = self::COOKIE_RESTRICTION;
        $valueEnable = 1;
        $this->configWriter->save($pathCookie, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    private function setEnableCookiesLocalStorage() {
        $pathCookie = self::COOKIE_LOCALSTORAGE;
        $valueEnable = 1;
        $this->configWriter->save($pathCookie, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
