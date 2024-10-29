<?php
/**
 * Add New field in catalog product Attribute Option Update
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddEdgeTreatmentCustomProductOptionForFrameType
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateProductAttributesOptionsV2 implements DataPatchInterface
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
        $this->AddOptionToFrameWidthRange();
    }

    /**
     * addEdgeTreatmentCustomProductOptionForFrameType
     */
    public function AddOptionToFrameWidthRange()
    {
        $allStores = $this->storeManager->getStores();
        $option = [];

        try {
            $attributeId = $this->eavAttribute->create()->load('frame_width_range', 'attribute_code');
            $attribute_arr = ['0.1" to 0.5"'];
            $option['attribute_id'] = $attributeId->getAttributeId();
            foreach ($attribute_arr as $key => $value) {
                $option['value'][$value][0] = $value;
                foreach ($allStores as $store) {
                    $option['value'][$value][$store->getId()] = $value;
                }
            }
            $eavSetup = $this->eavSetupFactory->create();
            $eavSetup->addAttributeOption($option);

        } catch (\Magento\Framework\Exception\NoSuchEntityException) {
            return true;
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
