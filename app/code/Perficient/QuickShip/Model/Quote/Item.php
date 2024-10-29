<?php
/**
 * Quickship requested qty validation
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<sandeep.mude@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);

namespace Perficient\QuickShip\Model\Quote;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;

class Item extends QuoteItem
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Quote\Model\Quote\Item\OptionFactory $itemOptionFactory,
        \Magento\Quote\Model\Quote\Item\Compare $quoteItemCompare,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        private readonly GetProductSalableQtyInterface $getProductSalableQty,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $productRepository,
            $priceCurrency,
            $statusListFactory,
            $localeFormat,
            $itemOptionFactory,
            $quoteItemCompare,
            $stockRegistry,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );
    }

    /**
     * @throws InputException
     * @throws LocalizedException
     */
    public function setQty($qty)
    {
        $qty = $this->_prepareQty($qty);
        $oldQty = $this->_getData(self::KEY_QTY);
        $sku = $this->getSku();
        $stockId = 1;
        $salableQty = $this->getProductSalableQty->execute($sku, $stockId);
        $quote = $this->getQuote();
        if ($quote && $quote->getData('quick_ship') && $qty > $salableQty) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The requested qty is not available.'));
        } else {
            $this->setData(self::KEY_QTY, $qty);
            $this->_eventManager->dispatch('sales_quote_item_qty_set_after', ['item' => $this]);
            if ($this->getQuote() && $this->getQuote()->getIgnoreOldQty()) {
                return $this;
            }
            if ($this->getUseOldQty()) {
                $this->setData(self::KEY_QTY, $oldQty);
            }
        }
        return $this;
    }

}
