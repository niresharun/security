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
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use DCKAP\Productimize\Helper\Data;

/**
 * Fetch product stock data
 * @package Perficient\PriceMultiplier\Controller\Product
 */
class AddToWishlist extends Action
{
    /**
     * AddToWishlist constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        Context                                     $context,
        private readonly JsonFactory                $resultJsonFactory,
        private readonly Session                    $customerSession,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly WishlistProviderInterface  $wishlistProvider,
        private readonly StoreManagerInterface      $store,
        private readonly Data                       $productimizeDataHelper
    )
    {
        parent::__construct($context);
    }

    /**
     * Function to add related products in wishlist
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $relatedProductIdWithImage = [];
        $response = [];

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $this->getResponse()->setNoCacheHeaders();
        try {
            $requestParams = $this->getRequest()->getParams();
            $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
            if (!$productId) {
                $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
                return;
            }

            $mainProductParams = $this->customerSession->getWishlistProductParams();

            if (isset($mainProductParams['qty'])) {
                $requestParams['qty'] = $mainProductParams['qty'];
            }
            if (isset($mainProductParams['wishlist_id'])) {
                $requestParams['wishlist_id'] = $mainProductParams['wishlist_id'];
            }
            if (isset($mainProductParams['edit_id'])) {
                $requestParams['edit_id'] = $mainProductParams['edit_id'];
            }

            // if (isset($mainProductParams['pz_cart_properties'])) {
            //     $requestParams['pz_cart_properties'] = $mainProductParams['pz_cart_properties'];
            // }

            $this->getRequest()->setParams($requestParams);

            $wishlist = $this->wishlistProvider->getWishlist();
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }

            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productRepository->getById($productId);
            $relatedProductIds = $product->getRelatedProductIds();

            $productimizeDataHelper = $this->productimizeDataHelper;
            $store = $this->store->getStore();
            $productImageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
            $defaultConf = $product->getData('default_configurations');
            $relatedProductImages = "";
            if ((is_countable($relatedProductIds) ? count($relatedProductIds) : 0) > 0) {
                if (isset($mainProductParams['edit_id'])) {
                    foreach ($relatedProductIds as $relatedProductId) {
                        $product = $this->productRepository->getById($relatedProductId);
                        if (!$product || !$product->isVisibleInCatalog()) {
                            continue;
                        }
                        $relatedProductImage = '';
                        $croppedImg = $product->getResource()->getAttribute('Cropped')->getFrontend()->getValue($product);
                        //$images = $productimizeDataHelper->getProductImages($product->getSku(), ['cropped' => 'croppedImg']);
                        //$croppedImg = (isset($images) && isset($images['croppedImg'])) ? $images['croppedImg'] : '';
                        if ((!empty($croppedImg) && $croppedImg != "no_selection")) {
                            $relatedProductImage = $croppedImg;
                        } else {
                            $relatedProductImage = $product->getData('image');
                        }
                        $relatedProductImage = $productImageUrl . $relatedProductImage;
                        $relatedProductIdWithImage[$product->getId()] = $relatedProductImage;

                    }

                    $relatedProductImages = $productimizeDataHelper->generateImageInNodeJs($relatedProductIdWithImage, $mainProductParams, $defaultConf);
                }

                foreach ($relatedProductIds as $relatedProductId) {
                    $product = $this->productRepository->getById($relatedProductId);
                    if (!$product || !$product->isVisibleInCatalog()) {
                        continue;
                    }
                    $requestParams = $this->getRequest()->getParams();
                    if (isset($mainProductParams['qty'])) {
                        $requestParams['qty'] = $mainProductParams['qty'];
                    }

                    $requestParams['product'] = $relatedProductId;
                    if (isset($mainProductParams['options'])) {
                        $requestParams['options'] = $mainProductParams['options'];
                    }
                    if (isset($mainProductParams['pz_cart_properties'])) {
                        $pzCartProperties = $mainProductParams['pz_cart_properties'];

                        if (isset($mainProductParams['edit_id']) && ($relatedProductImages !== null) && (trim((string)$relatedProductImages) != '')) {
                            $currRelatedProductImages = $productimizeDataHelper->getUnserializeData($relatedProductImages);

                            if (isset($currRelatedProductImages) && !empty($currRelatedProductImages) && array_key_exists($relatedProductId, $currRelatedProductImages)) {
                                $_pzCartProperties = $productimizeDataHelper->getUnserializeData($pzCartProperties);
                                if (isset($_pzCartProperties['CustomImage'])) {
                                    $_pzCartProperties['CustomImage'] = $currRelatedProductImages[$relatedProductId];
                                    $pzCartProperties = $productimizeDataHelper->getSerializeData($_pzCartProperties);
                                }
                                $priceParams = $productimizeDataHelper->getPriceParam($pzCartProperties, $relatedProductId, $defaultConf);
                                $requestParams['configurator_price'] = $productimizeDataHelper->getConfiguredSellingPrice($relatedProductId, $priceParams);
                            }
                        }
                        $requestParams['pz_cart_properties'] = $pzCartProperties;
                        $this->getRequest()->setParams($requestParams);
                    }
                    $buyRequest = new \Magento\Framework\DataObject($requestParams);
                    $result = $wishlist->addNewItem($product, $buyRequest);
                }
                if ($wishlist->isObjectNew()) {
                    $wishlist->save();
                }

                $message = __(
                    'Collection has been added to your wishlist.'
                );
                $this->messageManager->addSuccessMessage($message);
            }
            $this->customerSession->unsWishlistProductParams();

            $response = [
                'errors' => false,
                'message' => __('Added Item successful.')
            ];
            $response['redirectUrl'] = $this->_redirect->getRefererUrl();
            return $resultJson->setData($response);

        } catch (LocalizedException $e) {
            $response['message'] = $e->getMessage();
        }
        return $resultJson->setData($response);
    }

}
