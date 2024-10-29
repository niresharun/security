<?php
/**
 * Add New field in catalog product For DCKAP
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Dominic Henry <dominic.henry@perficient.com>
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
class AddEdgeTreatmentCustomProductOptionForFrameType implements DataPatchInterface
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
        $this->addEdgeTreatmentCustomProductOptionForFrameType();
    }

    /**
     * addEdgeTreatmentCustomProductOptionForFrameType
     */
    public function addEdgeTreatmentCustomProductOptionForFrameType()
    {
        $allStores = $this->storeManager->getStores();
        $option = [];
        $attributeId = $this->eavAttribute->create()->load('frame_type', 'attribute_code');
        $attribute_arr = ['Edge Treatment'];
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
