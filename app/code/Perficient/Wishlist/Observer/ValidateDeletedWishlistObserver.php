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

class ValidateDeletedWishlistObserver implements ObserverInterface
{
    const REST_API_LOG_PATH = '/var/log/RestApi.log';

    /**
     * DeleteWishlistObserver constructor.
     * @param ManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     */
    public function __construct(
        private readonly ManagerInterface                                 $messageManager,
        private readonly ActionFlag                                       $actionFlag,
        private readonly UrlInterface                                     $url,
        private readonly \Perficient\Wishlist\Model\DeleteWishlistFactory $deleteWishlist,
        protected WishlistFactory                                         $wishlistFactory
    )
    {
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $action = 'Deleted';

        try {
            $wishlistId = $observer->getEvent()->getRequest()->getParam('wishlist_id');
            if (!$wishlistId) {
                throw new Exception();
            } else {
                $wishlist = $this->wishlistFactory->create();
                $wishlist->load($wishlistId);
                if ($wishlist->getId()) {
                    $deleteWishlistFactory = $this->deleteWishlist->create();
                    $deleteWishlistFactory->load($wishlistId);
                    $deleteWishlistFactory->delete();
                }
            }
        } catch (Exception $e) {
            $this->_fault($e->getMessage());
        }
    }

    /**
     * Dispatches error log
     *
     * @param string $message
     *
     * @throws \Exception
     */
    private function _erroLog($message, $error = null)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . self::REST_API_LOG_PATH);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Log Data: ", ['Message' => $message . " - " . $error]);
    }

    /**
     * Dispatches fault
     *
     * @param string $message
     *
     * @throws \Exception
     */
    private function _fault($message, $error = null)
    {
        $this->_erroLog($message, $error);
        throw new \Exception($message);
    }
}
