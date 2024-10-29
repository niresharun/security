<?php
/**
 * Add Products to wishlist by sku
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar<Amin.akhtar@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Controller\Index;
use Magento\CatalogInventory\Api\StockConfigurationInterface as stockConfiguration;
use Magento\CatalogInventory\Model\StockStateException;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Catalog\Helper\Data as PRFTHelper;

/**
 * Add products by sku in wishlist
 * @package Perficient\Wishlist\Controller\Index
 */
class AddBySku extends Action
{

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param WishlistProviderInterface $wishlistProvider
     * @param StoreManagerInterface $store
     */


    public function __construct(
        Context                                     $context,
        private readonly JsonFactory                $resultJsonFactory,
        private readonly Session                    $customerSession,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly WishlistProviderInterface  $wishlistProvider,
        private readonly StoreManagerInterface      $store,
        private readonly PRFTHelper                 $prftHelper,
        private readonly stockConfiguration         $stockConfiguration
    )
    {
        parent::__construct($context);
    }

    /**
     * Function to add products by sku in wishlist
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $response = [
            'errors' => false,
            'message' => ''
        ];
        $this->getResponse()->setNoCacheHeaders();
        $storeId = $this->store->getStore()->getId();
        try {
            $requestParams = $this->getRequest()->getParams();
            $requestSkus = $requestParams['sku_to_add'];
            $skus = (isset($requestSkus) && !empty($requestSkus)) ? $requestSkus : null;

            $wishlist = $this->wishlistProvider->getWishlist();
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }

            try {
                if (!empty($skus)) {
                    $skusArrExp = explode(',', (string)$skus);
                    $skusArr = array_filter($skusArrExp);
                    $skusCount = count($skusArr);
                    $skipProductCount = 0;
                    $outofstockProduct = 0;
                    $notAvailable = 0;
                    $missingConfiguration = 0;
                    $outofstockProductSKU = '';
                    $notAvailableSKU = '';
                    $missingConfigurationSKU ='';
                    if ($skusCount > 0) {
                        foreach ($skusArr as $sku) {
                            if (!empty($sku)) {
                                try {
                                    /** @var \Magento\Catalog\Model\Product $product */
                                    $product = $this->productRepository->get(trim($sku), false, $storeId);
                                } catch (NoSuchEntityException $e) {
                                    $product = null;
                                }


                                //If Product is missing skip
                                if (!$product || !$product->isVisibleInCatalog()) {
                                    $notAvailableSKU .= $notAvailableSKU ? ', ' . $sku : $sku;
                                    $skipProductCount++;
                                    $notAvailable++;
                                    continue;
                                }

                                //If Product is out of the stock skip
                                if (!$this->stockConfiguration->isShowOutOfStock($storeId) && !$product->getIsSalable()) {

                                    $outofstockProductSKU .= $outofstockProductSKU ? ', ' . $sku : $sku;
                                    $outofstockProduct++;
                                    $skipProductCount++;
                                    continue;
                                }

                                $productId = $product->getId();
                                $defaultConf = $product->getData('default_configurations');

                                //If Product default configuration is missing skip
                                if (!$defaultConf) {
                                    $missingConfigurationSKU .= $missingConfigurationSKU ? ', ' . $sku : $sku;
                                    $skipProductCount++;
                                    $missingConfiguration++;
                                    continue;
                                }

                                $formattedConf = $this->prftHelper->getDefaultConfigurationJson($defaultConf);
                                $requestParams['product'] = $productId;
                                $requestParams['pz_cart_properties'] = $formattedConf['jsonStr'];
                                $this->getRequest()->setParams($requestParams);

                                $buyRequest = new \Magento\Framework\DataObject($requestParams);
                                $result = $wishlist->addNewItem($product, $buyRequest);
                                if (is_string($result)) {
                                    throw new \Magento\Framework\Exception\LocalizedException(__($result));
                                }
                            }
                        }

                        if ($wishlist->isObjectNew()) {
                            $wishlist->save();
                        }

                        $this->resultMessage($skusCount, $skipProductCount, $outofstockProduct, $notAvailable, $missingConfiguration, $outofstockProductSKU, $notAvailableSKU, $missingConfigurationSKU);


                        $this->customerSession->unsWishlistProductParams();

                        $response = [
                            'errors' => false,
                            'message' => __('Added Item successful.')
                        ];
                        $response['redirectUrl'] = $this->_redirect->getRefererUrl();

                        return $resultJson->setData($response);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
                    return;
                }
            } catch (LocalizedException $e) {
                $response = [
                    'errors' => true,
                    'message' => __($e->getMessage())
                ];
            }
        } catch (LocalizedException $e) {
            $response = [
                'errors' => true,
                'message' => __($e->getMessage())
            ];
        }

        return $resultJson->setData($response);
    }


        /**
     * Function for result messages
     *
     */
    public function resultMessage($skusCount, $skipProductCount, $outofstockProduct, $notAvailable, $missingConfiguration, $outofstockProductSKU, $notAvailableSKU, $missingConfigurationSKU)
    {

        if ($skusCount != $skipProductCount) {
            $message = __(
                'Items has been added to your wishlist.'
            );
            $this->messageManager->addSuccessMessage($message);
        }

        if ($outofstockProduct) {
            $this->messageManager->addErrorMessage("Product SKU`s ".$outofstockProductSKU." are currently out of stock. You cannot add these products to this project.");
        }

        if ($notAvailable) {
            $message = __(
                'Items with SKU`s : ' . $notAvailableSKU . ' not available.'
            );
            $this->messageManager->addErrorMessage($message);
        }

        if ($missingConfiguration) {
            $message = __(
                'Item configurations are missing for SKU`s : ' . $missingConfigurationSKU
            );
            $this->messageManager->addErrorMessage($message);
        }


    }
}