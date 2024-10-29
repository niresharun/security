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
class CreateSelectAttributes implements DataPatchInterface
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
        $catArray = [];
        $categoryCollection = $this->catCollection;
        $categories = $categoryCollection->create();
        $categories->addAttributeToSelect('*');
        foreach ($categories as $category) {


            $catArray[] = "'" . preg_replace("/[^A-Za-z0-9 ]/", '', $category->getName()) . "'";
        }

        //  print_r($catArray);exit;
        $allStores = $this->storeManager->getStores();
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'category_list', $catArray);
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'licensed_collection', $catArray);
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'lifestyle', $catArray);

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'frame_family',
            [
                'group' => 'Frame Additional Information',
                'attribute_set' => 'Frame',
                'type' => 'int',
                'input' => 'select',
                'label' => 'Frame Family',
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
                            ['Coastal', 'Floater Frames', 'Gallery - Black', 'Gallery - Blonde Maple', 'Gallery - Espresso',
                                'Gallery - Gray', 'Gallery - Natural', 'Gallery - White', 'Legacy', 'Linen Liner',
                                'Metallic - Bright Gold', 'Metallic - Bright Silver', 'Metallic - Bronze', 'Metallic - Gold',
                                'Metallic - Stainless Steel', 'Metallic - Warm Silver', 'Old World', 'Oversized Classics', 'Rustic'],
                    ],
            ]
        );
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
        $connection = $this->resourceConnection->getConnection();
        $query = 'UPDATE `eav_attribute_option_value` SET VALUE = REPLACE(VALUE,"\'",\' \')';
        $connection->query($query);
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
