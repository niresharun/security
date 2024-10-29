<?php
/**
 * Add New field in catalog product For DCKAP
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateProductCustomAttributesOptions
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateProductAttributesOptions implements DataPatchInterface
{
    /**
     * UpdateProductCustomAttributesOptions constructor.
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param Config $eavConfig
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly \Magento\Eav\Setup\EavSetupFactory         $eavSetupFactory,
        private readonly \Magento\Store\Model\StoreManagerInterface $storeManager,
        private readonly \Magento\Eav\Model\Entity\AttributeFactory $eavAttribute,
        private readonly AttributeOptionManagementInterface         $attributeOptionManagement,
        private readonly Config                                     $eavConfig,
        private readonly ResourceConnection                         $resourceConnection
    )
    {
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->updateProductCustomAttribute();
    }

    /**
     * updateProductCustomAttribute
     */
    public function updateProductCustomAttribute()
    {
        $allStores = $this->storeManager->getStores();
        $simplified_size_arr = ['Vertical', 'Horizontal', 'Square'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'simplified_size', $simplified_size_arr);

        $orientation_arr = ['Small', 'Medium', 'Large', 'Oversized'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'orientation', $orientation_arr);

        $simplified_medium_arr = ['Paper', 'Canvas', 'Acrylic', 'Other'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'simplified_medium', $simplified_medium_arr);

        $frame_material_arr = ['Wood', 'Metal', 'Poly', 'Other'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'frame_material', $frame_material_arr);

        $art_configuration_type_arr = ['no_mat', 'one_mat_beveled', 'one_mat_beveled_top_floated', 'one_mat_deckled',
            'one_mat_deckled_floated', 'two_mats_floated_deckled', 'two_mats_top_beveled_bottom_beveled',
            'two_mats_top_beveled_bottom_floated', 'two_mats_top_floated_bottom_beveled', 'Two_mats_top_floated_deckled'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'art_configuration_type', $art_configuration_type_arr);

        $filter_size_arr = ['Standard', 'Oversized'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'filter_size', $filter_size_arr);

        $configuration_level_arr = ['1', '2', '3', '4'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'configuration_level', $configuration_level_arr);

        $frame_family_arr = ['Coastal', 'Floater Frames', 'Gallery - Black', 'Gallery - Blonde Maple',
            'Gallery - Espresso', 'Gallery - Gray', 'Gallery - Natural', 'Gallery - White', 'Legacy',
            'Linen Liner', 'Metallic - Bright Gold', 'Metallic - Bright Silver', 'Metallic - Bronze',
            'Metallic - Gold', 'Metallic - Stainless Steel', 'Metallic - Warm Silver', 'Old World',
            'Oversized Classics', 'Rustic'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'frame_family', $frame_family_arr);

        $filter_type_arr = ['Standard', 'Fabric'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'filter_type', $filter_type_arr);

        $filter_thickness_arr = ['Single', 'Double', 'Triple'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'filter_thickness', $filter_thickness_arr);

        $color_frame_arr = ['Antique Gold', 'Bright Gold', 'Silver Family', 'Antique Silver',
            'Bright Silver', 'Espresso', 'Oak', 'Gloss Black', 'Matte Black', 'Gloss White',
            'Matte White', 'Cream', 'Tan'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color_frame', $color_frame_arr);

        $color_mat_arr = ['Bright White', 'Off-White', 'Antique', 'Cream', 'Red', 'Blue', 'Yellow', 'Light Linen', 'Dark Linen'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color_mat', $color_mat_arr);

        $color_family_frame_arr = ['Gold Family', 'Wood Tone Family', 'Black Family', 'White Family', 'Natural Family'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color_family_frame', $color_family_frame_arr);

        $color_family_mat_arr = ['White Family', 'Natural Family', 'Primary Family', 'Linen Family'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color_family_mat', $color_family_mat_arr);

        $color_arr = ['Black', 'Blue', 'Brown', 'Copper', 'Gold', 'Gray', 'Green', 'Neutral', 'Orange', 'Pink',
            'Purple', 'Red', 'Silver', 'White', 'Yellow'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color', $color_arr);

        $frame_width_range_arr = ['1"', '1.5"', '2"'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'frame_width_range', $frame_width_range_arr);

        $mat_type_arr = ['Oversized', 'Oversized', 'Standard'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'mat_type', $mat_type_arr);
    }

    function addNewAndRemoveDuplicateAttributeOptions($allStores, $attribute_code, $optionArray)
    {
        $option = [];
        $attributeObj = $this->eavAttribute->create()->load($attribute_code, 'attribute_code');
        $newOptions = $optionArray;
        $option['attribute_id'] = $attributeObj->getAttributeId();
        foreach ($newOptions as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $existingOptions = $attributeObj->getSource()->getAllOptions();
        if (!empty($existingOptions)) {
            $resetExistingOptions = [];
            foreach ($existingOptions as $value) {
                $resetExistingOptions [$value['value']] = ["value" => $value['value'], "label" => $value['label']];
            }
            $retainUniqueExistingOptions = [];
            $deleteDuplicateExistingOption = [];
            sort($resetExistingOptions);
            foreach ($resetExistingOptions as $option) {
                if (array_key_exists($option['label'], $retainUniqueExistingOptions)) {
                    $deleteDuplicateExistingOption[$option['value']] = $option['label'];
                } else {
                    $retainUniqueExistingOptions[$option['label']] = $option['value'];
                }
            }
        }
        if (!empty($deleteDuplicateExistingOption)) {
            $connection = $this->resourceConnection->getConnection();
            ksort($deleteDuplicateExistingOption);
            foreach ($deleteDuplicateExistingOption as $key => $value) {
                $connection->query('Delete  FROM `eav_attribute_option` WHERE attribute_id = ' . $attributeObj->getAttributeId() . ' AND option_id =' . $key);
                $connection->query('Delete  FROM `eav_attribute_option_value` WHERE option_id =' . $key);
            }

        }
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
