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

namespace Perficient\Zsetup\Setup\Patch\Data;

use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateProductAttributesOptionsV3
 * @package Perficient\Zsetup\Setup\Patch\Data
 */
class UpdateProductAttributesOptionsV3 implements DataPatchInterface
{
    /**
     * UpdateProductAttributesOptionsV3 constructor.
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param Config $eavConfig
     * @param ResourceConnection $resourceConnection
     * @param AttributeManagementInterface $attributeManagement
     * @param ModuleDataSetupInterface $cSetUp
     */
    public function __construct(
        private readonly \Magento\Eav\Setup\EavSetup $eavSetupA,
        private readonly \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        private readonly \Magento\Store\Model\StoreManagerInterface $storeManager,
        private readonly \Magento\Eav\Model\Entity\AttributeFactory $eavAttribute,
        private readonly AttributeOptionManagementInterface $attributeOptionManagement,
        private readonly Config $eavConfig,
        private readonly ResourceConnection $resourceConnection,
        private readonly AttributeManagementInterface $attributeManagement,
        private readonly CatalogConfig $catalogConfig,
        private readonly ModuleDataSetupInterface $cSetUp
    ) {
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
        /* remove attribute from attribute set*/
        $this->removeAttributeFromAttributeSet();
        /*add new attribute */
        $this->addNewAttribute();
        /*add new attribute to attribute set*/
        $this->addAttributeFromAttributeSet();
        /*remove old options*/
        $this->removeOldOptions();
        /*add new options*/
        $this->updateProductCustomAttribute();
    }

    function removeAttributeFromAttributeSet()
    {
        $attributeSetIdToRemove = $this->eavSetupA->getAttributeSetId(
            \Magento\Catalog\Model\Product::ENTITY,
            'Art' // Attribute set name
        );
        $attributeId = $this->eavSetupA->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'filter_size' // Attribute code
        );
        $this->cSetUp->getConnection()->delete(
            $this->cSetUp->getTable('eav_entity_attribute'),
            [
                'attribute_id = ?' => $attributeId,
                'attribute_set_id = ?' => $attributeSetIdToRemove,
            ]
        );
    }

    function addNewAttribute()
    {
        $this->eavSetupA->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'art_category',
            [
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Category',
                'input' => 'multiselect',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'wysiwyg_enabled' => true,
                'unique' => false,
                'apply_to' => '',
				'is_filterable_in_search' => true,
				'is_html_allowed_on_front' => false,
				'position' => true,
				'is_html_allowed_on_front' => false
            ]
        );
    }

    function addAttributeFromAttributeSet()
    {
        $connection = $this->resourceConnection->getConnection();
        $attributeSetId = $this->eavSetupA->getAttributeSetId(
            \Magento\Catalog\Model\Product::ENTITY,
            'Art' // Attribute set name
        );
        $group_id = $connection->fetchCol('select attribute_group_id from `eav_attribute_group` WHERE attribute_set_id =' . $attributeSetId . ' and attribute_group_name = "Árt"');
        $this->attributeManagement->assign(
            'catalog_product',
            $attributeSetId,
            $group_id[0],
            'art_category',
            999
        );
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeOldOptions()
    {
        /*filter_size*/
        $eavConfig = $this->eavConfig;
        $attribute = $eavConfig->getAttribute('catalog_product', 'filter_size');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup = $this->eavSetupA;
        $eavSetup->addAttributeOption($options);
        /*licensed_collection*/
        $attribute = $eavConfig->getAttribute('catalog_product', 'licensed_collection');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup->addAttributeOption($options);
        /*lifestyle*/
        $attribute = $eavConfig->getAttribute('catalog_product', 'lifestyle');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup->addAttributeOption($options);

        /*simplified_medium*/
        $attribute = $eavConfig->getAttribute('catalog_product', 'simplified_medium');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup->addAttributeOption($options);

        /*orientation*/
        $attribute = $eavConfig->getAttribute('catalog_product', 'orientation');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup->addAttributeOption($options);

        /*simplified_size*/
        $attribute = $eavConfig->getAttribute('catalog_product', 'simplified_size');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup->addAttributeOption($options);
		
		 /*simplified_size*/
        $attribute = $eavConfig->getAttribute('catalog_product', 'frame_width_range');
        $id = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            $options['delete'][$option['value']] = true;
            $options['value'][$option['value']] = true;
        }
        $eavSetup->addAttributeOption($options);
    }

    /**
     * updateProductCustomAttribute
     */
    public function updateProductCustomAttribute()
    {
        $allStores = $this->storeManager->getStores();
        $simplified_size_arr = ['Small', 'Medium', 'Large', 'Oversized'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'simplified_size', $simplified_size_arr);
        $art_category_arr = ["Abstract", "Animals", "Architecture", "Coastal", "Decorative", "Europe", "Figurative",
            "Floral", "For Kids", "Landscape", "Lodge", "Mirrors", "Nature", "Photography", "Rugs", "Shadow Boxes", "Still life", "Typography", "Vintage"];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'art_category', $art_category_arr);
        $licensed_collection_arr = ["Lillian August", "Nathan Turner", "Thom Filicia", "Christoper Kennedy", "Mat Sanders",
            "Danielle Rollins", "Meg Braff", "Chris Coleman", "Max May", "Michelle Nussbaumer"];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'licensed_collection', $licensed_collection_arr);
        $lifestyle_arr = ["American Midwest", "Classic Nautical", "Global", "Hamptons", "Industrial Loft", "Lake Resort",
            "Low Country", "Mid-Century Modern", "Modern Luxe", "Mountain Retreat", "New traditional", "Pacific Coast",
            "SoCal", "Southwest", "Traditional", "Tropical", "Wine Country"];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'lifestyle', $lifestyle_arr);
        $simplified_medium_arr = ['Paper', 'Canvas', 'Acrylic', 'Other'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'simplified_medium', $simplified_medium_arr);
        $orientation_arr = ['Vertical', 'Horizontal', 'Square'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'orientation', $orientation_arr);
		 $frame_width_range_arr = ['0.1" to 0.5"', '0.6" to 1.0"', '1.1" to 1.5"', '1.6" to 2.0"','2.1" to 2.5"','2.6" to 3.0"','3.1" to 3.5"','3.6" to 4.0"','4.1" to 4.5"','4.6" to 5.0"' ];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'frame_width_range', $frame_width_range_arr);
    }

    /**
     * @param $allStores
     * @param $attribute_code
     * @param $optionArray
     */
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