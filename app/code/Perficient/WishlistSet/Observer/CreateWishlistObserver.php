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

namespace Perficient\WishlistSet\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Action;
use Perficient\WishlistSet\Helper\Data;

class CreateWishlistObserver implements ObserverInterface
{
    /**
     * CreateWishlistObserver constructor.
     * @param ManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     */
    public function __construct(
        private readonly ManagerInterface $messageManager,
        private readonly ActionFlag       $actionFlag,
        private readonly UrlInterface     $url
    )
    {
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $name = $observer->getEvent()->getRequest()->getParam('name');

        if (strtolower((string)$name) == strtolower(Data::DEFAULT_WISHLIST_NAME)) {
            $this->messageManager->addErrorMessage(__('Wish list "%1" already exists.', $name));
            // Stop further processing if your condition is met
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
            // then in last redirect
            $observer->getControllerAction()->getResponse()->setRedirect(
                $this->url->getUrl("wishlist/index/index")
            );

            return $this;
        }
    }

}
