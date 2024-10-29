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
 * Class UpdateProductCustomAttributesOptionsOne
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateProductCustomAttributesOptionsOne implements DataPatchInterface
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

        $licensed_collection_arr = ['Blank'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'licensed_collection', $licensed_collection_arr);

        $color_mat_arr = ['Black', 'Cream', 'Weatherwood', 'French Vanilla', 'Forest Shadow', 'Silver Mist', 'Midnight Blue', 'Mojave', 'Seashell', 'Sea Foam',
            'Granite', 'Smooth Black', 'Smooth White', 'Toasted Almond', 'Sea Green', 'Antique White',
            'Cappuccino', 'Pale Laurel', 'Bottle Blue', 'Hay', 'Oyster Bay', 'Dill', 'Pistachio', 'Pure White',
            'Cream Linen', 'Spice', 'Umber', 'Dark Olive', 'Crisp', 'Splash', 'Canvas (White)', 'Cloud',
            'White Silk', 'Ecru', 'Seaside', 'Off White Silk', 'Pear', 'Palm', 'Honeydew', 'Fountain Blue', 'Gray',
            'Brittany Blue', 'Deep Blue', 'Merlot', 'Burnt Sienna', 'Tawny', 'Clay', 'Felt', 'Cobblestone', 'Cinder',
            'Mocha', 'Belgique - Bruxelles', 'Belgique - Antwerpen', 'Marzipan', 'Loam', 'Dover White', 'Polar White',
            'Papyrus', 'Classic Gold', 'Aged Oak', 'Array Agate', 'Array Flint', 'Array Sandy', 'Array Topaz', 'Becca Taupe',
            'Belfast Buff', 'Belfast Earth', 'Burlap Gold', 'Burlap Khaki', 'Burlap Natural', 'Canvas', 'Frost NIK89', 'Muslin',
            'Primed Linen', 'Raw Linen', 'Shoji', 'Turbine Smoke', 'White'];
        $this->addNewAndRemoveDuplicateAttributeOptions($allStores, 'color_mat', $color_mat_arr);

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
