<?php
declare(strict_types=1);

namespace Wendover\Catalog\Observer;

use Magento\Catalog\Block\Product\View as ProductViewBlock;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Perficient\Catalog\Helper\Data as CatalogHelper;
use Perficient\PriceMultiplier\Model\ProductPrice;

class UpdateMultiplePrice implements ObserverInterface
{
    /**
     * @param ProductPrice $productPrice
     * @param ProductViewBlock $productView
     */
    public function __construct(
        private readonly ProductPrice     $productPrice,
        private readonly ProductViewBlock $productView,
        private readonly CatalogHelper $catalogHelper
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $responseObject = $observer->getData('response_object');

        $product = $this->productView->getProduct();
        if (!$this->catalogHelper->isConfigurableMirrorProduct($product)) {
            return;
        }
        $productId = $product->getId();
        $itemPrice = $this->productPrice->getItemPrice([$productId]);
        $multiplePrice = $itemPrice[$productId]['unformatted_price'];
        $prices = [
            'baseOldPrice' => [
                'amount' => $multiplePrice
            ],
            'oldPrice' => [
                'amount' => $multiplePrice
            ],
            'basePrice' => [
                'amount' => $multiplePrice
            ],
            'finalPrice' => [
                'amount' => $multiplePrice
            ],
        ];
        $additionalOptions = $responseObject->getAdditionalOptions();
        $additionalOptions['prices'] = $prices;
        $responseObject->setAdditionalOptions($additionalOptions);
        $observer->setData('response_object', $responseObject);
    }
}
