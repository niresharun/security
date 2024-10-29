<?php
/**
 * Add New field in catalog product
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Archana Lohakare<archana.lohakare@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class CreateFrameWidthRangeAttribute
 * product custom attribute
 */
class CreateFrameWidthRangeAttribute implements DataPatchInterface
{
    /**
     * frame attribute set.
     */
    const FRAME_ATTRIBUTE_SET = 'Frame';

    /**
     * frame attribute set.
     */
    const FRAME_ATTRIBUTE_GROUP = 'Frame Additional Information';

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
        $this->CreateFrameWidthRangeAttribute();
    }

    /**
     * Create custom product attribute
     */
    private function CreateFrameWidthRangeAttribute(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::FRAME_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::FRAME_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'frame_width_range',
            [
                'group' => $groupName,
                'type' => 'int',
                'input' => 'select',
                'label' => 'Frame Width Range',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 9910,
                'option' =>
                    [
                        'values' =>
                            ['1"', '1.5"', '2"'],
                    ],
            ]
        );
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
