<?php
/**
 * Update product attribute position, so that they will appear as per the mock-up.
 *
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

/**
 * Class UpdateProductAttributePosition
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateProductAttributePosition implements DataPatchInterface
{
    /**
     * Color attribute
     */
    const ATTR_COLOR = 'color';

    /**
     * Position field
     */
    const FIELD_POSITION = 'position';

    private array $attributesAndPosition = [
        'category_list' => 0,
        'licensed_collection' => 1,
        'lifestyle' => 2,
        'color' => 3,
        'filter_size' => 4,
        'simplified_size' => 4,
        'simplified_medium' => 5,
        'orientation' => 6,
        'price' => 7,
    ];

    /**
     * UpdateProductAttributePosition constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Attribute $eavAttribute
     * @param Config $eavConfig
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Attribute                $eavAttribute,
        private readonly Config                   $eavConfig
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach ($this->attributesAndPosition as $attribute => $position) {
            // Check, if attribute is exists or not.
            $attr = $this->eavConfig->getAttribute(Product::ENTITY, $attribute);
            if ($attr && $attr->getId()) {
                $eavSetup->updateAttribute(Product::ENTITY, $attribute, self::FIELD_POSITION, $position);
            }
        }

        /**
         * Change the label of Color List to Color attribute.
         */
        $attributeId = $this->eavAttribute->getIdByCode(Product::ENTITY, self::ATTR_COLOR);
        if ($attributeId) {
            $eavSetup->updateAttribute(Product::ENTITY, $attributeId, 'frontend_label', 'Color');
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
