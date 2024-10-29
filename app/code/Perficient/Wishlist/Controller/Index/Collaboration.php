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

namespace Perficient\Wishlist\Controller\Index;

use Magento\Wishlist\Controller\Index\Index as WishlistIndex;
use Magento\Framework\App\Action;
use Perficient\Wishlist\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Framework\Exception\NotFoundException;

class Collaboration extends WishlistIndex
{
    /**
     * Collaboration constructor.
     * @param Action\Context $context
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        Action\Context            $context,
        WishlistProviderInterface $wishlistProvider,
        private readonly Data     $helper
    )
    {
        parent::__construct($context, $wishlistProvider);
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
    {
        try {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            if (!$this->helper->getCustomer()) {
                $this->messageManager->addNoticeMessage(__('Please login to view the collaboration list.'));
                $resultRedirect->setPath('customer/account/login');
                return $resultRedirect;
            }

            if (!$this->helper->isMultipleEnabled()) {
                throw new NotFoundException(__('Page not found.'));
            } else {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
                if (!$wishlistId) {
                    $wishlists = $this->helper->getCustomerCollaborationWishlists();
                    if ($wishlists) {
                        foreach ($wishlists as $wishlist) {
                            $wishlistId = $wishlist->getId();
                            break;
                        }
                    }
                    if ($wishlistId) {
                        $params = ['wishlist_id' => $wishlistId];
                        $this->getRequest()->setParams($params);
                        $resultRedirect->setPath('wishlist/index/collaboration', ['wishlist_id' => $wishlistId]);
                        return $resultRedirect;
                    }
                    $this->messageManager->addNoticeMessage(__('You have no projects collaborated with you.'));
                } else {
                    /** @var \Magento\Framework\View\Result\Page resultPage */
                    $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                    return $resultPage;
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                __('Requested project not found: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Requested project not found.')
            );
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }

}
