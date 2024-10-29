<?php
/**
 * Update existing product attributes.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class UpdateProductBloomReachFeedAttribute
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class CreateColorFamilyMultiselectAttribute implements DataPatchInterface
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
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_SET = 'Mat';

    /**
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_GROUP = 'Mat Additional Information';

    /**
     * CreateProductCustomAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Attribute $eavAttribute
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Attribute                $eavAttribute
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->CreateColorFamilyMultiselectAttribute();
    }

    /**
     * Create custom product attribute
     */
    private function CreateColorFamilyMultiselectAttribute(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::FRAME_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::FRAME_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_family_frame',
            [
                'group' => $groupName,
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Frame Color Family',
                'input' => 'multiselect',
                'class' => '',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
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
                'sort_order' => 1003,
                'option' => [
                    'value' => [
                        'option_1' => ['Gold Family'],
                        'option_2' => ['Wood Tone Family'],
                        'option_3' => ['Black Family'],
                        'option_4' => ['White Family'],
                        'option_5' => ['Natural Family']
                    ],
                    'order' => [
                        'option_1' => 1,
                        'option_2' => 2,
                        'option_3' => 3,
                        'option_4' => 4,
                        'option_5' => 5,
                    ],
                ],
            ]
        );

        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::MAT_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::MAT_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_family_mat',
            [
                'group' => $groupName,
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Mat Color Family',
                'input' => 'multiselect',
                'class' => '',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
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
                'sort_order' => 1004,
                'option' => [
                    'value' => [
                        'option_1' => ['White Family'],
                        'option_2' => ['Primary Family'],
                        'option_3' => ['Linen Family'],
                        'option_4' => ['Natural Family']
                    ],
                    'order' => [
                        'option_1' => 1,
                        'option_2' => 2,
                        'option_3' => 3,
                        'option_4' => 4,
                    ],
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
