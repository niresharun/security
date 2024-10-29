<?php
/**
 * Admin configuration of GoogleTagManager
 * @category: Magento
 * @package: Perficient/GoogleTagManager
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde<trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_GoogleTagManager
 */
namespace Perficient\GoogleTagManager\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for GoogleTagManager config data
 */
class ConfigData implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final public const PATH_PREFIX = 'google/analytics/';

    final public const MODULE_ACTIVE = 'active';

    final public const CONTAINER_ID = 'container_id';

    final public const ACCOUNT_TYPE = 'type';

    final public const ANONYMIZE = 'anonymize';

    final public const EXPERIMENTS = 'experiments';

    final public const SCOPE_ID = 0;

    final public const MODULE_ACTIVE_VALUE  = 1;

    final public const ACCOUNT_TYPE_VALUE  = 'tag_manager';

    final public const ANONYMIZE_VALUE  = 0;

    final public const EXPERIMENTS_VALUE  = 0;

    final public const CONTAINER_ID_VALUE  = 'GTM-NJTMN9L';
    /**#@-*/
    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;


    private array $configData = [
        self::PATH_PREFIX . self::MODULE_ACTIVE => self::MODULE_ACTIVE_VALUE,
        self::PATH_PREFIX . self::ACCOUNT_TYPE => self::ACCOUNT_TYPE_VALUE,
        self::PATH_PREFIX . self::ANONYMIZE => self::ANONYMIZE_VALUE,
        self::PATH_PREFIX . self::EXPERIMENTS => self::EXPERIMENTS_VALUE,
        self::PATH_PREFIX . self::CONTAINER_ID => self::CONTAINER_ID_VALUE

    ];


    /**
     * ConfigData constructor.
     */
    public function __construct(
        WriterInterface $configWriter,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->configWriter = $configWriter;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        foreach($this->configData as $key=>$value){
            $this->configWriter->save($key, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        }
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
}
