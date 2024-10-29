<?php
declare(strict_types=1);

namespace Perficient\Search\Plugin\Block\Search;

use Amasty\Xsearch\Block\Search\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Perficient\Catalog\Helper\Data as CatalogHelper;

class ProductPlugin
{
    public function __construct(
      private readonly CatalogHelper $catalogHelper
    ) {
    }

    /**
     * @param Product $subject
     * @param array[] $result
     * @return array $result
     */
    public function afterGetResults(Product $subject, array $result)
    {
        foreach ($result as $productId => $productData) {
            if ($this->catalogHelper->getParentId($productId)?->getTypeId() === Configurable::TYPE_CODE) {
                $result[$productId]['url'] = $this->catalogHelper->getMirrorProductUrl($productId);
            }
        }
        return $result;
    }
}
