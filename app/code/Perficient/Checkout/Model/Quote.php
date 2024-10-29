<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\Checkout\Model;

use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Sales\Model\Status;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class Quote extends \Magento\Quote\Model\Quote
{
    final const MAX_SHOPPING_CART_ITEMS_PATH = 'checkout/cart/max_items_in_cart';

    final const MAX_CART_ITEMS_MESSAGE_PATH = 'checkout/cart/max_items_in_cart_message';

    /**
     * Const for Quote Item Table to use in count query.
     */
    final const QUOTE_ITEM_TABLE = 'quote_item';

    public function __construct(
        \Magento\Framework\Model\Context                                   $context,
        \Magento\Framework\Registry                                        $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory                  $extensionFactory,
        AttributeValueFactory                                              $customAttributeFactory,
        \Magento\Quote\Model\QuoteValidator                                $quoteValidator,
        \Magento\Catalog\Helper\Product                                    $catalogProduct,
        \Magento\Framework\App\Config\ScopeConfigInterface                 $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface                         $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface                 $_config,
        \Magento\Quote\Model\Quote\AddressFactory                          $quoteAddressFactory,
        \Magento\Customer\Model\CustomerFactory                            $customerFactory,
        \Magento\Customer\Api\GroupRepositoryInterface                     $groupRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory    $quoteItemCollectionFactory,
        \Magento\Quote\Model\Quote\ItemFactory                             $quoteItemFactory,
        \Magento\Framework\Message\Factory                                 $messageFactory,
        Status\ListFactory                                                 $statusListFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface                    $productRepository,
        \Magento\Quote\Model\Quote\PaymentFactory                          $quotePaymentFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory,
        \Magento\Framework\DataObject\Copy                                 $objectCopyService,
        \Magento\CatalogInventory\Api\StockRegistryInterface               $stockRegistry,
        \Magento\Quote\Model\Quote\Item\Processor                          $itemProcessor,
        \Magento\Framework\DataObject\Factory                              $objectFactory,
        \Magento\Customer\Api\AddressRepositoryInterface                   $addressRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder                       $criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder                               $filterBuilder,
        \Magento\Customer\Api\Data\AddressInterfaceFactory                 $addressDataFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory                $customerDataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface                  $customerRepository,
        \Magento\Framework\Api\DataObjectHelper                            $dataObjectHelper,
        \Magento\Framework\Api\ExtensibleDataObjectConverter               $extensibleDataObjectConverter,
        \Magento\Quote\Model\Cart\CurrencyFactory                          $currencyFactory,
        JoinProcessorInterface                                             $extensionAttributesJoinProcessor,
        \Magento\Quote\Model\Quote\TotalsCollector                         $totalsCollector,
        \Magento\Quote\Model\Quote\TotalsReader                            $totalsReader,
        \Magento\Quote\Model\ShippingFactory                               $shippingFactory,
        \Magento\Quote\Model\ShippingAssignmentFactory                     $shippingAssignmentFactory,
        private readonly ResourceConnection                                $resourceConnection,
        private readonly LoggerInterface                                   $psrLogger,
        \Magento\Framework\Model\ResourceModel\AbstractResource            $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb                      $resourceCollection = null,
        array                                                              $data = [],
        \Magento\Sales\Model\OrderIncrementIdChecker                       $orderIncrementIdChecker = null,
        AllowedCountries                                                   $allowedCountriesReader = null
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $quoteValidator,
            $catalogProduct,
            $scopeConfig,
            $storeManager,
            $_config,
            $quoteAddressFactory,
            $customerFactory,
            $groupRepository,
            $quoteItemCollectionFactory,
            $quoteItemFactory,
            $messageFactory,
            $statusListFactory,
            $productRepository,
            $quotePaymentFactory,
            $quotePaymentCollectionFactory,
            $objectCopyService,
            $stockRegistry,
            $itemProcessor,
            $objectFactory,
            $addressRepository,
            $criteriaBuilder,
            $filterBuilder,
            $addressDataFactory,
            $customerDataFactory,
            $customerRepository,
            $dataObjectHelper,
            $extensibleDataObjectConverter,
            $currencyFactory,
            $extensionAttributesJoinProcessor,
            $totalsCollector,
            $totalsReader,
            $shippingFactory,
            $shippingAssignmentFactory,
            $resource,
            $resourceCollection,
            $data,
            $orderIncrementIdChecker,
            $allowedCountriesReader
        );
    }

    /**
     * Add product. Returns error message if product type instance can't prepare product.
     *
     * @param mixed $product
     * @param null|float|\Magento\Framework\DataObject $request
     * @param null|string $processMode
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addProduct(
        \Magento\Catalog\Model\Product $product,
                                       $request = null,
                                       $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ): \Magento\Quote\Model\Quote\Item|string
    {
        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = $this->objectFactory->create(['qty' => $request]);
        }
        if (!$request instanceof \Magento\Framework\DataObject) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }

        if (!$product->isSalable()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Product that you are trying to add is not available.')
            );
        }

        $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);

        /**
         * Error message
         */
        if (is_string($cartCandidates) || $cartCandidates instanceof \Magento\Framework\Phrase) {
            return (string)$cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        $parentItem = null;
        $errors = [];
        $item = null;
        $items = [];
        $maxItemsInCart = $this->getMaxItemsInCart();
        $maxItemsMessage = $this->getMaxItemsInCartMessage();
        foreach ($cartCandidates as $candidate) {
            // Child items can be sticked together only within their parent
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            $candidate->setStickWithinParent($stickWithinParent);

            $item = $this->getItemByProduct($candidate);
            if (!$item) {
                $quoteId = $this->getId();
                if ($quoteId && !empty($quoteId)) {
                    $itemsInCart = $this->getQuoteItemCounts($quoteId);
                    if (!empty($maxItemsInCart) && !empty($maxItemsMessage)
                        && $itemsInCart && $itemsInCart >= $maxItemsInCart) {
                        $this->setHasError(true);
                        throw new \Exception($maxItemsMessage);
                    }
                }

                $item = $this->itemProcessor->init($candidate, $request);
                $item->setQuote($this);
                $item->setOptions($candidate->getCustomOptions());
                $item->setProduct($candidate);
                // Add only item that is not in quote already
                $this->addItem($item);
            }
            $items[] = $item;

            /**
             * As parent item we should always use the item of first added product
             */
            if (!$parentItem) {
                $parentItem = $item;
            }
            if ($parentItem && $candidate->getParentProductId() && !$item->getParentItem()) {
                $item->setParentItem($parentItem);
            }

            $this->itemProcessor->prepare($item, $request, $candidate);

            // collect errors instead of throwing first one
            if ($item->getHasError()) {
                $this->deleteItem($item);
                foreach ($item->getMessage(false) as $message) {
                    if (!in_array($message, $errors)) {
                        // filter duplicate messages
                        $errors[] = $message;
                    }
                }
                break;
            }
        }
        if (!empty($errors)) {
            throw new \Magento\Framework\Exception\LocalizedException(__(implode("\n", $errors)));
        }

        $this->_eventManager->dispatch('sales_quote_product_add_after', ['items' => $items]);
        return $parentItem;
    }

    /**
     * @return mixed
     */
    public function getMaxItemsInCart()
    {
        $maxItems = $this->_config->getValue(
            self::MAX_SHOPPING_CART_ITEMS_PATH,
            ScopeInterface::SCOPE_STORE
        );

        return $maxItems;
    }

    /**
     * @return mixed
     */
    public function getMaxItemsInCartMessage()
    {
        $maxItemsMessage = $this->_config->getValue(
            self::MAX_CART_ITEMS_MESSAGE_PATH,
            ScopeInterface::SCOPE_STORE
        );

        return $maxItemsMessage;
    }

    /**
     * @param $quoteId
     */
    public function getQuoteItemCounts($quoteId): int|string
    {
        $quoteItemsCount = 0;
        try {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select()->from(
                $this->resourceConnection->getTableName(self::QUOTE_ITEM_TABLE),
                ['cnt' => 'count(*)']
            )->where(
                'quote_id = ' . $quoteId
            );
            $quoteItemsCount = $connection->fetchOne($select);
        } catch (\Exception $e) {
            $this->psrLogger->info($e);
        }

        return (int)$quoteItemsCount;
    }
}
