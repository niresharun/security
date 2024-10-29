<?php
declare(strict_types=1);

namespace Perficient\Catalog\ViewModel;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\Checkout\Model\Cart;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Perficient\Catalog\Helper\Data as CatalogHelper;
use Perficient\Productimize\Model\ProductDetails;
use Perficient\Wishlist\Helper\Data as WishlistHelper;
use Perficient\Wishlist\ViewModel\WishListProductViewModel;

class CatalogViewModel implements ArgumentInterface
{
    /**
     * Path to config value that contains weight unit
     */
    public const XML_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';

    /**
     * @var array
     * Used only on PDP
     */
    public static $pdpLabelsList = [
        'medium',
        'treatment',
        'item_height',
        'item_width',
        'frame_width',
        //'frame_depth',
        'default_frame_depth',
        'liner_width',
        //'liner_depth',
        'default_liner_depth',
        'top_mat_size_bottom',
        'top_mat_size_left',
        'top_mat_size_right',
        'top_mat_size_top',
        'bottom_mat_size_bottom',
        'bottom_mat_size_left',
        'bottom_mat_size_right',
        'bottom_mat_size_top',
    ];

    /**
     * @var array
     * Used only on PDP
     */
    public static $pdpSwatchesList = [
        'frame_default_sku',
        'liner_sku',
        'top_mat_default_sku',
        'bottom_mat_default_sku',
    ];

    /**
     * @var array
     * Used at multiple places - need to check if other details needs to be added****
     */
    public static $textOnlyOptions = [
        'medium' => 'Medium',
        'treatment' => 'Treatment',
        //?//'frame_skus' => 'Frame SKUs',
        'frame_default_sku' => 'Frame',
        'frame_width' => 'Frame Width',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        //?//'top_mat_skus' => 'Top Mat SKUs',
        'top_mat_default_sku' => 'Top Mat',
        'liner_width' => 'Liner Width',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
    ];

    /**
     * @param Cart $cart
     * @param SerializerInterface $serializer
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductDetails $productDetails
     * @param Image $imageHelper
     * @param Context $httpContext
     * @param WishlistHelper $wishListHelper
     * @param WishListProductViewModel $wishListProductViewModel
     * @param CatalogHelper $catalogHelper
     */
    public function __construct(
        private readonly Cart                  $cart,
        private readonly SerializerInterface   $serializer,
        private readonly ScopeConfigInterface  $scopeConfig,
        private readonly ProductDetails        $productDetails,
        private readonly Image                 $imageHelper,
        private readonly Context               $httpContext,
        private readonly WishlistHelper        $wishListHelper,
        private readonly WishListProductViewModel $wishListProductViewModel,
        private readonly CatalogHelper         $catalogHelper
    ){
    }

    /**
     * Get frame stock AJAX Url
     * @return string|null
     */
    public function getFrameStockAjaxUrl(): ?string
    {
        return $this->catalogHelper->getFrameStockAjaxUrl();
    }

    /**
     * Check stock alert enabled
     * Check whether stock alert is allowed
     * @return bool
     */
    public function isStockAlertEnabled()
    {
        return $this->catalogHelper->isStockAlertEnabled();
    }

    /**
     * Used only on PDP for skipping to be displayed as default available options for product
     * @return array
     */
    public function skipLabelDirectDisplay()
    {
        return $this->catalogHelper->skipLabelDirectDisplay();
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    public function getDefaultConfigurationForPDP(ProductInterface $product)
    {
        $defaultConf = $product->getData('default_configurations');
        if (empty($defaultConf)) {
            return [];
        }
        if (!$this->isMirrorProduct($product)) {
            return $this->catalogHelper->getDefaultConfigurationForPDP($defaultConf);
        }

        // will retrieve the simple product for mirror product type
        $simpleProduct = $this->wishListProductViewModel->getSimpleProductForGivenProduct($product);
        if (empty($simpleProduct)) {
            return $this->catalogHelper->getDefaultConfigurationForPDP($defaultConf);
        }
        $defaultConf = $simpleProduct->getData('default_configurations');

        return $this->catalogHelper->getDefaultConfigurationForPDP(
            $defaultConf,
            CatalogHelper::$defaultConfMirrorProductLabel
        );
    }

    /**
     * @param $name
     * @param string $path
     * @return string
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function getSwatchImagePathForDefaultConf($name, $path = CatalogHelper::MEDIA_PATH)
    {
        return $this->catalogHelper->getSwatchImagePathForDefaultConf($name, $path);
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    public function getDefaultConfigurationJson(ProductInterface $product)
    {
        $defaultConf = $product->getData('default_configurations');
        if (empty($defaultConf)) {
            return [];
        }
        if (!$this->isMirrorProduct($product)) {
            return $this->catalogHelper->getDefaultConfigurationJson($defaultConf);
        }

        // will retrieve the simple product for mirror product type
        $simpleProduct = $this->wishListProductViewModel->getSimpleProductForGivenProduct($product);
        if (empty($simpleProduct)) {
            return $this->catalogHelper->getDefaultConfigurationJson(
                $defaultConf,
                CatalogHelper::$defaultConfMirrorProductLabel
            );
        }
        $defaultConf = $simpleProduct->getData('default_configurations');

        return $this->catalogHelper->getDefaultConfigurationJson(
            $defaultConf,
            CatalogHelper::$defaultConfMirrorProductLabel
        );
    }

    /**
     * @param int $itemID
     * @return string
     */
    public function getSideMark(int $itemID): string
    {
        return $this->wishListHelper->getSideMark($itemID);
    }

    /**
     * @param int $itemID
     * @return string
     */
    public function getCartSideMark(int $itemID): string
    {
        if (empty($itemID)) {
            return '';
        }
        $itemOption = $this->cart->getQuote();
        if (empty($itemOption?->getItemById($itemID)?->getBuyRequest())) {
            return '';
        }
        $pzData = $itemOption->getItemById($itemID)->getBuyRequest()->getData();
        if (empty($pzData['pz_cart_properties'])) {
            return '';
        }
        $pzArray = $this->serializer->unserialize($pzData['pz_cart_properties']);
        if (empty($pzArray['Side Mark'])) {
            return '';
        }
        return $pzArray['Side Mark'];
    }

    /**
     * @param $treatmentSku
     * @return string
     */
    public function getDisplayName($sku, $table)
    {
        return $this->productDetails->getDisplayName($sku, $table);
    }

    /**
     * @return string
     */
    public function getDefaultPlaceholderImg()
    {
        return $this->imageHelper->getDefaultPlaceholderUrl('small_image');
    }
    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEIGHT_UNIT, ScopeInterface::SCOPE_STORE);
    }
    /**
     * get Custom Image Data from table
     *
     * @param string $sku
     * @param string $type
     * @return array
     */
    public function getCustomImgData($sku, $type)
    {
        return $this->catalogHelper->getCustomImgData($sku, $type);
    }

    /**
     * @param $categoryIds
     * @return CategoryInterface[]|void
     */
    public function getProductCategories($categoryIds)
    {
        return $this->catalogHelper->getProductCategories($categoryIds);
    }

    public function customerIsLogin()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
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
