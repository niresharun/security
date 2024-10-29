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

namespace Perficient\WishlistSet\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;


class WishlistMessageObserver implements ObserverInterface
{
    /**
     * WishlistMessageObserver constructor.
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param ProductRepository $productRepository
     * @param Session $customerSession
     */
    public function __construct(
        protected ManagerInterface              $messageManager,
        protected RequestInterface              $request,
        protected ProductRepositoryInterface    $productRepository,
        protected Session                       $customerSession
    )
    {
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest()->getParams();
        $productId = isset($request['product']) ? (int)$request['product'] : null;
        if ($productId) {
            $product = $this->productRepository->getById($productId);
            $relatedProduct = $product->getRelatedProductIds();

            $this->messageManager->getMessages(true);
            $session = $this->customerSession;
            $referer = $session->getBeforeWishlistUrl();

            if (empty($relatedProduct)) {
                $session->unsWishlistProductParams();

                $this->messageManager->addComplexSuccessMessage(
                    'addProductSuccessMessage',
                    [
                        'product_name' => $product->getName(),
                        'referer' => $referer
                    ]
                );
            } else {
                $session->unsWishlistProductParams();
                $session->setWishlistProductParams($request);
                $this->messageManager->addComplexSuccessMessage(
                    'addProductSuccessMessage',
                    [
                        'product_name' => $product->getName(),
                        'product_id' => $product->getId(),
                        'referer' => $referer
                    ]
                );
            }
        }

    }
}
