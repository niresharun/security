<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Wendover\Catalog\Model\Entity\Attribute\Source\FrameDefaultSKUSource;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\Entity;

/**
 * To create configurable option attribute for `frame_default_sku` product attribute
 *
 */
class CreateMirrorOptionAttribute implements DataPatchInterface
{
    const DEFAULT_FRAME_SKU_CONFIGURABLE = 'frame_default_sku_configurable';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly AttributeFactory         $eavAttributeFactory,
        private readonly Entity                   $entity,
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            self::DEFAULT_FRAME_SKU_CONFIGURABLE,
            [
                'group' => 'Mirror',
                'type' => 'varchar',
                'label' => 'Frame default sku',
                'backend' => '',
                'input' => 'select',
                'wysiwyg_enabled' => false,
                'source' => FrameDefaultSKUSource::class,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
                'sort_order' => 5,
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'attribute_set' => 'Mirror',
                'apply_to' => null,
                'option' => [
                    'values' => [],
                ]
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'size_string',
            [
                'group' => 'Mirror',
                'type' => 'varchar',
                'label' => 'Size',
                'backend' => '',
                'input' => 'select',
                'wysiwyg_enabled' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
                'sort_order' => 6,
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'attribute_set' => 'Mirror',
                'apply_to' => null,
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            'glass_type',
            [
                'group' => 'Mirror',
                'type' => 'varchar',
                'label' => 'Glass Type',
                'backend' => '',
                'input' => 'select',
                'wysiwyg_enabled' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => false,
                'sort_order' => 7,
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'attribute_set' => 'Mirror',
                'apply_to' => null,
                'option' => [
                    'values' => ['Beveled', 'Non-Beveled'],
                ]
            ]
        );
    }
}
