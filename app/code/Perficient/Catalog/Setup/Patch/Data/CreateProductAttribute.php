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
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CreateProductCustomAttribute
 * product custom attribute
 */
class CreateProductAttribute implements DataPatchInterface
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
        $this->TypesOfAttribute();
    }

    /**
     * Create custom product attribute
     */
    private function TypesOfAttribute(): void
    {
        $m2Attribute = ['color_swatch' => 'text--Color Swatch--Default', 'art_mount_type' => 'text--Art Mount Type--Art',
            'mirror_bevel' => 'text--Mirror Bevel--Art', 'wag_number' => 'text--WAG Number--Art',
            'artist_name' => 'text--Artist Name--Art', 'bottom_mat_size_bottom' => 'text--Bottom Mat Size Bottom--Art',
            'bottom_mat_size_left' => 'text--Bottom Mat Size Left--Art', 'bottom_mat_size_right' => 'text--Bottom Mat Size Right--Art'
            , 'bottom_mat_size_top' => 'text--Bottom Mat Size Top--Art',
            'category_list' => 'multiselect--Category List--Art', 'configuration_level' => 'select--Configuration Level--Art',
            'image_height' => 'text--Image Height--Art', 'image_width' => 'text--Image Width--Art',
            'keyword_list' => 'text--Keyword List--Art',
            'licensed_collection' => 'multiselect--Licensed Collection--Art', 'lifestyle' => 'multiselect--Lifestyle--Art',
            'max_image_height' => 'text--Max Image Height--Art', 'max_image_width' => 'text--Max Image Width--Art',
            'media_category' => 'text--Media Category--Art', 'orientation' => 'multiselect--Orientation--Art',
            'other_skus_in_series' => 'text--Other Items in Series--Art',
            'top_mat_size_bottom' => 'text--Top Mat Size Bottom--Art', 'top_mat_size_left' => 'text--Top Mat Size Left--Art',
            'top_mat_size_right' => 'text--Top Mat Size Right--Art', 'top_mat_size_top' => 'text--Top Mat Size Top--Art',
            'frame_material' => 'select--Frame Material--Frame', 'frame_rabbet_depth' => 'text--Frame Rabbet Depth--Frame',
            'max_outer_size' => 'text--Max Outer Size--Frame', 'moulding_waste_pct' => 'text--Moulding Waste PTC--Frame',
            'landed_cost_per_foot' => 'text--Landed Cost Per Foot--Frame', 'color_family' => 'text--Color Family--Frame',
            'frame_family' => 'text--Frame Family--Frame', 'mat_type' => 'select--Mat Type--Mat',
            'fabric_cost_per_lin_ft' => 'text--Fabric Cost Per Lin Ft--Mat', 'specialty_note' => 'text--Speciality Note--Mat',
            'filter_thickness' => 'select--Filter Thickness--Mat', 'filter_type' => 'text--Filter Type--Mat', 'filter_medium' => 'text--Filter Medium--Art',
            'treatment' => 'text--Treatment--Art', 'related_items' => 'text--Related Item--Art', 'year_added' => 'text--Year Added--Art'
            , 'color_family_frame' => 'multiselect--Frame Color Family--Frame',
            'color_family_mat' => 'multiselect--Mat Color Family--Mat',
            'frame_width_range' => 'select--Frame Width Range--Frame',
            'color_frame' => 'multiselect--Color Frame--Frame',
            'color_mat' => 'multiselect--Color Mat--Mat',
            'simplified_medium' => 'multiselect--Simplified Medium--Default',
            'simplified_size' => 'multiselect--Simplified Size--Default',
            'default_configurations' => 'textarea--Default Configurations--Default',
            'is_quick_ship' => 'boolean--Is Quick Ship--Default'
        ];

        //$m2Attribute = ['prft_art_mount_type' => 'select--Art Mount Type--Art'];

        $text = [
            'user_defined' => true,
            'type' => Table::TYPE_TEXT,
            'backend' => '',
            'frontend' => '',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => ''
        ];
        $textarea = [
            'user_defined' => true,
            'type' => Table::TYPE_TEXT,
            'backend' => '',
            'frontend' => '',
            'input' => 'textarea',
            'class' => '',
            'source' => '',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'apply_to' => ''
        ];
        $boolean = [
            'user_defined' => true,
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Is Quick Ship',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'visible_in_advanced_search' => true,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => ''
        ];
        $select = [
            'user_defined' => true,
            'type' => Table::TYPE_TEXT,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
            'input' => 'select',
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
            'apply_to' => ''
        ];
        $multiSelect = [
            'user_defined' => true,
            'type' => Table::TYPE_TEXT,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
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
            'apply_to' => ''
        ];
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach ($m2Attribute as $key => $value) {
            $getValues = explode('--', $value);
            $label = trim($getValues[1]);
            $attributeSet = trim($getValues[2]);
            $type = trim($getValues[0]);
            if ($attributeSet == 'Art') {
                $group = 'Art';
            }
            if ($attributeSet == 'Default') {
                $group = 'Product Details';
            }
            if ($attributeSet == 'Frame') {
                $group = 'Frame Additional Information';
            }
            if ($attributeSet == 'Mat') {
                $group = 'Mat Additional Information';
            }
            if ($type == 'text') {
                $attributeData = $text;
            }
            if ($type == 'textarea') {
                $attributeData = $textarea;
            }
            if ($type == 'boolean') {
                $attributeData = $boolean;
            }
            if ($type == 'select') {
                $attributeData = $select;
            }
            if ($type == 'multiselect') {
                $attributeData = $multiSelect;
            }

            $attributeData['label'] = $label;

            //$attributeData['group'] = $group;
            $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
            $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, $attributeSet);
            $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $group);
            $groupName = $eavSetup->getAttributeGroup(Product::ENTITY, $attributeSetId, $attributeGroupId, 'attribute_group_name');

            if (!$eavSetup->getAttributeId(Product::ENTITY, $key)) {
                $eavSetup->removeAttribute(Product::ENTITY, $key);
            }
            $eavSetup->addAttribute(
                Product::ENTITY,
                $key, $attributeData);

            $eavSetup->addAttributeToGroup(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeSetId, //set
                $group, // group
                $key,
                '' // sort order
            );
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
