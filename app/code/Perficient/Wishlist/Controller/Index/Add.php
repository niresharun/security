<?php

/**
 * This is used to add product in wish list using ajax
 *
 * @category: PHP
 * @package: Perficient_Wishlist
 * @copyright: Copyright © 2021 Magento. All rights reserved.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Divya Sree <divya.sree@perficient.com>
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Controller\Index\Add as WishListAdd;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryGraphQl\Model\Resolver\StockStatusProvider;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Add
 *
 * @package Perficient\Wishlist\Controller\Index
 */
class Add extends WishListAdd
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var StockStatusProvider
     */
    private $stockStatusProvider;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite;

    /**
     * @var AreProductsSalableInterface
     */
    private $areProductsSalable;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     * @param RedirectInterface|null $redirect
     * @param UrlInterface|null $urlBuilder
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator,
        ?StockStatusProvider $stockStatusProvider = null,
        ?GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite = null,
        ?AreProductsSalableInterface $areProductsSalable = null,
        ?JsonFactory $jsonFactory = null,
        ?RedirectInterface $redirect = null,
        ?UrlInterface $urlBuilder = null
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->stockStatusProvider = $stockStatusProvider ?: ObjectManager::getInstance()->get(StockStatusProvider::class);
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite ?: ObjectManager::getInstance()->get(GetStockIdForCurrentWebsite::class);
        $this->areProductsSalable = $areProductsSalable ?: ObjectManager::getInstance()->get(AreProductsSalableInterface::class);
        $this->jsonFactory = $jsonFactory ?: ObjectManager::getInstance()->get(JsonFactory::class);
        parent::__construct($context,$customerSession,$wishlistProvider,$productRepository,$formKeyValidator,$redirect,$urlBuilder);
    }
    /**
     * Add product in Wish List
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     */
    public function execute()
    {
        $result = [];
        $relatedItems = false;

        $requestParams = $this->getRequest()->getParams();
        $isAjax = isset($requestParams['ajax']) ? (int)$requestParams['ajax'] : 0;
        unset($requestParams['ajax']);


        $superAttributes = "";
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultJson = $this->jsonFactory->create();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            if($isAjax) {
                $resultJson->setData(['success' => false,
                    'message' => 'Form Key error. Please try again later',
                    'relatedItems' => false
                ]);
                return $resultJson;
            }
            return $resultRedirect->setPath('*/');
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $session = $this->_customerSession;

        $requestParams = $this->getRequest()->getParams();

        if (!empty($requestParams['super_attribute'])) {
            $superAttributes .= "#";
            foreach ($requestParams['super_attribute'] as $key => $value) {
                $superAttributes .= sprintf('%s=%s&', $key, $value);
            }
            $superAttributes = rtrim($superAttributes, '&');
        }
        if ($session->getBeforeWishlistRequest()) {
            $requestParams = $session->getBeforeWishlistRequest();
            $session->unsBeforeWishlistRequest();
        }

        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        if (!$productId) {
            if($isAjax) {
                $resultJson->setData(['success' => false,
                    'message' => 'Product Not Found',
                    'relatedItems' => false
                ]);
                return $resultJson;
            }
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
            $relatedProductIds = $product->getRelatedProductIds();
            $productName = $product->getName();
            foreach ($relatedProductIds as $relatedProductId) {
                $relatedProduct = $this->productRepository->getById($relatedProductId);
                $stockId = $this->getStockIdForCurrentWebsite->execute();
                $resultRelated = $this->areProductsSalable->execute([$relatedProduct->getSku()], $stockId);
                $resultRelated = current($resultRelated);

                if ($resultRelated->isSalable()) {
                    $relatedItems = true;
                }
            }
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            if($isAjax) {
                $resultJson->setData(['success' => false,
                    'message' => 'We can\'t specify a product.',
                    'relatedItems' => false
                ]);
                return $resultJson;
            }
            $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $buyRequest = new \Magento\Framework\DataObject($requestParams);
            $result = $wishlist->addNewItem($product, $buyRequest);

            if (is_string($result)) {
                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }
            if ($wishlist->isObjectNew()) {
                $wishlist->save();
            }
            $this->_eventManager->dispatch(
                'wishlist_add_product',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
            );

            if($isAjax) {
                $resultJson->setData(['success' => true,
                    'message' => $productName,
                    'relatedItems' => $relatedItems
                ]);
                return $resultJson;
            }

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_redirect->getRefererUrl();
            }

            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

            $this->messageManager->addComplexSuccessMessage(
                'addProductSuccessMessage',
                [
                    'product_name' => $product->getName(),
                    'referer' => $referer
                ]
            );
            // phpcs:disable Magento2.Exceptions.ThrowCatch
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add the item to Wish List right now.')
            );
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl() . $superAttributes);
        return $resultRedirect;
    }
}
