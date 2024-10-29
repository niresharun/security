<?php
/**
 * Add New field in catalog product
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Dominic Henry <dominic.henry@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class CreateFrameWidthRangeAttribute
 * product custom attribute
 */
class CreateShowWithLinersAttribute implements DataPatchInterface
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
     * CreateProductCustomAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->CreateShowWithLinersAttribute();
    }

    /**
     * Create custom product attribute
     */
    private function CreateShowWithLinersAttribute(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId(Product::ENTITY, self::FRAME_ATTRIBUTE_SET);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, self::FRAME_ATTRIBUTE_GROUP);

        // Create "Show With Liners" attribute
        $eavSetup->addAttribute(
            Product::ENTITY,
            'show_with_liners',
            [
                'type' => 'int',
                'input' => 'boolean',
                'label' => 'Show With Liners',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 9911
            ]
        );

        // Assign "Show With Liners" attribute to "Frame Additional Information"
        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSetId,
            $attributeGroupId,
            'show_with_liners',
            18 // sort order
        );
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
