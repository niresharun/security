<?php
/**
 * Module to correct the attributes.
 *
 * @category: PHP
 * @package: Perficient/Zsetup
 * @copyright:
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suraj Jaiswal <suraj.jaiswal@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Zsetup
 */

namespace Perficient\Zsetup\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for ups config data
 */
class ZbattributesCorrections implements DataPatchInterface
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
     * EAV attribute Table
     */
    const EAV_ATTRIBUTE_TABLE = 'eav_attribute';
    /**
     * Catalog EAV Attribute Table
     */
    const CATALOG_EAV_ATTRIBUTE_TABLE = 'catalog_eav_attribute';
    /**
     * Table name
     */
	const AMASTY_AMSHOPBY_FILTER_SETTING_TABLE = 'amasty_amshopby_filter_setting';

    /**
     * ZbattributesCorrections constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ResourceConnection $resourceConnection
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeRepositoryInterface $eavAttributeRepository
     * @param AttributeManagementInterface $attributeManagementInterface
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ResourceConnection $resourceConnection,
        private readonly EavSetupFactory $eavSetupFactory,
        private readonly AttributeRepositoryInterface $eavAttributeRepository,
        private readonly AttributeManagementInterface $attributeManagementInterface
    ) {
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        //Deleting old filter_type attribute
        if ($eavSetup->getAttributeId(Product::ENTITY, 'filter_type')) {
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY,
                'filter_type');
        }
        //Creating new filter_type attribute
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::MAT_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::MAT_ATTRIBUTE_GROUP);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'filter_type',
            [
                'type' => 'int',
                'input' => 'select',
                'label' => 'Type',
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
                'option' => ['values' => ['Standard', 'Fabric']],
            ]
        );
        $this->attributeManagementInterface->assign(
            'catalog_product',
            $attributeSetId,
            $attributeGroupId,
            'filter_type',
            1000
        );
        // add if attribute option is missing for filter size.
        if ($eavSetup->getAttributeId(Product::ENTITY, 'filter_size')) {
            $attributeFilterSize = $this->eavAttributeRepository->get(
                \Magento\Catalog\Model\Product::ENTITY,
                'filter_size'
            );
            $newOptions = ['Standard', 'Oversized'];
            $existingOptions = $attributeFilterSize->getSource()->getAllOptions();
            if(is_array($existingOptions)){
                foreach ($existingOptions as $key => $value) {
                    if (in_array($value['label'], $newOptions)) {
                        $rmKey = array_search($value['label'], $newOptions);
                        if (false !== $rmKey) {
                            unset($newOptions[$rmKey]);
                        }
                    }
                }
            }

            if (!empty($newOptions)) {
                $attributeId = $eavSetup->getAttributeId('catalog_product', 'filter_size');
                $options = [
                    'values' => $newOptions,
                    'attribute_id' => $attributeId,
                ];

                $eavSetup->addAttributeOption($options);
            }
        }

        $this->correctAttributesFilterableProperty();
        $this->correctAttributesLabel();
        $this->setAmastyFilterSettings();

        $this->moduleDataSetup->getConnection()->endSetup();
    }
    /**
     * Correct Attributes Filterable Property
     */
    public function correctAttributesFilterableProperty()
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select();
        $select->reset();

        $select->from(
            ['eav' => $this->resourceConnection->getTableName(self::EAV_ATTRIBUTE_TABLE)],
            ['attribute_id']
        )->where(
            'attribute_code IN ("color_frame", "color_mat") AND entity_type_id = 4'
        );
        $attrData = $connection->fetchCol($select);

        if (is_array($attrData) && !empty($attrData)) {
            $connection->update(
                $this->resourceConnection->getTableName(self::CATALOG_EAV_ATTRIBUTE_TABLE),
                [
                    'is_filterable' => 0,
                    'is_filterable_in_search' => 0
                ],
                'attribute_id IN (' . implode(',', $attrData) . ')'
            );
        }
    }

    /**
     * Correct Attributes Label
     */
    public function correctAttributesLabel()
    {
        $attrCodeLabels = [
            'frame_width_range' => 'Width',
            'frame_type' => 'Type',
            'filter_thickness' => 'Thickness',
            'mat_type' => 'Type'
        ];

        $connection = $this->resourceConnection->getConnection();

        foreach ($attrCodeLabels as $attrCode => $attrLabel) {
            $connection->update(
                $this->resourceConnection->getTableName(self::EAV_ATTRIBUTE_TABLE),
                ['frontend_label' => $attrLabel],
                'attribute_code = "' . $attrCode . '" AND entity_type_id = 4'
            );
        }
    }

    /**
     * set amasty filter settings
     */
    public function setAmastyFilterSettings()
    {
        $filterColumns = [
            'filter_code',
            'is_multiselect',
            'display_mode',
            'is_seo_significant',
            'slider_step',
            'units_label_use_currency_symbol',
            'units_label',
            'index_mode',
            'follow_mode',
            'is_expanded',
            'sort_options_by',
            'show_product_quantities',
            'is_show_search_box',
            'number_unfolded_options',
            'tooltip',
            'is_use_and_logic',
            'add_from_to_widget',
            'visible_in_categories',
            'categories_filter',
            'attributes_filter',
            'attributes_options_filter',
            'block_position',
            'slider_min',
            'slider_max',
            'rel_nofollow',
            'show_icons_on_product',
            'category_tree_display_mode',
            'position_label',
            'limit_options_show_search_box'
        ];

        $connection = $this->resourceConnection->getConnection();

        $data = [
            'attr_filter_type','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
            '0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}

