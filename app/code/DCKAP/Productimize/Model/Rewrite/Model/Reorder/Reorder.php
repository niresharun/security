<?php
declare(strict_types=1);

namespace DCKAP\Productimize\Model\Rewrite\Model\Reorder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Cart\CustomerCartResolver;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\GuestCart\GuestCartResolver;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Helper\Reorder as ReorderHelper;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Sales\Model\Reorder\Data\ReorderOutput as ReorderOutput;

class Reorder
{

    /**#@+
     * Error message codes
     */
    private const ERROR_PRODUCT_NOT_FOUND = 'PRODUCT_NOT_FOUND';
    private const ERROR_INSUFFICIENT_STOCK = 'INSUFFICIENT_STOCK';
    private const ERROR_NOT_SALABLE = 'NOT_SALABLE';
    private const ERROR_REORDER_NOT_AVAILABLE = 'REORDER_NOT_AVAILABLE';
    private const ERROR_UNDEFINED = 'UNDEFINED';
    /**#@-*/

    /**
     * List of error messages and codes.
     */
    private const MESSAGE_CODES = [
        'The required options you selected are not available' => self::ERROR_NOT_SALABLE,
        'Product that you are trying to add is not available' => self::ERROR_NOT_SALABLE,
        'This product is out of stock' => self::ERROR_NOT_SALABLE,
        'There are no source items' => self::ERROR_NOT_SALABLE,
        'The fewest you may purchase is' => self::ERROR_INSUFFICIENT_STOCK,
        'The most you may purchase is' => self::ERROR_INSUFFICIENT_STOCK,
        'The requested qty is not available' => self::ERROR_INSUFFICIENT_STOCK,
    ];

    /**
     * @var OrderFactory
     */
    private $orderFactory;
    /**
     * @var CustomerCartResolver
     */
    private $customerCartProvider;
    /**
     * @var GuestCartResolver
     */
    private $guestCartResolver;
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;
    /**
     * @var ReorderHelper
     */
    private $reorderHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var Data\Error[]
     */
    private $errors = [];

    public function __construct(
        OrderFactory $orderFactory,
        CustomerCartResolver $customerCartProvider,
        GuestCartResolver $guestCartResolver,
        CartRepositoryInterface $cartRepository,
        ReorderHelper $reorderHelper,
        \Psr\Log\LoggerInterface $logger,
        ProductCollectionFactory $productCollectionFactory
    )
    {
        $this->orderFactory = $orderFactory;
        $this->customerCartProvider = $customerCartProvider;
        $this->guestCartResolver = $guestCartResolver;
        $this->cartRepository = $cartRepository;
        $this->reorderHelper = $reorderHelper;
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function aroundExecute(\Magento\Sales\Model\Reorder\Reorder $subject, \Closure $proceed, string $orderNumber, string $storeId)
    {
        $order = $this->orderFactory->create()->loadByIncrementIdAndStoreId($orderNumber, $storeId);

        if (!$order->getId()) {
            throw new InputException(
                __('Cannot find order number "%1" in store "%2"', $orderNumber, $storeId)
            );
        }
        $customerId = (int)$order->getCustomerId();
        $this->errors = [];

        $cart = $customerId === 0
            ? $this->guestCartResolver->resolve()
            : $this->customerCartProvider->resolve($customerId);
        if (!$this->reorderHelper->isAllowed($order->getStore())) {
            $this->addError((string)__('Reorders are not allowed.'), self::ERROR_REORDER_NOT_AVAILABLE);
            return $this->prepareOutput($cart);
        }

        $this->addItemsToCart($cart, $order->getItemsCollection(), $storeId);

        try {
            $this->cartRepository->save($cart);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // handle exception from \Magento\Quote\Model\QuoteRepository\SaveHandler::save
            $this->addError($e->getMessage());
        }

        $savedCart = $this->cartRepository->get($cart->getId());

        return $this->prepareOutput($savedCart);
        //return $proceed($orderNumber,$storeId);
    }

    /**
     * Add collections of order items to cart.
     *
     * @param Quote $cart
     * @param ItemCollection $orderItems
     * @param string $storeId
     * @return void
     */
    private function addItemsToCart(Quote $cart, ItemCollection $orderItems, string $storeId): void
    {
        $orderItemProductIds = [];
        /** @var \Magento\Sales\Model\Order\Item[] $orderItemsByProductId */
        $orderItemsByProductId = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($orderItems as $item) {
            if ($item->getParentItem() === null) {
                $orderItemProductIds[] = $item->getProductId();
                $orderItemsByProductId[$item->getProductId()][$item->getId()] = $item;
            }
        }

        $products = $this->getOrderProducts($storeId, $orderItemProductIds);

        // compare founded products and throw an error if some product not exists
        $productsNotFound = array_diff($orderItemProductIds, array_keys($products));
        if (!empty($productsNotFound)) {
            foreach ($productsNotFound as $productId) {
                /** @var \Magento\Sales\Model\Order\Item $orderItemProductNotFound */
                $this->addError(
                    (string)__('Could not find a product with ID "%1"', $productId),
                    self::ERROR_PRODUCT_NOT_FOUND
                );
            }
        }

        foreach ($orderItemsByProductId as $productId => $orderItems) {
            if (!isset($products[$productId])) {
                continue;
            }
            $product = $products[$productId];
            foreach ($orderItems as $orderItem) {
                $this->addItemToCart($orderItem, $cart, clone $product);
            }
        }
    }

    /**
     * Get order products by store id and order item product ids.
     *
     * @param string $storeId
     * @param int[] $orderItemProductIds
     * @return Product[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOrderProducts(string $storeId, array $orderItemProductIds): array
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setStore($storeId)
            ->addIdFilter($orderItemProductIds)
            ->addStoreFilter()
            ->addAttributeToSelect('*')
            ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner')
            ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        return $collection->getItems();
    }

    /**
     * Adds order item product to cart.
     *
     * @param OrderItemInterface $orderItem
     * @param Quote $cart
     * @param ProductInterface $product
     * @return void
     */
    private function addItemToCart(OrderItemInterface $orderItem, Quote $cart, ProductInterface $product): void
    {
        $info = $orderItem->getProductOptionByCode('info_buyRequest');
        $info = new \Magento\Framework\DataObject($info);
        $info->setQty($orderItem->getQtyOrdered());
        if ($additionalOptions = $orderItem->getProductOptionByCode('additional_options')) {
            $info->setProductimizeOptions($additionalOptions);
        }
        $addProductResult = null;
        try {
            $addProductResult = $cart->addProduct($product, $info);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->addError($this->getCartItemErrorMessage($orderItem, $product, $e->getMessage()));
        } catch (\Throwable $e) {
            $this->logger->critical($e);
            $this->addError($this->getCartItemErrorMessage($orderItem, $product), self::ERROR_UNDEFINED);
        }

        // error happens in case the result is string
        if (is_string($addProductResult)) {
            $errors = array_unique(explode("\n", $addProductResult));
            foreach ($errors as $error) {
                $this->addError($this->getCartItemErrorMessage($orderItem, $product, $error));
            }
        }
    }

    /**
     * Add order line item error
     *
     * @param string $message
     * @param string|null $code
     * @return void
     */
    private function addError(string $message, string $code = null): void
    {
        $this->errors[] = new Data\Error(
            $message,
            $code ?? $this->getErrorCode($message)
        );
    }

    /**
     * Get message error code. Ad-hoc solution based on message parsing.
     *
     * @param string $message
     * @return string
     */
    private function getErrorCode(string $message): string
    {
        $code = self::ERROR_UNDEFINED;

        $matchedCodes = array_filter(
            self::MESSAGE_CODES,
            function ($key) use ($message) {
                return false !== strpos($message, $key);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($matchedCodes)) {
            $code = current($matchedCodes);
        }

        return $code;
    }


    /**
     * Prepare output
     *
     * @param CartInterface $cart
     * @return Data\ReorderOutput
     */
    private function prepareOutput(CartInterface $cart): ReorderOutput
    {
        $output = new ReorderOutput($cart, $this->errors);
        $this->errors = [];
        // we already show user errors, do not expose it to cart level
        $cart->setHasError(false);
        return $output;
    }

    /**
     * Get error message for a cart item
     *
     * @param Item $item
     * @param Product $product
     * @param string|null $message
     * @return string
     */
    private function getCartItemErrorMessage(Item $item, Product $product, string $message = null): string
    {
        // try to get sku from line-item first.
        // for complex product type: if custom option is not available it can cause error
        $sku = $item->getSku() ?? $product->getData('sku');
        return (string)($message
            ? __('Could not add the product with SKU "%1" to the shopping cart: %2', $sku, $message)
            : __('Could not add the product with SKU "%1" to the shopping cart', $sku));
    }
}