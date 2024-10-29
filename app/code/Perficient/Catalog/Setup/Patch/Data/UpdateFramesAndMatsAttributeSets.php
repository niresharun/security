<?php
/**
 * Update attribute groups for frames and mats attribute sets
 *
 * @category : PHP
 * @package  : Perficient_Catalog
 * @copyright: Copyright © 2020 Magento. All rights reserved.
 * @license  : Perficient, Inc.
 * @author   : Tahir Aziz <tahir.aziz@perficient.com>
 * @keywords : Perficient frames, mates, Category
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

/**
 * Class UpdateFramesAndMatsAttributeSets
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateFramesAndMatsAttributeSets implements DataPatchInterface
{
    /**
     * frame attribute set.
     */
    const FRAME_ATTRIBUTE_SET = 'Frame';

    /**
     * frame attribute set.
     */
    const FRAME_ATTRIBUTE_GROUP = 'Frame Additional Information';

    /**
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_SET = 'Mat';

    /**
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_GROUP = 'Mat Additional Information';

    /**
     * CreateProductCustomAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Attribute $eavAttribute
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Attribute                $eavAttribute
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $this->removeMatsGroupFromFrames($eavSetup, $entityTypeId);
        $this->removeFramesGroupFromMats($eavSetup, $entityTypeId);
    }

    /**
     * @param EavSetup $eavSetup
     * @param $entityTypeId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function removeMatsGroupFromFrames($eavSetup, $entityTypeId)
    {
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, self::FRAME_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::MAT_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name');
        if ($attributeGroupId && $attributeSetId && $groupName == self::MAT_ATTRIBUTE_GROUP) {
            $eavSetup->removeAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId);
        }
    }

    /**
     * @param EavSetup $eavSetup
     * @param $entityTypeId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function removeFramesGroupFromMats($eavSetup, $entityTypeId)
    {
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, self::MAT_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::FRAME_ATTRIBUTE_GROUP);
        $groupName = $eavSetup->getAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name');
        if ($attributeGroupId && $attributeSetId && $groupName == self::FRAME_ATTRIBUTE_GROUP) {
            $eavSetup->removeAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId);
        }
    }


    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
