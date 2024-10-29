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

namespace Wendover\Catalog\Setup\Patch\Data;

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
    private array $attributes = [
        'art_category',
        'category_list',
        'color',
        'color_family_frame',
        'color_family_mat',
        'filter_size',
        'filter_thickness',
        'filter_type',
        'frame_type',
        'frame_width_range',
        'licensed_collection',
        'lifestyle',
        'simplified_size',
        'simplified_medium',
        'orientation'
    ];
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
        foreach ($this->attributes as $key => $value) {
            $eavSetup->updateAttribute(Product::ENTITY, $value, 'is_filterable_in_search', 1);
        }        
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
