<?php
declare(strict_types=1);

namespace Wendover\Catalog\Model\Entity\Attribute\Source;

use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class FrameDefaultSKUSource extends AbstractSource
{
    protected $_options = null;

    public function __construct(
        private readonly CatalogConfig     $catalogConfig,
        private readonly ProductCollection $productCollection
    )
    {
    }

    public function getAllOptions()
    {
        if ($this->_options !== null) {
            return $this->_options;
        }

        $frameTypeSource = $this->catalogConfig->getAttribute(Product::ENTITY, 'frame_type')->getSource();
        $optionId1 = $frameTypeSource->getOptionId('Floater');
        $optionId2 = $frameTypeSource->getOptionId('Standard');

        $attributeSetId = $this->catalogConfig->getAttributeSetId(Product::ENTITY, 'Frame');

        $this->productCollection->addFieldToFilter('attribute_set_id', $attributeSetId)
            ->addFieldToFilter('frame_type', ['in' => [$optionId1, $optionId2]]);
        $this->_options = [
            ['label' => 'Select', 'value' => '']
        ];
        foreach ($this->productCollection->getItems() as $prod) {
            $this->_options[] = ['label' => $prod->getData('sku'), 'value' =>  $prod->getData('sku')];
        }
        return $this->_options;
    }
}
