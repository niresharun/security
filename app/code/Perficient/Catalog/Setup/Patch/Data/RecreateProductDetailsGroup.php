<?php
/**
 * Recreate Product details group for Frames and Mats
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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

/**
 * Class RecreateProductDetailsGroup
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class RecreateProductDetailsGroup implements DataPatchInterface
{
    /**
     * frame attribute set.
     */
    const FRAME_ATTRIBUTE_SET = 'Frame';

    /**
     * frame attribute set.
     */
    const MAT_ATTRIBUTE_SET = 'Mat';

    /**
     * Product Details
     */
    const PRODUCT_DETAILS_GROUP = 'Product Details';

    /**
     * RecreateProductDetailsGroup constructor.
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
        $this->createGroupForFrame($eavSetup, $entityTypeId);
        $this->createGroupForMat($eavSetup, $entityTypeId);
    }

    /**
     * @param EavSetup $eavSetup
     * @param $entityTypeId
     * @throws LocalizedException
     */
    private function createGroupForFrame($eavSetup, $entityTypeId)
    {
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, self::FRAME_ATTRIBUTE_SET);
        $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, self::PRODUCT_DETAILS_GROUP, 1);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::PRODUCT_DETAILS_GROUP);
        $groupName = $eavSetup->getAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name');
        $this->addAttributeToProductDetailsGroup($eavSetup, $entityTypeId, $attributeSetId, $attributeGroupId, $groupName);
    }

    /**
     * @param EavSetup $eavSetup
     * @param $entityTypeId
     * @throws LocalizedException
     */
    private function createGroupForMat($eavSetup, $entityTypeId)
    {
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, self::MAT_ATTRIBUTE_SET);
        $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, self::PRODUCT_DETAILS_GROUP, 1);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::PRODUCT_DETAILS_GROUP);
        $groupName = $eavSetup->getAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name');
        $this->addAttributeToProductDetailsGroup($eavSetup, $entityTypeId, $attributeSetId, $attributeGroupId, $groupName);
    }

    /**
     * @param EavSetup $eavSetup
     * @param $entityTypeId
     * @param $attributeSetId
     * @param $attributeGroupId
     * @param $groupName
     */
    private function addAttributeToProductDetailsGroup($eavSetup, $entityTypeId, $attributeSetId, $attributeGroupId, $groupName)
    {
        if ($attributeGroupId && $attributeSetId && $groupName == self::PRODUCT_DETAILS_GROUP) {
            foreach ($this->getAttributesToAdd() as $attributeCode => $sort) {
                $attributeId = $eavSetup->getAttributeId($entityTypeId, $attributeCode);
                if ($attributeId) {
                    $eavSetup->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attributeCode, $sort);
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getAttributesToAdd()
    {
        return [
            "status" => 1,
            "name" => 2,
            "sku" => 3,
            "sku_type" => 4,
            "price" => 5,
            "giftcard_type" => 6,
            "old_id" => 6,
            "price_type" => 7,
            "giftcard_amounts" => 8,
            "allow_open_amount" => 9,
            "open_amount_min" => 10,
            "open_amount_max" => 11,
            "url_path" => 11,
            "tax_class_id" => 12,
            "quantity_and_stock_status" => 13,
            "required_options" => 14,
            "weight" => 14,
            "has_options" => 15,
            "weight_type" => 15,
            "visibility" => 16,
            "image_label" => 16,
            "small_image_label" => 17,
            "category_ids" => 17,
            "thumbnail_label" => 18,
            "news_from_date" => 18,
            "news_to_date" => 19,
            "created_at" => 19,
            "updated_at" => 20,
            "country_of_manufacture" => 20,
            "is_returnable" => 21,
            "syspro_number" => 22,
            "uuid" => 23,
            "enable_googlecheckout" => 24,
            "price_level" => 25,
            "product_customizer" => 26,
            "is_quick_ship" => 27,
            "default_configurations" => 28,
            //"simplified_medium"=>29,
            //"simplified_size"=>30,
            //"lifestyle"=>31,
            //"licensed_collection"=>32,
            //"orientation"=>33,
            "links_purchased_separately" => 111,
            "samples_title" => 112,
            "links_title" => 113,
            "links_exist" => 114,
            "related_tgtr_position_limit" => 115,
            "related_tgtr_position_behavior" => 116,
            "upsell_tgtr_position_limit" => 117,
            "upsell_tgtr_position_behavior" => 118
        ];
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
