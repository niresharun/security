<?php

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class ProductFilterAttribute implements DataPatchInterface
{
    private $moduleDataSetup;
    private $eavSetupFactory;
    private $attributeFactory;

    public function __construct(
        ModuleDataSetupInterface  $moduleDataSetup,
        EavSetupFactory           $eavSetupFactory,
        Attribute                 $attributeFactory
    ) {
        $this->moduleDataSetup  = $moduleDataSetup;
        $this->eavSetupFactory  = $eavSetupFactory;
        $this->attributeFactory = $attributeFactory;
    }
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $productAttributes = [
            "color"             => ['Natural', 'Off-White', 'Woodtone'],
            "orientation"       => ['Bidirectional'],
            "simplified_medium" => ['Mirror'],
            "simplified_size"   => ['Small (upto 20")','Medium (21" - 40")','Large (41" - 50")', 'Oversized (50" +)']
        ];

        foreach ($productAttributes as $key => $list) {
            $attributeInfo = $this->attributeFactory->getCollection()
                ->addFieldToFilter('attribute_code', ['eq' => $key])
                ->getFirstItem();
            $option = array();
            $option['attribute_id'] = $attributeInfo->getAttributeId();
            foreach ($list as $value) {
                $option['value'][$value][0] = $value;
                $option['value'][$value][1] = $value;
            }
            $eavSetup->addAttributeOption($option);
        }
    }
    public static function getDependencies()
    {
        return [];
    }
    public function getAliases()
    {
        return [];
    }
}
