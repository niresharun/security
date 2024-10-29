<?php
declare(strict_types=1);

namespace Perficient\Wishlist\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Model\Item\Option as WishlistItemOption;
use Perficient\Catalog\Helper\Data as CatalogHelper;

class WishListProductViewModel implements ArgumentInterface
{

    public function __construct(
        protected readonly CatalogHelper     $catalogHelper,
    ) {
    }
    /**
     * Return the selected child product of the configurable product which was added to wishlist
     *
     * @param ItemInterface $item
     * @return ProductInterface|null
     */
    public function getSimpleProduct(ItemInterface $item): ?ProductInterface
    {
        $product = $item->getProduct();
        return $this->getSimpleProductForGivenProduct($product);
    }

    /**
     * @param Product $product
     * @return Product|null
     */
    public function getSimpleProductForGivenProduct(Product $product): ?ProductInterface
    {
        if ($product->getTypeId() !== Configurable::TYPE_CODE) {
            return $product;
        }
        /** @var WishlistItemOption $simpleOption */
        $simpleOption = $product->getCustomOption('simple_product');
        return $simpleOption?->getProduct();
    }

    /**
     * @param Product $product
     * @param DataObject|int|array|null $requestInfo
     * @return int|null
     */
    public function getSimpleProductIdAddedToCart(Product $product, $requestInfo = null): ?int
    {
        if ($product->getTypeId() !== Configurable::TYPE_CODE) {
            return null;
        }
        $product = $this->getSimpleProductForGivenProduct($product);
        if ($product !== null) {
            return (int)$product->getId();
        }
        if (!empty($requestInfo['selected_configurable_option'])) {
            return (int)$requestInfo['selected_configurable_option'];
        }
        return null;
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    public function isMirrorProduct(ProductInterface $product): bool
    {
       return $this->catalogHelper->isMirrorProduct($product);
    }
}
