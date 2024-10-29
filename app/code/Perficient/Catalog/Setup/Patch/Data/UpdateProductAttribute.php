<?php
/**
 * Update New field in catalog product For DCKAP
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde<trupti.bobde@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class UpdateCustomerAttribute
 * product custom attribute
 */
class UpdateProductAttribute implements DataPatchInterface
{
    /**
     * frame max size attribute
     */
    const FRAME_MAX_SIZE_ATTRIBUTE = 'frame_max_size';

    /**
     * default item price attribute
     */
    const DEFAULT_ITEM_PRICE_ATTRIBUTE = 'default_item_price';

    /**
     * CreateProductCustomAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    )
    {
    }

    /**
     * @inheritdoc
     */

    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->updateAttribute(Product::ENTITY, self::FRAME_MAX_SIZE_ATTRIBUTE, 'used_for_sort_by', 0);
        $eavSetup->updateAttribute(Product::ENTITY, self::DEFAULT_ITEM_PRICE_ATTRIBUTE, 'used_for_sort_by', 0);
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
