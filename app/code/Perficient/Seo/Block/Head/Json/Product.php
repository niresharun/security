<?php
/**
 * @category: Magento
 * @package: Perficient/Seo
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Ankita Bodhankar <ankita.bodhankar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Seo
 */
declare(strict_types=1);

namespace Perficient\Seo\Block\Head\Json;

use MageWorx\SeoMarkup\Block\Head\Json\Product as ParentProduct;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\SeoAll\Helper\SeoFeaturesStatusProvider;

class Product extends ParentProduct
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Product constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageWorx\SeoMarkup\Helper\Product $helperProduct,
        \MageWorx\SeoMarkup\Helper\DataProvider\Product $helperDataProvider,
        \MageWorx\SeoMarkup\Helper\DataProvider\RelatedProducts $helperRelatedProducts,
        \Magento\Catalog\Helper\Data $helperCatalog,
        \Magento\Framework\View\Element\Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        SeoFeaturesStatusProvider $seoFeaturesStatusProvider,
        array $data = []
    ) {
        parent::__construct(
            $registry,
            $helperProduct,
            $helperDataProvider,
            $helperRelatedProducts,
            $helperCatalog,
            $context,
            $priceCurrency,
            $seoFeaturesStatusProvider,
            $data
        );
    }

    /**
     *
     * @return array
     */
    protected function getJsonProductData(): array
    {
        $product = $this->registry->registry('current_product');

        if (!$product) {
            return [];
        }

        $this->_product = $product;

        $data                = [];
        $data['@context']    = 'http://schema.org';
        $data['@type']       = 'Product';
        $data['name']        = $this->_product->getName();
        $data['description'] = $this->helperDataProvider->getDescriptionValue($this->_product);
        $data['image']       = $this->helperDataProvider->getProductImage($this->_product)->getImageUrl();

        $offers = $this->getOfferData();
        if (!empty($offers['price']) || !empty($offers[0]['price'])) {
            unset($offers['price']);
            unset($offers['priceCurrency']);
            $data['offers'] = $offers;
        }

        $aggregateRatingData = $this->helperDataProvider->getAggregateRatingData($this->_product, false);

        if (!empty($aggregateRatingData)) {
            $aggregateRatingData['@type'] = 'AggregateRating';
            $data['aggregateRating']      = $aggregateRatingData;
        }

        /**
         * Google console error: "Either 'offers', 'review' or 'aggregateRating' should be specified"
         */
        if ($this->helperProduct->isRsEnabledForSpecificProduct() === false
            && empty($data['aggregateRating'])
            && empty($data['offers'])
        ) {
            return [];
        }

        if (!empty($data['aggregateRating']) && $this->helperProduct->isReviewsEnabled()) {
            $reviewData = $this->helperDataProvider->getReviewData($this->_product, false);

            if (!empty($reviewData)) {
                $data['review'] = $reviewData;
            }
        }

        $productIdValue = $this->helperDataProvider->getProductIdValue($this->_product);

        if ($productIdValue) {
            $data['productID'] = $productIdValue;
        }

        $color = $this->helperDataProvider->getColorValue($this->_product);
        if ($color) {
            $data['color'] = $color;
        }

        $brand = $this->helperDataProvider->getBrandValue($this->_product);
        if ($brand) {
            $data['brand'] = $brand;
        }

        $manufacturer = $this->helperDataProvider->getManufacturerValue($this->_product);
        if ($manufacturer) {
            $data['manufacturer'] = $manufacturer;
        }

        $model = $this->helperDataProvider->getModelValue($this->_product);
        if ($model) {
            $data['model'] = $model;
        }

        $gtin = $this->helperDataProvider->getGtinData($this->_product);
        if (!empty($gtin['gtinType']) && !empty($gtin['gtinValue'])) {
            $data[$gtin['gtinType']] = $gtin['gtinValue'];
        }

        $skuValue = $this->helperDataProvider->getSkuValue($this->_product);
        if ($skuValue) {
            $data['sku'] = $skuValue;
        }

        $weightValue = $this->helperDataProvider->getWeightValue($this->_product);
        if ($weightValue) {
            $data['weight'] = $weightValue;
        }

        $categoryName = $this->helperDataProvider->getCategoryValue($this->_product);
        if ($categoryName) {
            $data['category'] = $categoryName;
        }

        $customProperties = $this->helperProduct->getCustomProperties();

        if ($customProperties) {
            foreach ($customProperties as $propertyName => $propertyValue) {
                if (!$propertyName || !$propertyValue) {
                    continue;
                }
                $value = $this->helperDataProvider->getCustomPropertyValue($product, $propertyValue);
                if ($value) {
                    $data[$propertyName] = $value;
                }
            }
        }

        return $data;
    }
}
