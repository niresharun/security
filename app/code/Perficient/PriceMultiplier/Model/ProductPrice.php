<?php
/**
 * PriceMultiplier module for multiplier price .
 *
 * @category:  JS
 * @package:   Perficient/PriceMultiplier
 * @copyright:
 * See COPYING.txt for license details.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords:  Module Perficient_PriceMultiplier
 */
declare(strict_types=1);

namespace Perficient\PriceMultiplier\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Perficient\RolesPermission\Helper\Data as RolesPermissionHelper;
use Perficient\Productimize\Model\ProductConfiguredPrice;
use Perficient\MyCatalog\Helper\Data;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;


/**
 * Class ProductPrice
 * @package Perficient\PriceMultiplier\Model
 */
class ProductPrice
{
    final const DISCOUNT_TYPE_STANDARD = 'standard';
    final const DISCOUNT_TYPE_POST_DISCOUNTED = 'post-discounted';

    /**
     * ProductPrice constructor.
     * @param ResourceConnection $resource
     * @param StoreManagerInterface $storeManager
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param ProductConfiguredPrice $productConfiguredPrice
     * @param Data $helper
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        protected readonly ResourceConnection     $resource,
        protected readonly CustomerSession        $customerSession,
        protected readonly StoreManagerInterface  $storeManager,
        protected readonly RoleInfo               $roleInfo,
        protected readonly Escaper                $escaper,
        protected readonly ProductConfiguredPrice $productConfiguredPrice,
        protected readonly Data                   $helper,
        protected readonly PriceCurrencyInterface $priceCurrency,
        private   readonly ProductFactory         $productFactory,
        protected readonly Product                $product
    )
    {

    }

    /**
     * @param $productIds
     */
    public function getItemPrice($productIds): array
    {
        $items = $this->getProductPriceByIds($productIds);
        $responseData = [];
        foreach ($items as $key => $item) {
            $item['currency_code'] = $this->getCurrencySymbol();
            $item['unformatted_price'] = $item['display_price'] ?? "";
            $item['display_price'] = $item['display_price'] ? $this->formatCurrency($item['display_price']) : '';
            $item['strikeout_price'] = $item['strikeout_price'] ? $this->formatCurrency($item['strikeout_price']) : '';
            $responseData[$key] = $item;
        }

        return $responseData;
    }

    /**
     * Get SKUs by products Ids
     *
     * @param $productIds array
     */
    protected function getProductPriceByIds(array $productIds): mixed
    {
        if (empty($productIds)) {
            return null;
        }
        $items = [];
        $multiplier = $this->customerSession->getMultiplier() ?? 1;
        $discountType = $this->customerSession->getDiscountType();
        $discountAvailable = $this->customerSession->getDiscountAvailable();
        $displayStrikeOut = $this->customerSession->getStrikeOut();

        $isCustomer = false;
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);

        if (is_countable($currentUserRole) ? count($currentUserRole) : 0) {
            if ($discountAvailable && !isset($displayStrikeOut)) {
                $displayStrikeOut = 1;

                if ($currentUserRole[0] == $this->escaper->escapeHtml(RolesPermissionHelper::CUSTOMER_CUSTOMER_ROLE_NAME)) {
                    $displayStrikeOut = 0;
                }
                $this->customerSession->setStrikeOut($displayStrikeOut);
            }
        }
        foreach ($productIds as $productId) {
            $productId = (int) $productId;
            $priceData = $this->getProductPriceById($productId);
            $basePrice = $priceData['price'];
            $product = $this->productFactory->create()->load($productId);
            if($product->getTypeId() == 'configurable'){
                $basePrice = $this->getConfigurableProductPrice($productId);
            }
            $finalPrice = $this->productConfiguredPrice->applyCompanyDiscount($basePrice);
            $price = $finalPrice;

            $strikeOutPrice = '';

            if ($discountType == self::DISCOUNT_TYPE_STANDARD) {
                $price = $basePrice;
            }

            if ($discountAvailable && $displayStrikeOut) {
                if ($discountType == self::DISCOUNT_TYPE_POST_DISCOUNTED) {
                    $strikeOutPrice = $basePrice * $multiplier;
                }
            } else if (!$isCustomer) {
                $price = $basePrice;
            }
            $displayPrice = $price * $multiplier;

            if ($displayPrice == 0) {
                $displayPrice = '';
            }

            if ($displayPrice >= $strikeOutPrice) {
                $strikeOutPrice = '';
            }

            $items[$priceData['entity_id']] = [
                'display_price' => $displayPrice,
                'strikeout_price' => $strikeOutPrice
            ];
        }

        return $items;
    }

    /**
     * Get mininum price
     *
     * @param $productId array
     */
    protected function getConfigurableProductPrice($productId): float {
        /* for simple product of configurable product */
        $configurable = $this->product->load($productId);
        $children = $configurable->getTypeInstance()->getChildrenIds($configurable->getId());
        $priceMin = null;
        foreach (current($children) as $childId) {
            $priceData = $this->getProductPriceById($childId);
            $basePrice = $priceData['price'];
            if ($priceMin === null) {
                $priceMin = $basePrice;
            }
            $priceMin = min($priceMin, $basePrice);
        }
        return (float)$priceMin;
    }

    /**
     * Get SKUs by products Ids
     *
     * @param $productId
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getProductPriceById($productId): mixed
    {
        $customerGrpId = $this->customerSession->getCustomerGroupId();
        if (empty($productId)) {
            return null;
        }

        $connection = $this->resource->getConnection();
        $select = $connection->select();
        $select->from(
            ['cpe' => $connection->getTableName('catalog_product_index_price')],
            [
                'entity_id', 'price', 'final_price'
            ]
        )->where('entity_id = ?', $productId)
            ->where('customer_group_id = ?', $customerGrpId);

        return $connection->fetchRow($select);
    }

    /**
     * Retrieve company account
     *
     * @param $value float
     * @param $invalid string
     * @return  float
     */
    protected function formatCurrency($value)
    {
        if (empty($value)) {
            return $value;
        }
        return $this->getFormatedPrice($value);
    }

    /**
     * Get Store Currency Symbol
     * @throws
     */
    protected function getCurrencySymbol(): string
    {
        return $this->helper->getCurrencySymbol();
    }

    /**
     * Function getFormatedPrice
     *
     * @param  $price string
     * @param $includeContainer bool
     * @return string
     */
    public function getFormatedPrice($price, $includeContainer = false)
    {
        return $this->priceCurrency->convertAndFormat($price, $includeContainer);
    }
}
