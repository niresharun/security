<?php
/**
 * Enable Persistent Shopping Cart
 * @category: Magento
 * @package: Perficient/Persistent
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Persistent
 */
declare(strict_types=1);

namespace Perficient\Persistent\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for Persistent Shopping Cart
 */
class ConfigData implements DataPatchInterface
{
    /**
     * Prefix path for PersistentShoppingCart
     */
    final public const PATH_PREFIX = 'persistent/options/';

    /**
     * @cont string
     */
    final public const XML_PATH_ENABLED = self::PATH_PREFIX . 'enabled';

    /**
     * @cont string
     */
    final public const XML_PATH_LIFE_TIME = self::PATH_PREFIX . 'lifetime';

    /**
     * @cont string
     */
    final public const XML_PATH_LOGOUT_CLEAR = self::PATH_PREFIX . 'logout_clear';

    /**
     * @cont string
     */
    final public const XML_PATH_REMEMBER_ME_ENABLED = self::PATH_PREFIX . 'remember_enabled';

    /**
     * @cont string
     */
    final public const XML_PATH_REMEMBER_ME_DEFAULT = self::PATH_PREFIX . 'remember_default';

    /**
     * @cont string
     */
    final public const XML_PATH_PERSIST_SHOPPING_CART = self::PATH_PREFIX . 'shopping_cart';

    /**
     * @cont string
     */
    final public const XML_PATH_PERSIST_WISHLIST = self::PATH_PREFIX . 'wishlist';

    /**
     * @cont string
     */
    final public const XML_PATH_PERSIST_RECENTLY_ORDERED = self::PATH_PREFIX . 'recently_ordered';

    /**
     * @cont string
     */
    final public const XML_PATH_PERSIST_COMPARE_CURRENT = self::PATH_PREFIX . 'compare_current';

    /**
     * @cont string
     */
    final public const XML_PATH_PERSIST_COMPARE_HISTORY = self::PATH_PREFIX . 'compare_history';

    /**
     * @cont string
     */
    final public const XML_PATH_PERSIST_RECENTLY_VIEWED = self::PATH_PREFIX . 'recently_viewed';


    private array $configData = [
        self::XML_PATH_ENABLED => 1,
        self::XML_PATH_LIFE_TIME => 31536000,
        self::XML_PATH_REMEMBER_ME_ENABLED => 1,
        self::XML_PATH_REMEMBER_ME_DEFAULT => 1,
        self::XML_PATH_LOGOUT_CLEAR => 1,
        self::XML_PATH_PERSIST_SHOPPING_CART => 1,
        self::XML_PATH_PERSIST_WISHLIST => 1,
        self::XML_PATH_PERSIST_RECENTLY_ORDERED => 1,
        self::XML_PATH_PERSIST_COMPARE_CURRENT => 1,
        self::XML_PATH_PERSIST_COMPARE_HISTORY => 1,
        self::XML_PATH_PERSIST_RECENTLY_VIEWED => 1
    ];

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
        $this->setupPersistentShoppingCart();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Setup Persistent Shopping Cart
     */
    public function setupPersistentShoppingCart() {
        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        foreach($this->configData as $configuration => $value){
            $this->configWriter->save($configuration, $value, $scope, $scopeId);
        }
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
