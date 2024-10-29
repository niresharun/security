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

use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateProductCustomAttributes
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateProductCustomAttributes implements DataPatchInterface
{
    public function __construct(
        private readonly \Magento\Eav\Setup\EavSetupFactory         $eavSetupFactory,
        private readonly \Magento\Store\Model\StoreManagerInterface $storeManager,
        private readonly \Magento\Eav\Model\Entity\AttributeFactory $eavAttribute
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
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('frame_type', 'attribute_code');
        $attribute_arr = ['Liner'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('filter_size', 'attribute_code');
        $attribute_arr = ['Oversized', 'Standard'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('color', 'attribute_code');
        $attribute_arr = ['Green', 'Blue', 'Gold'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('category_list', 'attribute_code');
        $attribute_arr = ['Abstract', 'Contemporary', 'Asian'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('lifestyle', 'attribute_code');
        $attribute_arr = ['Montauk', 'Traditional'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('configuration_level', 'attribute_code');
        $attribute_arr = ['1', '2', '3', '4', '5'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);

        $option = [];
        $attributeId = $this->eavAttribute->create()->load('licensed_collection', 'attribute_code');
        $attribute_arr = ['Lillian August'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);

        $option = [];
        $attributeId = $this->eavAttribute->create()->load('orientation', 'attribute_code');
        $attribute_arr = ['Horizontal'];
        $option['attribute_id'] = $attributeId->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
    }


    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
