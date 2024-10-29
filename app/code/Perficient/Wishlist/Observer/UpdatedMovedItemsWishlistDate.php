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

namespace Perficient\Wishlist\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Stdlib\DateTime;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Model\ItemFactory;

class UpdatedMovedItemsWishlistDate implements ObserverInterface
{
    /**
     * DeleteWishlistObserver constructor.
     * @param ManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     * @param ItemFactory $itemFactory
     */
    public function __construct(
        private readonly ManagerInterface $messageManager,
        private readonly ActionFlag       $actionFlag,
        private readonly UrlInterface     $url,
        protected WishlistFactory         $wishlistFactory,
        protected DateTime\DateTime       $_date,
        protected ItemFactory             $itemFactory
    )
    {
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        try {
            $wishlistId = $observer->getEvent()->getRequest()->getParam('wishlist_id');
            $itemIds = $observer->getEvent()->getRequest()->getParam('selected', []);

            if ($wishlistId) {
                $wishlist = $this->wishlistFactory->create();
                $wishlist->load($wishlistId);
                if ($wishlist->getId()) {
                    $wishlist->setUpdatedAt($this->_date->gmtDate());
                    $wishlist->save();
                }
            }

            if (is_countable($itemIds) ? count($itemIds) : 0) {
                foreach ($itemIds as $id => $value) {
                    /* @var \Magento\Wishlist\Model\Item $item */
                    $item = $this->itemFactory->create();
                    $item->loadWithOptions($id);
                    if ($item->getWishlistId()) {
                        $wishlist = $this->wishlistFactory->create();
                        $wishlist->load($item->getWishlistId());
                        if ($wishlist->getId()) {
                            $wishlist->setUpdatedAt($this->_date->gmtDate());
                            $wishlist->save();
                            break;
                        }
                    }

                }
            }

        } catch (Exception $e) {
            $this->_fault($e->getMessage());
        }
    }

}



