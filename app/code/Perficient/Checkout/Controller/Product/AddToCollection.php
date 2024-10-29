<?php
/**
 * This module is used to prepare add to collection configurable url on checkout
 *
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */
declare(strict_types=1);

namespace Perficient\Checkout\Controller\Product;

use DCKAP\Productimize\Helper\Data as ProductimizeHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Catalog\Helper\Data as PerficientCatalogHelper;
use Perficient\Order\Helper\Data as PerficientOrderHelper;

/**
 * Class AddToCollection
 * @package Perficient\Checkout\Controller\Product
 */
class AddToCollection extends \Magento\Checkout\Controller\Cart
{
    /**
     * AddToCollection constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param ProductRepositoryInterface $productRepository
     * @param JsonFactory $resultJsonFactory
     * @param QuoteRepository $quoteRepository
     * @param DataObjectFactory $objectFactory
     * @param Http $request
     * @param Json $jsonSerializer
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     */
    public function __construct(
        Context                                  $context,
        ScopeConfigInterface                     $scopeConfig,
        Session                                  $checkoutSession,
        private readonly StoreManagerInterface   $storeManager,
        Validator                                $formKeyValidator,
        CustomerCart                             $cart,
        protected ProductRepositoryInterface     $productRepository,
        private readonly JsonFactory             $resultJsonFactory,
        private readonly PerficientOrderHelper   $perficientOrderHelper,
        private readonly PerficientCatalogHelper $perficientCatalogHelper,
        private readonly QuoteRepository         $quoteRepository,
        private readonly DataObjectFactory       $objectFactory,
        protected Http                           $request,
        private readonly ProductimizeHelper      $productimizeHelper,
        protected Json                           $jsonSerializer,
        private readonly ResponseFactory         $responseFactory,
        private readonly UrlInterface            $url
    )
    {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
    }

    public function execute()
    {
        $previousUrl = [];
        $addParams = [];
        if (isset($_SERVER['HTTP_REFERER'])) {
            $previousUrl = explode('/', substr((string)$_SERVER['HTTP_REFERER'], 0, -1));
        } else {
            $previousUrl[] = 'cart';
        }
        try {
            if ($this->cart->getQuote()->getHasError()) {
                $messageCollection = $this->messageManager->getMessages(true);
                $lastMessage = $messageCollection->getLastAddedMessage();
                if (strlen((string)$lastMessage->getText())) {
                    throw new LocalizedException(__($lastMessage->getText()));
                }
            }
            $productId = $this->getRequest()->getParam('product');
            $customizer = $this->getRequest()->getParam('customizer');
            $currentLayout = "";
            $currentLayout = $this->getRequest()->getParam('currentLayout');
            $storeId = $this->storeManager->getStore()->getId();
            $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
            $surchargeProduct = $this->productRepository->get($surchargeProductSku);
            $surchargeProductId = $surchargeProduct->getId();
            $getAllCartProductIds = [];
            if ($this->cart->getQuote()->getAllVisibleItems() > 0) {
                foreach ($this->cart->getQuote()->getAllVisibleItems() as $item) {
                    $getAllCartProductIds[] = $item->getProductId();
                    if ($item->getProductId() == $surchargeProductId) {
                        $this->cart->getQuote()->removeItem($item->getItemId());
                    }
                }
            }
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productRepository->getById($productId, false, $storeId);
            $getCurrentCartCount = is_countable($this->cart->getQuote()->getAllVisibleItems()) ? count($this->cart->getQuote()->getAllVisibleItems()) : 0;
            $relatedProductIds = $product->getRelatedProductIds();
            if ($currentLayout && $currentLayout == "catalog_product_view") {
                if (!in_array($productId, $getAllCartProductIds))
                    $relatedProductIds[] = $productId;
            }
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $quoteItems = $this->cart->getQuote()->getAllVisibleItems();
            $pzCartProperties = '';
            $infoBuyRequest = '';
            if ((is_countable($quoteItems) ? count($quoteItems) : 0) > 0) {
                $itemId = $this->getRequest()->getParam('id', false);
                foreach ($quoteItems as $item) {
                    if ($itemId == $item->getId() || empty($itemId)) {
                        $quantity = $item->getQty();
                        if ($productId == $item->getProductId()) {
                            $buyRequest = $item->getBuyRequest();
                            if (isset($buyRequest['pz_cart_properties'])) {
                                $pzCartProperties = $buyRequest['pz_cart_properties'];
                            }
                            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                            $productimizeCustomOptions = [];
                            if (isset($options['options']) && !empty($options['options'])) {
                                $customOptions = $options['options'];
                                if (!empty($customOptions)) {
                                    foreach ($customOptions as $option) {
                                        $productimizeCustomOptions[] = [
                                            'label' => $option['label'],
                                            'value' => $option['value'],
                                        ];
                                    }
                                }
                            }
                            if (isset($buyRequest['edit_id']) && ($buyRequest['edit_id'] == 1) && (($itemId == $item->getId()) || ($currentLayout == 'catalog_product_view'))) {
                                if (isset($options['info_buyRequest']) && (!empty($options['info_buyRequest']))) {
                                    $infoBuyRequest = $options['info_buyRequest'];
                                }
                            }
                        }
                    }
                }
            }
            if (isset($relatedProductIds) && !empty($relatedProductIds)) {
                $productImageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
                $defaultConf = $product->getData('default_configurations');
                if (!empty($infoBuyRequest)) {
                    $relatedProductIdWithImage = [];
                    foreach ($relatedProductIds as $relatedProductId) {
                        $relatedProduct = $this->productRepository->getById($relatedProductId);
                        $relatedProductImage = $relatedProduct->getData('image');
                        $croppedImg = $relatedProduct->getResource()->getAttribute('cropped')->getFrontend()->getValue($relatedProduct);
                        if ($croppedImg && !empty($croppedImg) && $croppedImg != "no_selection") {
                            $relatedProductImage = $croppedImg;
                        }
                        $relatedProductImage = $productImageUrl . $relatedProductImage;
                        $relatedProductIdWithImage[$relatedProductId] = $relatedProductImage;
                    }

                    $relatedProductImages = $this->productimizeHelper->generateImageInNodeJs($relatedProductIdWithImage, $infoBuyRequest, $defaultConf);

                    if ($relatedProductImages !== null && (trim((string)$relatedProductImages) != '')) {
                        $addParams['edit_id'] = 1;
                        $relatedProductImages = $this->jsonSerializer->unserialize($relatedProductImages);
                    }
                }
                foreach ($relatedProductIds as $relatedProductId) {
                    $relatedProduct = $this->productRepository->getById($relatedProductId);
                    if (!empty($productimizeCustomOptions)) {
                        $relatedProduct->addCustomOption(
                            'additional_options',
                            $this->jsonSerializer->serialize($productimizeCustomOptions)
                        );
                    }
                    if (!isset($quantity)) {
                        $quantity = 1;
                    }
                    $addParams['qty'] = $quantity;
                    $addParams['product'] = $relatedProductId;
                    $addParams['product_id'] = $relatedProductId;
                    $addParams['item'] = $relatedProductId;
                    if ($pzCartProperties) {
                        if ($item->getBuyRequest()['edit_id'] == 1) {
                            $addParams['edit_id'] = 1;
                            // $addParams['params_addtocart'] = $pzCartProperties;

                            if (isset($relatedProductImages) && !empty($relatedProductImages) && array_key_exists($relatedProductId, $relatedProductImages)) {
                                $_pzCartProperties = $this->productimizeHelper->getUnserializeData($pzCartProperties);
                                if (isset($_pzCartProperties['CustomImage'])) {
                                    $_pzCartProperties['CustomImage'] = $relatedProductImages[$relatedProductId];
                                    $pzCartProperties = $this->jsonSerializer->serialize($_pzCartProperties);
                                }
                            }
                            if ($pzCartProperties) {
                                $priceParams = $this->productimizeHelper->getPriceParam($pzCartProperties, $relatedProductId, $defaultConf);
                                $addParams['configurator_price'] = $this->productimizeHelper->getConfiguredSellingPrice($relatedProductId, $priceParams);
                            }
                        }
                        $addParams['pz_cart_properties'] = $pzCartProperties;
                    }
                    if (($getCurrentCartCount == 0 || empty($pzCartProperties))
                        && $currentLayout && $currentLayout == "catalog_product_view") {
                        $defaultConf = $product->getData('default_configurations');
                        $confData = $this->perficientCatalogHelper->getDefaultConfigurationJson($defaultConf);
                        $jsonStr = $confData['jsonStr'] ?: '';
                        ($jsonStr) ? $addParams['pz_cart_properties'] = $jsonStr : '';
                    }
                    $request = $this->objectFactory->create();
                    $request->setData($addParams);
                    $quote = $this->quoteRepository->get($this->cart->getQuote()->getId());
                    $result = $quote->addProduct($relatedProduct, $request);
                    $this->quoteRepository->save($quote);
                    $this->_eventManager->dispatch(
                        'checkout_cart_add_product_complete',
                        ['product' => $relatedProduct, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                    );

                    $this->_eventManager->dispatch(
                        'checkout_cart_add_collection_product',
                        ['product' => $relatedProduct, 'quote_item' => $result]
                    );
                }
                $this->messageManager->addSuccessMessage(__('Collection has been added in your cart.'));
            } else {
                $this->messageManager->addSuccessMessage(__('No product assigned in collection.'));
            }
            if (isset($customizer)) {

                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($relatedProductIds);

                return $resultJson;
            } else {

                if (end($previousUrl) == 'cart') {

                    $RedirectUrl = $this->url->getUrl('checkout/cart');
                    $this->responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
                    return;
                } else {

                    return $this->_redirect($this->_redirect->getRefererUrl());
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            if (!$this->getRequest()->getParam('customizer')) {

                if (end($previousUrl) == 'cart') {

                    $RedirectUrl = $this->url->getUrl('checkout/cart');
                    $this->responseFactory->create()->setRedirect($RedirectUrl)->sendResponse();
                    return;
                } else {

                    return $this->_redirect($this->_redirect->getRefererUrl());
                }
            }
        }
    }
}
