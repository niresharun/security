<?php
/**
 * Added to handle product surcharge if minimum order amount not met by customer
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */
declare(strict_types=1);

namespace Perficient\Order\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Data
 * @package Perficient\Order\Helper
 */
class Data extends AbstractHelper
{
    const MINIMUM_ORDER_AMOUNT_CONFIGURATION_PATH = 'order/general/minimum_order_amount';
    const FLAT_SURCHARGE_CONFIGURATION_PATH = 'order/general/flat_surcharge';
    const SURCHARGE_PRODUCT_SKU_CONFIGURATION_PATH = 'order/general/surcharge_sku';

    /**
     * Data constructor.
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        private readonly Session $customerSession,
        protected ProductRepository $productRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getCurrentUserSurchargeStatus()
    {
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getSurchargeStatus();
        }
    }

    /**
     * @return mixed
     */
    public function getMinimumOrderAmount()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::MINIMUM_ORDER_AMOUNT_CONFIGURATION_PATH, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getSurchargeProductSku()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::SURCHARGE_PRODUCT_SKU_CONFIGURATION_PATH, $storeScope);
    }

    /**
     * @return mixed
     */
    public function getFlatSurchargeAmount()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::FLAT_SURCHARGE_CONFIGURATION_PATH, $storeScope);
    }

    /**
     * @param null $sku
     * @param int $qty
     * @return \Magento\Framework\DataObject
     */
    public function makeAddRequest(\Magento\Catalog\Model\Product $product, $sku = null, $qty = 1)
    {
        $data = [
            'product' => $product->getEntityId(),
            'qty' => $qty
        ];

        switch ($product->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                $data = $this->setConfigurableRequestOptions($product, $sku, $data);
                break;
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                $data = $this->setBundleRequestOptions($product, $data);
                break;
        }

        $request = new \Magento\Framework\DataObject();
        $request->setData($data);

        return $request;
    }

    /**
     * @param $sku
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setConfigurableRequestOptions(\Magento\Catalog\Model\Product $product, $sku, array $data)
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typedProduct */
        $typedProduct = $product->getTypeInstance();

        $childProduct = $this->productRepository->get($sku);
        $productAttributeOptions = $typedProduct->getConfigurableAttributesAsArray($product);

        $superAttributes = [];
        foreach ($productAttributeOptions as $option) {
            $superAttributes[$option['attribute_id']] = $childProduct->getData($option['attribute_code']);
        }

        $data['super_attribute'] = $superAttributes;
        return $data;
    }

    /**
     * @return array
     */
    public function setBundleRequestOptions(\Magento\Catalog\Model\Product $product, array $data)
    {
        /** @var \Magento\Bundle\Model\Product\Type $typedProduct */
        $typedProduct = $product->getTypeInstance();

        $selectionCollection = $typedProduct->getSelectionsCollection($typedProduct->getOptionsIds($product), $product);

        $options = [];
        foreach ($selectionCollection as $proselection) {
            $options[$proselection->getOptionId()] = $proselection->getSelectionId();
        }

        $data['bundle_option'] = $options;
        return $data;
    }
}
