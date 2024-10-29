<?php
/**
 * Module to correct the attributes type.
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

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Patch script for ups config data
 */
class ZattributesTypeCorrection implements DataPatchInterface
{
    /**
     * Amasty Amshopby Filter Setting
     */
    const AMASTY_AMSHOPBY_FILTER_SETTING_TABLE = 'amasty_amshopby_filter_setting';

    /**
     * ConfigData constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $this->correctAttributesType();
        $this->setAmastyFilterSettings();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Correct Attributes Type
     */
    public function correctAttributesType()
    {
        $connection = $this->resourceConnection->getConnection();

        $connection->update(
            'eav_attribute',
            [
                'frontend_input' => 'multiselect',
                'backend_type' => 'text'
            ],
            'attribute_code IN (
                "simplified_medium", "simplified_size", "lifestyle", "licensed_collection", "orientation"
            ) AND entity_type_id = 4'
        );

        $connection->update(
            'eav_attribute',
            [
                'frontend_input' => 'select',
                'backend_type' => 'text'
            ],
            'attribute_code IN ("filter_thickness", "mat_type") AND entity_type_id = 4'
        );
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
            'attr_color','1','5','0','1.0000','1','','0','0','0','0','0','0','0','{"1":"","2":""}','0','0',
            'visible_everywhere','','','','0',NULL,NULL,'1','0','0','0', '0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 5]
        );

        $data = [
            'attr_licensed_collection','1','0','0','1.0000','0','','0','0','0','0','0','0','0',
            '{\"1\":\"\",\"2\":\"\"}','0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_lifestyle','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0','0',
            'visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_orientation','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0',
            '0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_simplified_medium','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
            '0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_simplified_size','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0'
            ,'0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_filter_size','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0','0'
            ,'visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_frame_type','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0','0',
            'visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_color_frame','1','5','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0','0'
            ,'visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 5]
        );

        $data = [
            'attr_color_mat','1','5','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}','0','0',
            'visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 5]
        );

        $data = [
            'attr_color_family_mat','1','5','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
            '0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 5]
        );

        $data = [
            'attr_color_family_frame','1','5','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
            '0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 5]
        );

        $data = [
            'attr_frame_width_range','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
            '0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_filter_thickness','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
            '0','0','visible_everywhere','','','','0',NULL,NULL,'1','0','0','0','0'
        ];
        $columnVal = array_combine($filterColumns, $data);
        $connection->insertOnDuplicate(
            self::AMASTY_AMSHOPBY_FILTER_SETTING_TABLE,
            $columnVal,
            ['is_multiselect' => 1, 'display_mode' => 0]
        );

        $data = [
            'attr_mat_type','1','0','0','1.0000','0','','0','0','0','0','0','0','0','{\"1\":\"\",\"2\":\"\"}',
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
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
