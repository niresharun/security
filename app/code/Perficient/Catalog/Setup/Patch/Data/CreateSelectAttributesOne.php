<?php
/**
 * Add New field in catalog product
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<sachin.badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class CreateSelectAttributes
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class CreateSelectAttributesOne implements DataPatchInterface
{
    /**
     * CreateSelectAttributes constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $catCollection
     * @param AttributeFactory $eavAttribute
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CollectionFactory        $catCollection,
        private readonly AttributeFactory         $eavAttribute,
        private readonly StoreManagerInterface    $storeManager,
        private readonly ResourceConnection       $resourceConnection
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
        $this->CreateSelectAttribute();
    }

    /**
     * Create custom product attribute
     */
    private function CreateSelectAttribute(): void
    {

        $allStores = $this->storeManager->getStores();
        $catArray = ['White Linen', 'Natural', 'Black', 'Natural Linen', 'White', 'Off-White', 'Natural Linen', 'Off-White', 'Natural Linen', 'White Linen', 'Silver', 'Silver', 'Silver', 'Black with Gold Leaf', 'Gold', 'Cream with Gold Leaf', 'Espresso', 'Black', 'Black', 'White', 'White', 'Espresso', 'Espresso', 'Black', 'Black', 'Silver Leaf', 'Stainless Silver', 'Black', 'Black', 'Espresso', 'Distressed Espresso', 'Distressed Silver Leaf', 'Silver', 'Antique Silver', 'Black', 'Etched Silver Leaf', 'Espresso', 'Espresso', 'Rubbed Espresso with Gold', 'Espresso', 'Silver', 'Espresso', 'Distressed Off-White and Antique Gold', 'Distressed Black with Antique Gold', 'Distressed Antique Silver with Inner Bead', 'Bamboo', 'Gold', 'Black', 'Espresso', 'Antique Gold', 'Espresso', 'Gold Leaf', 'Espresso', 'Silver', 'Espresso', 'Black', 'White Linen', 'Silver', 'Silver Leaf', 'Gold', 'Silver Leaf', 'Black', 'White', 'Textured Ivory', 'Black', 'Silver', 'Black', 'Black', 'Matte Black', 'Silver', 'Silver', 'Distressed Antique Gold and Black', 'Espresso', 'Distressed Antique Gold and Black', 'Gold', 'Black', 'Distressed Black with Antique Gold', 'Black', 'Espresso', 'Espresso', 'Silver Etched', 'Silver Etched', 'Matte White', 'Espresso', 'Silver Leaf', 'Distressed Black and Gold', 'Distressed Black and Silver', 'Distressed Black and Antique Gold', 'Distressed Antique Gold', 'Silver', 'Silver Leaf', 'Scaled Texture Gold', 'Black', 'White', 'Distressed Chocolate', 'Distressed Gray', 'Black', 'Distressed Black', 'Black', 'Espresso', 'White', 'Silver', 'White', 'Gray', 'Espresso', 'Gray', 'Silver Etched', 'Distressed Ivory', 'Silver', 'Black', 'Antique Silver Etched', 'Silver Leaf Etched', 'Black', 'Silver', 'Gold', 'White', 'Distressed Ivory', 'Silver', 'Antique Black with Brushed Silver Accents', 'Silver', 'Silver', 'Silver Etched', 'Silver', 'Silver', 'Silver', 'Black', 'Silver', 'Black', 'Gray', 'Distressed Gray', 'White', 'Gray', 'White', 'Silver', 'Warm Silver', 'Brown', 'Silver', 'Etched Silver', 'Silver', 'Mahogany', 'Matte Black', 'Matte Black', 'Silver', 'Flat White Distressed', 'Silver', 'White Gold', 'White Gold', 'Gold', 'Black', 'Distressed Black', 'Silver', 'Espresso', 'Silver', 'Silver', 'Espresso', 'Antique Silver', 'Silver', 'Silver', 'Antique Gold', 'Silver', 'Black', 'Distressed Antique Gold', 'Etched Gold', 'Etched Gold', 'Gold', 'Etched and Gold', 'Antique Gold and Black', 'Gold Leaf', 'Gold', 'Gold', 'Rustic Tan', 'Brown Bark', 'Antique Gold', 'Silver', 'White', 'Distressed Mahogany', 'Brown', 'Brown', 'Champagne and Espresso ', 'White', 'Matte White', 'White', 'Dark Gray', 'Silver', 'Silver Metallic Leaf', 'Black', 'Distressed Black', 'Walnut', 'White', 'White', 'Off-White Distressed', 'Champagne Distressed', 'Silver', 'White', 'White', 'White', 'Brown', 'Gray', 'Red', 'Black', 'Pewter', 'Brown', 'Walnut Distressed', 'Silver', 'Brown', 'Black', 'Brown', 'Gray', 'Gray', 'Espresso', 'Satin Dark Silver', 'Satin Gold', 'Satin Charcoal', 'Silver', 'Espresso', 'Gray', 'Walnut', 'Gray', 'Natural', 'Natural', 'Natural', 'Gold Banboo', 'Weathered White', 'Blue', 'Gold', 'Gold', 'Champagne', 'Gray', 'Black Woodgrain', 'Walnut', 'Espresso', 'Silver', 'Antique Silver', 'Matte White', 'Matte Black', 'Bright Silver', 'Bright Silver', 'Silver', 'Gold', 'Gold', 'Antique Gold', 'Gold', 'Antique Gold', 'Mahogany', 'Aluminum', 'Silver', 'Brown', 'Black', 'Natural ', 'Silver & Espresso', 'Espresso', 'Matte Black', 'Matte Black', 'Brown', 'Silver', 'Black', 'Silver Leaf', 'Black', 'Gold', 'Antique Silver', 'Gold', 'Black', 'Silver', 'Silver Leaf', 'Silver', 'White', 'Stainless Steel', 'Silver', 'Brown', 'Antique Gold', 'Gold', 'Antique Gold', 'Antique Gold', 'Antique Gold', 'Antique Gold', 'Brown', 'Matte White', 'Black', 'Gold', 'Dark Gray', 'Gold', 'Espresso', 'Antique Gold', 'Gold', 'Antique Gold', 'Antique Gold', 'Antique Gold', 'Gray', 'Black', 'Matte Black', 'White', 'White', 'Black', 'Gray', 'Walnut', 'Dark Gray', 'White', 'Brown', 'Gold', 'White', 'Gold', 'Brown', 'Gold', 'Gold', 'Bright Gold', 'Gold', 'Gold', 'Espresso', 'Bronze', 'Natural', 'Espresso', 'Gold', 'Hammered Gold', 'Walnut', 'Black', 'High Gloss Black', 'Walnut', 'Espresso and Antique Silver', 'Matte Black', 'Champagne', 'Espresso', 'Champagne', 'Silver', 'Silver & Espresso', 'White', 'Silver Leaf', 'Antique Gold', 'Distressed Black', 'Distressed Black', 'Bronze', 'Light Walnut', 'Dark Espresso', 'Dark Walnut', 'Champagne', 'Black', 'Natural Linen', 'Matte White', 'Taupe', 'Taupe', 'Pickled White', 'Black', 'Espresso', 'Matte White', 'Matte White', 'Matte Black', 'Taupe', 'Espresso', 'Blonde Maple', 'Espresso', 'Warm Silver', 'Pickled White', 'Matte Black', 'Warm Silver', 'Bright Gold', 'Bright Silver', 'Matte Black', 'Matte White', 'Espresso', 'Taupe', 'Warm Silver', 'Blonde Maple', 'Pickled White', 'Matte Black', 'Matte White', 'Espresso', 'Taupe', 'Warm Silver', 'Blonde Maple', 'Pickled White', 'Espresso', 'Warm Silver', 'Blonde Maple', 'Pickled White', 'Bright Gold', 'Bright Silver', 'Matte Black', 'Matte White', 'Blonde Maple', 'Matte Black', 'Matte White', 'Espresso', 'Taupe', 'Warm Silver', 'Blonde Maple', 'Pickled White', 'Bright Gold', 'Bright Silver', 'Matte Black', 'Espresso', 'Taupe', 'Warm Silver', 'Blonde Maple', 'Pickled White', 'Bright Gold', 'Bright Silver', 'Gray', 'Walnut', 'Champagne', 'Gold', 'Pewter', 'Gray Driftwood', 'Antique Silver', 'Antique Gold', 'Antique Gold', 'Antique Gold', 'Antique Silver', 'Black', 'Brown', 'Charcoal Woodgrain', 'Silver', 'Dark Brown Barnwood', 'Dark Walnut Woodgrain', 'Dark Brown Barnwood', 'Bronze', 'Walnut Woodgrain', 'Gray Woodgrain', 'Black Woodgrain', 'Walnut Woodgrain', 'Black Woodgrain', 'Natural Woodgrain', 'Dark Walnut Woodgrain', 'Silver', 'Distressed Black', 'Gray Brown Barnwood', 'Gray Brown Barnwood', 'Distressed White', 'Natural Woodgrain', 'Charcoal Woodgrain', 'Silver', 'Light Gray Barnwood', 'Distressed Gray', 'Grey Woodgrain', 'Light Gray Barnwood', 'Bright Gold', 'Bright Silver'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color_frame', $catArray);
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

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
