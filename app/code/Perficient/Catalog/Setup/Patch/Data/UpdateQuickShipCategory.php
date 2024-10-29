<?php

/**
 * Disable Quick Ship Category
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain <hiral.jain@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */

declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\CategoryRepository;

use Magento\Catalog\Model\CategoryFactory;

class UpdateQuickShipCategory implements DataPatchInterface
{
    /**
     * UpdateQuickShipCategory constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param CategoryRepository $categoryRepository
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        protected ModuleDataSetupInterface  $setup,
        protected Config                    $eavConfig,
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryFactory    $categoryFactory
    )
    {
    }

    /**
     * @return DataPatchInterface|void
     */
    public function apply()
    {
        try {
            $categoryName = 'Quick Ship';
            $collection = $this->categoryFactory->create()->getCollection();
            $collection->addFieldToFilter('name', ['eq' => $categoryName]);
            $collection->setOrder('entity_id', 'ASC');
            $collection->setPageSize(1);

            foreach ($collection as $cat) {
                $catObj = $this->categoryRepository->get($cat->getId());
                $catObj->setIsActive(0)->save();

            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * The default magento OOB method used to get aliases.
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * The default magento OOB method used to get dependencies.
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
