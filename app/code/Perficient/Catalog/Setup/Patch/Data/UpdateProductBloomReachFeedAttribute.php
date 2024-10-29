<?php
/**
 * Update existing product attributes.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpdateProductBloomReachFeedAttribute
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateProductBloomReachFeedAttribute implements DataPatchInterface
{
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
        $this->updateProductBloomReachFeedAttribute();
    }

    /**
     * Create custom product attribute
     */
    private function updateProductBloomReachFeedAttribute(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * List of attributes to be change.
         */
        $attributes = [
            'color',
            //'licensed_collection',
            //'lifestyle',
            //'orientation',
            //'simplified_medium',
            //'simplified_size',
        ];

        /**
         * List of properties to be change.
         */
        $properties = [
            'backend_model' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
            'backend_type' => 'varchar',
            'frontend_input' => 'multiselect',
            'source_model' => null
        ];

        try {
            /**
             * Loop on all the attributes to change all the mentioned properties.
             */
            foreach ($attributes as $attribute) {
                $attributeId = $this->eavAttribute->getIdByCode(Product::ENTITY, $attribute);
                foreach ($properties as $field => $value) {
                    $eavSetup->updateAttribute(Product::ENTITY, $attributeId, $field, $value);
                }
            }

            /**
             * Change the label of Color attribute.
             */
            $attributeId = $this->eavAttribute->getIdByCode(Product::ENTITY, 'color');
            $eavSetup->updateAttribute(Product::ENTITY, $attributeId, 'frontend_label', 'Color List');
        } catch (\Exception $e) {
            throw $e;
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
