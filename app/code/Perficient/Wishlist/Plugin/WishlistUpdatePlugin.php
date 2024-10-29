<?php
/**
 * Plugin for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Plugin;

use Magento\Wishlist\Controller\Index\Update;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class WishlistUpdatePlugin
{
    /**
     * WishlistUpdatePlugin constructor.
     * @param ResultFactory $resultFactory
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        private readonly ResultFactory             $resultFactory,
        private readonly WishlistProviderInterface $wishlistProvider
    )
    {
    }

    /**
     * @param Update $subject
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function afterExecute(Update $subject, $result)
    {
        $post = $subject->getRequest()->getPostValue();
        $pageType = $subject->getRequest()->getParam('page_type');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $wishlist = $this->wishlistProvider->getWishlist();
        if (isset($post['save_and_collaborate'])) {
            $resultRedirect->setPath('*/*/collaborate', ['wishlist_id' => $wishlist->getId()]);
            return $resultRedirect;
        }

        if (isset($pageType) && $pageType == 'collaboration') {
            $resultRedirect->setPath('*/*/collaboration', ['wishlist_id' => $wishlist->getId()]);
            return $resultRedirect;
        }
        return $result;
    }

}
