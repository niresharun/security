<?php
/**
 * Controller for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Controller\MyProjects;

use Magento\Wishlist\Controller\Index\Index as WishlistIndex;
use Magento\Framework\App\Action;
use Magento\MultipleWishlist\Helper\Data as MultipleWishlistHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Framework\Exception\NotFoundException;

class Index extends WishlistIndex
{
    /**
     * Index constructor.
     * @param Action\Context $context
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        Action\Context                          $context,
        WishlistProviderInterface               $wishlistProvider,
        private readonly MultipleWishlistHelper $multipleWishlistHelper
    )
    {
        parent::__construct($context, $wishlistProvider);
    }

    public function execute()
    {
        $wishlistId = null;
        try {
            if (!$this->multipleWishlistHelper->isMultipleEnabled()) {
                throw new NotFoundException(__('Page not found.'));
            } else {
                $wishlists = $this->multipleWishlistHelper->getCustomerWishlists();
                $defaultWishlistId = $this->multipleWishlistHelper->getDefaultWishlist()->getId();
                foreach ($wishlists as $wishlist) {
                    if ($defaultWishlistId != $wishlist->getId()) {
                        $wishlistId = $wishlist->getId();
                        break;
                    }
                }
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                if ($wishlistId) {
                    $defaultWishlistId = $wishlistId;
                } else {
                    $this->messageManager->addNoticeMessage(__('You have no project(s) in your account.'));
                }
                $params = ['wishlist_id' => $defaultWishlistId];
                $urlParams = $this->getRequest()->getParams();
                $resultRedirect->setPath('*/index', ['wishlist_id' => $defaultWishlistId]);
                if (isset($urlParams['action'])) {
                    $params = ['wishlist_id' => $defaultWishlistId, 'action' => 'create'];
                    $resultRedirect->setPath('*/index', ['wishlist_id' => $defaultWishlistId, 'action' => 'create']);
                }
                $this->getRequest()->setParams($params);
                return $resultRedirect;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('Requested project not found. %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Requested project not found.')
            );
        }
    }

}
