<?php
/**
 * Wishlist Sidebar Section.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_WishlistSet
 */
declare(strict_types=1);

namespace Perficient\WishlistSet\CustomerData;

use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Perficient\Catalog\Helper\Data;
use Perficient\Productimize\Model\ProductConfiguredPrice;
use DCKAP\Productimize\Helper\Data as ProductimizeHelper;
use Wendover\Theme\ViewModel\WendoverViewModel;

class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    /**
     * Wishlist constructor.
     * @param Data $catalogHelper
     * @param ProductConfiguredPrice $productConfiguredPrice
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data                                                            $wishlistHelper,
        \Magento\Wishlist\Block\Customer\Sidebar                                                 $block,
        \Magento\Catalog\Helper\ImageFactory                                                     $imageHelperFactory,
        \Magento\Framework\App\ViewInterface                                                     $view,
        private readonly ItemResolverInterface $itemResolver,
        private readonly Data                                                                    $catalogHelper,
        private readonly ProductConfiguredPrice                                                  $productConfiguredPrice,
        private readonly ProductimizeHelper                                                      $productimizeHelper,
        private readonly WendoverViewModel $wendoverViewModel
    )
    {
        parent::__construct(
            $wishlistHelper,
            $block,
            $imageHelperFactory,
            $view,
            $itemResolver
        );
    }

    /**
     * Retrieve wishlist item data
     *
     * @return array
     */
    protected function getItemData(\Magento\Wishlist\Model\Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();

        $buyRequest = $wishlistItem->getBuyRequest();
        $productConfiguration = $buyRequest->getPzCartProperties();
        $size = '';
        if ($productConfiguration) {
            $confData = $this->catalogHelper->getValidCustomizedOptions($productConfiguration, true);
            $data = $confData['dataArray'];
            if (isset($data['Size'])) {
                $size = $data['Size'];
            }
        }
        $buyRequest = $wishlistItem->getBuyRequest();
        if (isset($buyRequest['edit_id']) && !empty($buyRequest['edit_id']) &&
            isset($buyRequest['configurator_price']) && !empty($buyRequest['configurator_price'])) {
            $priceData = $this->productConfiguredPrice->getConfigratorItemPrice($buyRequest['configurator_price']);
            $displayPrice = $priceData['display_price'];
            $priceHtml = '<div class="price-box price-configured_price" data-role="priceBox" data-product-id="' . $product->getId() . '" data-price-box="product-id-' . $product->getId() . '">';
            $priceHtml .= '<p class="price-as-configured">';
            $priceHtml .= '<span class="price-container price-configured_price tax weee">';
            $priceHtml .= '<span id="product-price-' . $product->getId() . '" class="price-wrapper "><span class="price">' . $displayPrice . '</span></span>';
            $priceHtml .= '</span>';
            $priceHtml .= '</p>';
            $priceHtml .= '</div>';
        } else {
            $priceHtml = $this->block->getProductPriceHtml(
                $product,
                'wishlist_configured_price',
                \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                ['item' => $wishlistItem]
            );
        }

        // To get customizer image in sidebar image
        $wishlistProductImageUrl = $wishlistItem->getData('wishlist_item_id') ? $this->productimizeHelper->getImageurlforwishlistId($wishlistItem->getData('wishlist_item_id')) : "";
        if ($wishlistProductImageUrl) {
            $imageResult = $this->getWishListImageData($this->itemResolver->getFinalProduct($wishlistItem), $wishlistProductImageUrl);
        } else {
            $imageResult = $this->getImageData($this->itemResolver->getFinalProduct($wishlistItem));
        }
        $productName = $product->getName();
        if ($this->wendoverViewModel->isMirrorProduct($product)) {
            $productName = ($this->wendoverViewModel->getProductName($product) ?: $productName);
        }
        return [
            'image' => $imageResult,
            'product_sku' => $product->getSku(),
            'product_id' => $product->getId(),
            'product_url' => $this->wishlistHelper->getProductUrl($wishlistItem),
            'product_name' => $productName,
            'product_price' => $priceHtml,
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility(),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->wishlistHelper->getAddToCartParams($wishlistItem),
            'delete_item_params' => $this->wishlistHelper->getRemoveParams($wishlistItem),
            'size' => $size,
        ];
    }

    /**
     * Retrieve product image data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getWishListImageData($product, $wishlistProductImageUrl)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->imageHelperFactory->create()
            ->init($product, 'wishlist_sidebar_block');

        $template = 'Magento_Catalog/product/image_with_borders';
        $imageSize = [$helper->getWidth(), $helper->getHeight()];
        $width = $helper->getFrame()
            ? $helper->getWidth()
            : $imageSize[0];

        $height = $helper->getFrame()
            ? $helper->getHeight()
            : $imageSize[1];

        return [
            'template' => $template,
            'src' => $wishlistProductImageUrl,
            'width' => $width,
            'height' => $height,
            'alt' => $helper->getLabel(),
        ];
    }
}
