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

namespace Perficient\Wishlist\Controller\MultipleWishlist;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\MultipleWishlist\Model\WishlistEditor;
use Magento\Wishlist\Controller\WishlistProviderInterface;


class Add extends \Perficient\Wishlist\Controller\Index\Add
{
    /**
     * @param Action\Context $context
     * @param Session $customerSession
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     * @param WishlistEditor $wishlistEditor
     */
    public function __construct(
        Action\Context             $context,
        Session                    $customerSession,
        WishlistProviderInterface  $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator                  $formKeyValidator,
        protected WishlistEditor   $wishlistEditor
    )
    {
        parent::__construct(
            $context,
            $customerSession,
            $wishlistProvider,
            $productRepository,
            $formKeyValidator
        );
    }

    /**
     * Add item to wishlist
     * Create new wishlist if wishlist params (name, visibility) are provided
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/');
        }

        $customerId = $this->_customerSession->getCustomerId();
        $name = $this->getRequest()->getParam('name');
        $visibility = $this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0;
        if ($name !== null) {
            try {
                $wishlist = $this->wishlistEditor->edit($customerId, $name, $visibility);
                $this->messageManager->addSuccess(
                    __(
                        'Wish list "%1" was saved.',
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($wishlist->getName())
                    )
                );
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t create the wish list right now.'));
            }
        }
        return parent::execute();
    }
}


