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

namespace Perficient\Wishlist\Controller\Collaborated;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\Shared\WishlistProvider;
use Magento\Customer\Model\Session;
use Magento\Wishlist\Helper\Data;

class Index extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * Index constructor.
     * @param WishlistProvider $wishlistProvider
     * @param Context $context
     * @param Session $customerSession
     * @param Data $helper
     */
    public function __construct(
        Context                    $context,
        protected WishlistProvider $wishlistProvider,
        protected Session          $customerSession,
        private readonly Data      $helper
    )
    {
        parent::__construct($context);
    }

    /**
     * Shared wishlist view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $wishlist = $this->wishlistProvider->getWishlist();
        $customerId = $this->customerSession->getCustomerId();
        if ($wishlist && $wishlist->getCustomerId() && $wishlist->getCustomerId() == $customerId) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl(
                $this->helper->getListUrl($wishlist->getId())
            );
            return $resultRedirect;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }

}
