<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateMirrorOptionAttribute implements DataPatchInterface
{
    public function __construct(
        private readonly Attribute $attributeFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [
            CreateMirrorOptionAttribute::class
        ];
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

        $attributesInfo = [
            'frame_default_sku_configurable' => [
                'frontend_label' => 'Frame'
            ],
            'size_string' => [
                'source_model' =>  Table::class
            ],
            'glass_type' => [
                'source_model' =>  Table::class,
                'frontend_label' => 'Glass'
            ]
        ];

        foreach ($attributesInfo as $code => $attribute) {
            $attributeInfo = $this->attributeFactory->getCollection()
                ->addFieldToFilter('attribute_code', ['eq' => $code])
                ->getFirstItem();
            foreach ($attribute as $column => $value) {
                $attributeInfo->setData($column, $value);
                $attributeInfo->save();
            }
        }
    }
}
