<?php
declare(strict_types=1);

namespace Wendover\Checkout\Plugin;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Perficient\Catalog\Helper\Data as CatalogHelper;

class ItemConverterPlugin
{
    /**
     * @param CatalogHelper $catalogHelper
     */
    public function __construct(public readonly CatalogHelper $catalogHelper)
    {
    }

    /**
     * @param ItemConverter $itemConverter
     * @param TotalsItemInterface $result
     * @param QuoteItem $item
     * @return TotalsItemInterface
     */
    public function afterModelToDataObject(
        ItemConverter $itemConverter,
        TotalsItemInterface $result,
        QuoteItem $item
    ): TotalsItemInterface {
        if ($item->getProductType() !== Configurable::TYPE_CODE) {
            return $result;
        }

        $productName = $this->catalogHelper->getSimpleProductName($item->getProduct());
        if (!empty($productName)) {
            $result->setName($productName);
        }
        return $result;
    }
}
