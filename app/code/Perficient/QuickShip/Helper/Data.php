<?php
/**
 * Inventory for Quick Ship
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);
namespace Perficient\QuickShip\Helper;

use Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\Header;
use Perficient\Wishlist\ViewModel\WishListProductViewModel as ProductViewModel;

/**
 * Helper Class for the Quick Ship
 */
class Data extends AbstractHelper{

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var object
     */
    private $connection;

    /**
     * @const string Config Path for Surcharge SKU
     */
    const SURCHARGE_PRODUCT_SKU_CONFIGURATION_PATH = 'order/general/surcharge_sku';

    /**
     * @const string Notification message when cart item is removed
     */
    const CART_REMOVED_MESSAGE = 'Removed some products from the cart as it is no longer available as Quick Ship';

    /**
     * @const string Add to cart restrict message when regular product is added to cart having quick ship products
     */
    const QUICK_SHIP_RESTRICT_MESSAGE = 'quickship/general/add_to_cart_restrict_message';

    /**
     * @const string Add to cart restrict message when quickship product is added to cart when cart contain regular products
     */
    const QUICK_SHIP_REGULAR_RESTRICT_MESSAGE = 'quickship/general/add_to_cart_regular_restrict_message';

    /**
     * @const string Add to cart restrict message when quickship product is added to cart when cart contain regular products
     */
    const QUICK_SHIP_QTY_RESTRICT_MESSAGE = 'quickship/general/quickship_qty_restrict_message';


    const QUICK_SHIP_OVER_PURCHASE_RESTRICT_MESSAGE = 'quickship/general/quickship_over_purchase_restrict_message';

    /**
     * @const string
     */
    const QUICK_SHIP_FIELD = 'is_quick_ship';

    /**
     * @const string
     */
    const QUICK_SHIP_ATTRIBUTE = 'quick_ship';

    /**
     * @const string
     */
    const QUICK_SHIP_CATEGORY_NAME = 'quickship/general/quick_ship_category_name';

    /**
     * @var
     */
    private $quickShipCategoryName;

    /**
     * Data constructor.
     * @param Context $context
     * @param Cart $cart
     * @param Session $checkoutSession
     * @param QuoteRepository $quoteRepository
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param ManagerInterface $messageManager
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     * @param Http $routeHttp
     * @param ResourceConnection $resource
     * @param Header $header
     */
    public function __construct(
        Context $context,
        protected Cart $cart,
        Session $checkoutSession,
        QuoteRepository $quoteRepository,
        protected StoreManagerInterface $storeManager,
        protected ProductRepositoryInterface $productRepository,
        protected ManagerInterface $messageManager,
        private readonly GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        protected Http $routeHttp,
        ResourceConnection $resource,
        private readonly Header $header,
        private readonly ProductViewModel $productViewModel
    ) {
        parent::__construct($context);
        $this->quote = $checkoutSession->getQuote();
        $this->connection = $resource->getConnection();
    }

    /**
     * Validate cart for Quick Ship
     * @return bool
     */
    public function validateQuickShipCart()
    {
        $isQuoteItemRemoved = false;
        $surchargeSKU = $this->getSurchargeSKU();
        $storeId = $this->storeManager->getStore()->getId();

        if(!empty($this->quote)) {
            $items = $this->quote->getAllItems();
            $hasQuickShipInCart = $this->quote->getData(self::QUICK_SHIP_ATTRIBUTE);
            if ($items && $hasQuickShipInCart) {
                foreach ($items as $item) {
                    $product = $this->productRepository->getById($item->getProductId(), false, $storeId);
                    if($product && $product->getSku() != $surchargeSKU ) {
                        $isQuickShipProduct = $product->getData(self::QUICK_SHIP_FIELD);

                        if (!$isQuickShipProduct){
                            $isQuoteItemRemoved = true;
                            $this->cart->removeItem($item->getItemId())->save();
                        }
                    }
                }
            }
        }

        $this->cart->getQuote()->collectTotals()->save();

        return $isQuoteItemRemoved;
    }

    /**
     * Get Surcharge SKU from Config
     * @return mixed
     */
    public function getSurchargeSKU() {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::SURCHARGE_PRODUCT_SKU_CONFIGURATION_PATH, $storeScope);
    }

    /**
     * Get Cart Item remove Message
     * @return string
     */
    public function getCartItemRemovedMessage() {
        return self::CART_REMOVED_MESSAGE;
    }

    /**
     * @throws LocalizedException
     */
    public function restrictCart($productInfo, $requestInfo = null)
    {
        $productInfo = $this->productViewModel
            ->getSimpleProductIdAddedToCart($productInfo, $requestInfo) ?: $productInfo;
        $product = $this->getProduct($productInfo);

        if(empty($product)) {
            return;
        }

        $itemCount = $this->quote->getItemsCount();
        $hasQuickShipInCart = $this->quote->getData(self::QUICK_SHIP_ATTRIBUTE);

        $isQuickShip = $this->_request->getParam('quick_ship_product');
        $fromQuickShip = 0;
        if(isset($isQuickShip) && $isQuickShip) {
            $fromQuickShip = 1;
        }

        $productId = $product->getId();
        $salable = $this->getSalableQuantityDataBySku->execute($product->getSku());
        $isQuickShipProduct = $product->getData(self::QUICK_SHIP_FIELD);

        if($fromQuickShip && ($salable[0]['qty'] < 1 || !$isQuickShipProduct )) {
            $this->quote->setHasError(true);
            $message = $this->getQuickShipQtyRestrictMessage();
            if(strlen((string) $message)) {
                throw new Exception($message);
            }
        }

        if ($itemCount && $productId) {
            if($this->checkCartRestriction($hasQuickShipInCart, $isQuickShipProduct, $fromQuickShip)) {
                $message = $this->getRestrictMessage();
                if(strlen((string) $message)) {
                    throw new Exception($message);
                }
            }
            if($this->regularProductRestriction($hasQuickShipInCart, $isQuickShipProduct, $fromQuickShip)) {
                $message = $this->getRegularProductRestrictMessage();
                if(strlen((string) $message)) {
                    throw new Exception($message);
                }
            }
        }

        $requestedQty = $this->_request->getParam('qty');
        $currentActionName =  $this->routeHttp->getFullActionName();
        if(is_array($requestedQty) && $currentActionName == 'wishlist_index_allcart'){
            $flipRequestedQty = array_flip($requestedQty);
            $select = $this->connection->select();
            $getWishListItemId = $select->from(
                ['ur' => $this->connection->getTableName('wishlist_item')],
                ['wishlist_item_id']
                 )->where('product_id = ?', $product->getId());
            $result = $this->connection->fetchCol($select);

            $currentProductQuantity =  array_intersect($flipRequestedQty, $result);
            $requestedQty = array_key_first($currentProductQuantity);

        }
        if(!$requestedQty) {
            $requestedQty = 1;
        }
        $productId = $this->_request->getParam('product');
        if(empty($productId)){
            $productId = $product->getId();
        }
        $hasProductInCart = $this->quote->hasProductId($productId);

        $qty = 0;
        if($hasProductInCart) {
            foreach ($this->quote->getAllItems() as $item) {
                if ($item->getProductId() == $productId) {
                    $qty = $item->getQty();
                    break;
                }
            }
        }
        $requestedQty += $qty;

        if($fromQuickShip && $isQuickShipProduct && $salable[0]['qty'] < $requestedQty ) {
            $this->quote->setHasError(true);
            $message = $this->getQuickShipOverPurchaseRestrictMessage();
            if(strlen((string) $message)) {
                throw new Exception(sprintf($message, $salable[0]['qty']));
            }
        }
    }

    /**
     * @param $hasQuickShipInCart
     * @param $isQuickShipProduct
     * @param $fromQuickShip
     * @return bool
     */
    public function checkCartRestriction($hasQuickShipInCart, $isQuickShipProduct, $fromQuickShip) {
        if ($hasQuickShipInCart && (!$isQuickShipProduct || !$fromQuickShip ) ) {
            $this->quote->setHasError(true);
            return true;
        }
        return false;
    }

    /**
     * @param $hasQuickShipInCart
     * @param $isQuickShipProduct
     * @param $fromQuickShip
     * @return bool
     */
    public function regularProductRestriction($hasQuickShipInCart, $isQuickShipProduct, $fromQuickShip) {
        if (!$hasQuickShipInCart && $isQuickShipProduct && $fromQuickShip ) {
            $this->quote->setHasError(true);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getRestrictMessage() {
        return $this->scopeConfig->getValue(self::QUICK_SHIP_RESTRICT_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getRegularProductRestrictMessage() {
        return $this->scopeConfig->getValue(self::QUICK_SHIP_REGULAR_RESTRICT_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getQuickShipQtyRestrictMessage() {
        return $this->scopeConfig->getValue(self::QUICK_SHIP_QTY_RESTRICT_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getQuickShipOverPurchaseRestrictMessage() {
        return $this->scopeConfig->getValue(self::QUICK_SHIP_OVER_PURCHASE_RESTRICT_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param $productId
     */
    public function restrictCartForAddCollection($productId)
    {
        $product = $this->getProduct($productId);
        if(empty($product)) {
            return;
        }

        $relatedProductIds = $product->getRelatedProductIds();
        if(empty(array_filter($relatedProductIds))) {
            return;
        }

        $itemCount = $this->quote->getItemsCount();
        if (!$itemCount){
            return;
        }

        $hasQuickShipInCart = $this->quote->getData(self::QUICK_SHIP_ATTRIBUTE);

        $isQuickShip = $this->_request->getParam('quick_ship_product');

        $fromQuickShip = 0;
        if(isset($isQuickShip) && $isQuickShip) {
            $fromQuickShip = 1;
        }

        $isQuickShipProduct = true;
        foreach($relatedProductIds as $relatedProductId) {
            $relatedProduct = $this->getProduct($relatedProductId);
            if(!empty($relatedProduct)) {
                if(!$relatedProduct->getData(self::QUICK_SHIP_FIELD)) {
                    $isQuickShipProduct = false;
                    break;
                }
            }
        }

        if($this->checkCartRestriction($hasQuickShipInCart, $isQuickShipProduct, $fromQuickShip)) {
            $message = $this->getRestrictMessage();
            if(strlen((string) $message)) {
                $this->messageManager->addErrorMessage($message);
            }
        }
    }

    /**
     * @param $productInfo
     * @return \Magento\Catalog\Api\Data\ProductInterface|Product|null
     */
    protected function getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException) {
                return $product;
            }
        }
        return $product;
    }

    /**
     * get quick ship categry name.
     *
     * @return mixed
     */
    public function getQuickShipCategoryName()
    {
        if (!$this->quickShipCategoryName) {
            $this->quickShipCategoryName = $this->scopeConfig->getValue(
                self::QUICK_SHIP_CATEGORY_NAME,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->quickShipCategoryName;
    }

    /**
     * @return int
     */
    public function isFromQuickShip()
    {
        $fromQuickShip = 0;
        if(!empty($this->_request->getParam('q')) && $this->_request->getParam('q') == self::QUICK_SHIP_FIELD){
            $fromQuickShip = 1;
        }
        return $fromQuickShip;
    }
}
