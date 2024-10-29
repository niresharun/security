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

namespace Perficient\Wishlist\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;

class WishlistProvider extends \Magento\Wishlist\Controller\WishlistProvider
{
    /**
     * WishlistProvider constructor.
     * @param WishlistFactory $wishlistFactory
     * @param Session $customerSession
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        WishlistFactory  $wishlistFactory,
        Session          $customerSession,
        ManagerInterface $messageManager,
        RequestInterface $request
    )
    {
        parent::__construct($wishlistFactory, $customerSession, $messageManager, $request);
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getWishlist($wishlistId = null)
    {
        if ($this->wishlist) {
            return $this->wishlist;
        }
        try {
            $collaborationIdsArr = [];
            if (!$wishlistId) {
                $wishlistId = $this->request->getParam('wishlist_id');
            }
            $customerId = $this->customerSession->getCustomerId();
            $wishlist = $this->wishlistFactory->create();

            if (!$wishlistId && !$customerId) {
                return $wishlist;
            }

            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } elseif ($customerId) {
                $wishlist->loadByCustomerId($customerId, true);
            }
            $collaborationIds = $wishlist->getCollaborationIds();
            if ($collaborationIds) {
                $collaborationIdsArr = explode(",", (string)$collaborationIds);
            }
            $editCustomizerId = ($this->request->getParam('edit_id')) ? 1 : 0;

            if (!$wishlist->getId() || ($wishlist->getCustomerId() != $customerId && in_array($customerId, $collaborationIdsArr) == false && $editCustomizerId === 0)) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The requested Wish List doesn\'t exist.')
                );
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t create the Wish List right now.'));
            return false;
        }
        $this->wishlist = $wishlist;
        return $wishlist;
    }

}
