<?php
/**
 * Add sets to My Projects (Wishlist)
 * @category: Magento
 * @package: Perficient/WishlistSet
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_WishlistSet
 */
declare(strict_types=1);

namespace Perficient\WishlistSet\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use DCKAP\Productimize\Helper\Data;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\QuoteRepository;
use Perficient\Order\Helper\Data as PerficientOrderHelper;

class AddToCart extends Action
{
    /**
     * AddToCart constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $store ,
     * @param Data $productimizeDataHelper
     * @param DataObjectFactory $objectFactory
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        Context                                     $context,
        private readonly JsonFactory                $resultJsonFactory,
        private readonly Session                    $customerSession,
        private readonly CustomerCart               $cart,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface      $store,
        private readonly Data                       $productimizeDataHelper,
        private readonly DataObjectFactory          $objectFactory,
        private readonly QuoteRepository            $quoteRepository,
        private readonly PerficientOrderHelper      $perficientOrderHelper
    )
    {
        parent::__construct($context);
    }


    public function execute()
    {

        $mainProductParams = $this->customerSession->getCartItemInfo();

        $requestParams = [];
        $requestParams['qty'] = $mainProductParams['qty'];
        $relatedProductIds = $mainProductParams['relatedIds'];
        $selectedCustomizedoptions = [];
        $relatedProductImages = "";

        $params = new \Magento\Framework\DataObject($mainProductParams);
        $buyRequest = $params->getDataByKey('buyRequest');
        $processedParams = $buyRequest->getData('_processing_params');
        $currentConf = $processedParams->getData('current_config');


        if (isset($currentConf['pz_cart_properties'])) {
            $requestParams['pz_cart_properties'] = $currentConf['pz_cart_properties'];
            $pzCartProperties = $requestParams['pz_cart_properties'];
            if (isset($currentConf['edit_id'])) {
                $requestParams['edit_id'] = 1;
                $productId = $mainProductParams['product_id'];
                $product = $this->productRepository->getById($productId);

                $productimizeDataHelper = $this->productimizeDataHelper;
                $store = $this->store->getStore();
                $productImageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
                $defaultConf = $product->getData('default_configurations');
                $selectedCustomizedoptions = [];

                if ((is_countable($relatedProductIds) ? count($relatedProductIds) : 0) > 0) {
                    if (isset($currentConf['pz_cart_properties']) && !empty($currentConf['pz_cart_properties'])) {
                        $addedParams = $productimizeDataHelper->getUnserializeData($currentConf['pz_cart_properties']);
                        if (is_array($addedParams)) {
                            if (!empty($addedParams)) {
                                foreach ($addedParams as $addedParamlabel => $addedParamValue) {
                                    if ($addedParamlabel != 'CustomImage') {
                                        $selectedCustomizedoptions[] = [
                                            'label' => $addedParamlabel,
                                            'value' => $addedParamValue
                                        ];
                                    }
                                }
                            }
                        }
                    }
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
                    $relatedProductImages = $productimizeDataHelper->generateImageInNodeJs($relatedProductIdWithImage, $currentConf, $defaultConf);
                }
            }
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $response = [];
        try {
            $currentQuote = $this->cart->getQuote();

            if ($currentQuote->getHasError()) {
                $messageCollection = $this->messageManager->getMessages(true);
                $lastMessage = $messageCollection->getLastAddedMessage();
                if (strlen((string)$lastMessage->getText())) {
                    throw new LocalizedException(__($lastMessage->getText()));
                }
            }

            $currentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
            if ($currentUserSurchargeStatus == true) {
                $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
                $items = $currentQuote->getAllItems();
                $surchargeProduct = $this->productRepository->get($surchargeProductSku);
                $surchargeProductId = $surchargeProduct->getId();
                if ($items) {
                    foreach ($items as $item) {
                        if ($item->getProductId() == $surchargeProductId) {
                            $currentQuote->removeItem($item->getItemId())->save();
                            $currentQuote->collectTotals()->save();
                        }
                    }
                }
            }

            $pzCartProperties = $currentConf['pz_cart_properties'];
            foreach ($relatedProductIds as $relatedProductId) {
                $relatedProduct = $this->productRepository->getById($relatedProductId);
                $requestParams = [];
                $requestParams['qty'] = $mainProductParams['qty'];
                if (isset($currentConf['edit_id'])) {
                    $requestParams['edit_id'] = 1;
                }
                if ($selectedCustomizedoptions && count($selectedCustomizedoptions) > 1) {
                    $relatedProduct->addCustomOption('additional_options', json_encode($selectedCustomizedoptions, JSON_THROW_ON_ERROR));
                }
                $requestParams['pz_cart_properties'] = $pzCartProperties;

                if (isset($relatedProductImages) && (!empty($relatedProductImages))) {
                    $currRelatedProductImages = $productimizeDataHelper->getUnserializeData($relatedProductImages);
                    if (isset($currRelatedProductImages) && (is_countable($currRelatedProductImages) ? count($currRelatedProductImages) : 0) > 0 && array_key_exists($relatedProductId, $currRelatedProductImages)) {
                        $_pzCartProperties = $productimizeDataHelper->getUnserializeData($pzCartProperties);
                        if (isset($_pzCartProperties['CustomImage'])) {
                            $_pzCartProperties['CustomImage'] = $currRelatedProductImages[$relatedProductId];
                            $pzCartProperties = $productimizeDataHelper->getSerializeData($_pzCartProperties);
                        }
                        $requestParams['pz_cart_properties'] = $pzCartProperties;
                        $priceParams = $productimizeDataHelper->getPriceParam($pzCartProperties, $relatedProductId, $defaultConf);
                        $requestParams['configurator_price'] = $productimizeDataHelper->getConfiguredSellingPrice($relatedProductId, $priceParams);
                    }
                }

                $request = $this->objectFactory->create();
                $request->setData($requestParams);
                $quote = $this->quoteRepository->get($currentQuote->getId());
                $relatedItem = $quote->addProduct($relatedProduct, $request);
                //$relatedItem = $this->cart->addProduct($relatedProduct, $requestParams);
                $this->quoteRepository->save($quote);

                $this->_eventManager->dispatch(
                    'checkout_cart_add_collection_product',
                    ['product' => $relatedProduct, 'quote_item' => $relatedItem]
                );
            }

            $message = __(
                'Collection has been added to your shopping cart.'
            );
            $this->messageManager->addSuccessMessage($message);

            $response['redirectUrl'] = $this->_redirect->getRefererUrl();

            return $resultJson->setData($response);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultJson->setData($response);
        }

    }

}
