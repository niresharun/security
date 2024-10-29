<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateSimplifiedSizeAttributeOption implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly EavConfig $eavConfig
    )
    {
    }

    public static function getDependencies()
    {
        return [ProductFilterAttribute::class];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'simplified_size');
        $attributeId = $attribute->getAttributeId();
        $options = $attribute->getSource()->getAllOptions(false);

        $newSimplifiedSizeLabel = [
            'Small (upto 20")' => 'Small: Up to 20”',
            'Medium (21" - 40")' => 'Medium: 21” - 40”',
            'Large (41" - 50")' => 'Large: 41” - 50”',
            'Oversized (50" +)' => 'Oversized: Over 50”'
        ];
        $newSimplifiedSizeOrder = [
            'Small',
            'Small (upto 20")',
            'Medium',
            'Medium (21" - 40")',
            'Large',
            'Large (41" - 50")' ,
            'Oversized',
            'Oversized (50" +)'
        ];
        if (!empty($options)) {
            $conn = $this->moduleDataSetup->getConnection();
            $optionValueTable = $this->moduleDataSetup->getTable('eav_attribute_option_value');
            foreach ($newSimplifiedSizeLabel as $oldLabel => $newLabel) {
                $conn->update(
                    $optionValueTable,
                    ['value' => $newLabel],
                    [
                        sprintf('option_id IN (%s)', implode(',', array_column($options, 'value'))),
                        'value = ?' => $oldLabel
                    ]
                );
            }
        }

        $updateOption['attribute_id'] = $attributeId;
        foreach ($options as $option) {
            list('label' => $optionText, 'value' => $optionValueId) = $option;
            if (in_array($optionText, $newSimplifiedSizeOrder)) {
                $sortOrder = array_flip($newSimplifiedSizeOrder)[$optionText]; // update sort_order
                $updateOption['values'][$sortOrder] = $newSimplifiedSizeLabel[$optionText] ?? $optionText; // set new text
            }
        }
        if (!empty($updateOption['values'])) {
            $eavSetup->addAttributeOption($updateOption);
        }
    }
}
