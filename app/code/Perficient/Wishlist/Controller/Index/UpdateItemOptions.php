<?php
/**
 * overide for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class UpdateItemOptions extends \Magento\Wishlist\Controller\Index\UpdateItemOptions
{
    public function __construct(
        Action\Context             $context,
        Session                    $customerSession,
        WishlistProviderInterface  $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator                  $formKeyValidator
    )
    {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context, $customerSession, $wishlistProvider, $productRepository, $formKeyValidator);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $id = (int)$this->getRequest()->getParam('id');
            /* @var \Magento\Wishlist\Model\Item */
            $item = $this->_objectManager->create(\Magento\Wishlist\Model\Item::class);
            $item->load($id);
            $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
            if (!$wishlist) {
                $resultRedirect->setPath('*/');
                return $resultRedirect;
            }

            $buyRequest = new \Magento\Framework\DataObject($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)->save();

            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();
            $this->_eventManager->dispatch(
                'wishlist_update_item',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id)]
            );

            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

            $message = __('%1 has been updated in your Wish List.', $product->getName());
            $this->messageManager->addSuccessMessage($message);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t update your Wish List right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        $pageType = $this->getRequest()->getParam('page_type');
        if (isset($pageType) && $pageType == 'collaboration') {
            $resultRedirect->setPath('*/*/collaboration', ['wishlist_id' => $wishlist->getId()]);
        } else {
            $resultRedirect->setPath('*/*', ['wishlist_id' => $wishlist->getId()]);
        }

        return $resultRedirect;
    }

}
