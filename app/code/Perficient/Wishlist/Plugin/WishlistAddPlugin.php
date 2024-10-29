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

use Magento\Wishlist\Controller\Index\Add;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class WishlistAddPlugin
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * WishlistAddPlugin constructor.
     * @param WishlistProviderInterface $wishlistProvider
     * @param Context $context
     */
    public function __construct(
        private readonly WishlistProviderInterface $wishlistProvider,
        Context                                    $context
    )
    {
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * @param Add $subject
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function afterExecute(Add $subject, $result)
    {
        $pageTypeParams = $subject->getRequest()->getParam('page_type');
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }
        if (!empty($pageTypeParams) && $pageTypeParams == 'collaboration') {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('wishlist/index/collaboration', ['wishlist_id' => $wishlist->getId()]);
            return $resultRedirect;

        }
        return $result;
    }

}
