<?php
/**
 * Add New field in catalog product For DCKAP
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
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
 * Class CreateProductBloomReachFeedAttribute
 * product custom attribute
 */
class CreateProductBloomReachFeedAttribute implements DataPatchInterface
{
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
        //$this->createProductBloomReachFeedAttribute();
    }

    /**
     * Create custom product attribute
     */
    /*private function createProductBloomReachFeedAttribute(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
        $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

        $eavSetup->addAttribute(
            Product::ENTITY,
            'simplified_medium',
            [
                'group' => $groupName,
                'type' => Table::TYPE_TEXT,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Simplified Medium',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'simplified_size',
            [
                'group' => $groupName,
                'type' => Table::TYPE_TEXT,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Simplified Size',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'lifestyle',
            [
                'group' => $groupName,
                'type' => Table::TYPE_TEXT,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Lifestyle',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
                'sort_order' => 1005,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'licensed_collection',
            [
                'group' => $groupName,
                'type' => Table::TYPE_TEXT,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Licensed Collection',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
                'sort_order' => 1006,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'orientation',
            [
                'group' => $groupName,
                'type' => Table::TYPE_TEXT,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Orientation',
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
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
                'sort_order' => 1007,
            ]
        );
    }*/

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
