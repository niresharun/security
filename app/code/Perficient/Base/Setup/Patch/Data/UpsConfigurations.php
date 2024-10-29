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
use Magento\Config\Model\Config\Backend\Encrypted;
/**
 * Patch script for ups config data
 */
class UpsConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const MODULE_ENABLED = 'active';

    final const MODE_XML = 'mode_xml';

    final const USERNAME = 'username';

    final const ACCESS_LICENSE_NUMBER = 'access_license_number';

    final const PASSWORD = 'password';

    final const ALLOWED_METHODS = 'allowed_methods';

    final const PATH_PREFIX = 'carriers/ups/';

    final const SCOPE_ID = 0;
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
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setEnabled();
        $this->setModeXml();
        $this->setAccessLicenseNumber();
        $this->setAllowedMethods();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Enabled for Checkout
     */
    public function setEnabled(): void
    {
        $pathEnable = self::PATH_PREFIX . self::MODULE_ENABLED;
        $valueEnable = 1;
        $this->configWriter->save($pathEnable, $valueEnable, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Mode
     */
    public function setModeXml(): void
    {

        $pathModeXml = self::PATH_PREFIX . self::MODE_XML;
        $valueModeXml = 0;
        $this->configWriter->save($pathModeXml, $valueModeXml, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    /**
     * Access License Number
     */
    public function setAccessLicenseNumber(): void
    {
        $pathAccessLicenseNumber = self::PATH_PREFIX . self::ACCESS_LICENSE_NUMBER;
        $valueAccessLicenseNumber = '8D8B2BBD731468D5';
        $this->encrypted->setPath('carriers/usp/access_license_number');
        $this->encrypted->setScopeId(0);
        $this->encrypted->setScope('default');
        $this->encrypted->setScopeCode('');
        $this->encrypted->setValue($valueAccessLicenseNumber);
        $this->encrypted->save();
        $this->configWriter->save($pathAccessLicenseNumber, $this->encrypted->getValue(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Allowed Methods
     */
    public function setAllowedMethods(): void
    {
        $pathAllowedMethods = self::PATH_PREFIX . self::ALLOWED_METHODS;
        $valueAllowedMethods = '03';
        $this->configWriter->save($pathAllowedMethods, $valueAllowedMethods, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
