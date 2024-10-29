<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey Pali <Kartikey.Pali@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;

/**
 * Class ThemeConfigurations
 * @package Perficient\Base\Setup\Patch\Data
 */
class ThemeConfigurations implements DataPatchInterface
{
    /**
     * Constant for default theme name.
     */
    final const THEME_NAME = 'Perficient/wendover';

    /**
     * Constant for theme path.
     */
    final const THEME_PATH = 'design/theme/theme_id';

    /**
     * Constant for theme path.
     */
    final const THEME_STORES = 'stores';

    /**
     * ThemeConfigurations constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigInterface $resourceConfig
     * @param StoreRepositoryInterface $storeRepository
     * @param ThemeProviderInterface $themeProviderInterface
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ConfigInterface $resourceConfig,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly ThemeProviderInterface $themeProviderInterface
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->assignTheme();
        $this->moduleDataSetup->endSetup();
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

    /**
     * Get Default Store Id
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreId(): int|string
    {
        $storeId = '';
        $store = $this->storeRepository->get('default');
        if ($store) {
            $storeId = $store->getId();
        }
        return $storeId;
    }

    /**
     * Method used to assign the theme as default.
     *
     * @throws \Exception
     */
    private function assignTheme(): void
    {
        try {
            $area     = DesignInterface::DEFAULT_AREA;
            $fullPath = $area . ThemeInterface::PATH_SEPARATOR . self::THEME_NAME;
            $theme    = $this->themeProviderInterface->getThemeByFullPath($fullPath);

            $this->resourceConfig->saveConfig(
                self::THEME_PATH,
                $theme->getId(),
                self::THEME_STORES,
                $this->getStoreId()
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
