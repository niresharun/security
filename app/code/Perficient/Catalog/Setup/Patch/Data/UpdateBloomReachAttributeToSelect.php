<?php
/**
 * Update existing product attributes.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Archana Lohakare <archana.lohakare@Perficient.com>
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
 * Class UpdateBloomReachAttributeToSelect
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateBloomReachAttributeToSelect implements DataPatchInterface
{
    /**
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_SET = 'Mat';

    /**
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_GROUP = 'Mat Additional Information';

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
        $this->UpdateBloomReachAttributeToSelect();
    }

    /**
     * Create custom product attribute
     */
    private function UpdateBloomReachAttributeToSelect(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        if (!$eavSetup->getAttributeId(Product::ENTITY, 'filter_thickness')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'filter_thickness');
        }
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::MAT_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::MAT_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'filter_thickness',
            [
                'group' => $groupName,
                'type' => 'int',
                'input' => 'select',
                'label' => 'Filter Thickness',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 9911,
                'option' => ['values' => ['Single', 'Double', 'Triple']],
            ]
        );

        if (!$eavSetup->getAttributeId(Product::ENTITY, 'mat_type')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'mat_type');
        }
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::MAT_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::MAT_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'mat_type',
            [
                'group' => $groupName,
                'type' => 'int',
                'input' => 'select',
                'label' => 'Mat Type',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 9912,
                'option' => ['values' => ['Oversized', 'Oversized', 'Standard']],
            ]
        );

        if (!$eavSetup->getAttributeId(Product::ENTITY, 'color_frame')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'color_frame');
        }
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::FRAME_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::FRAME_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_frame',
            [
                'group' => $groupName,
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Color Frame',
                'input' => 'multiselect',
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
                'sort_order' => 1005
            ]
        );

        if (!$eavSetup->getAttributeId(Product::ENTITY, 'color_mat')) {
            $eavSetup->removeAttribute(Product::ENTITY, 'color_mat');
        }
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::MAT_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::MAT_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'color_mat',
            [
                'group' => $groupName,
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Color Mat',
                'input' => 'multiselect',
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
                'sort_order' => 1006
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
