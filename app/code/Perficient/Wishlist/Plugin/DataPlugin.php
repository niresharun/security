<?php
/**
 * overide for collaboration
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

use Magento\Wishlist\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Helper\PostHelper;

class DataPlugin
{
    /**
     * DataPlugin constructor.
     */
    public function __construct(
        Context                                         $context,
        protected PostHelper                            $postHelper,
        protected \Magento\Framework\App\Request\Http   $request,
        protected \Magento\Framework\UrlInterface       $_urlBuilder
    )
    {
    }

    /**
     * @param Data $subject
     * @param $item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetUpdateParams(Data $subject, callable $proceed, $item): bool|string
    {
        $itemId = null;
        if ($item instanceof \Magento\Catalog\Model\Product) {
            $itemId = $item->getWishlistItemId();
            $productId = $item->getId();
        }
        if ($item instanceof \Magento\Wishlist\Model\Item) {
            $itemId = $item->getId();
            $productId = $item->getProduct()->getId();
        }

        $url = $this->_urlBuilder->getUrl('wishlist/index/updateItemOptions');
        $this->request->getParam('page_type');
        if ($itemId) {
            $params = ['id' => $itemId, 'product' => $productId, 'qty' => $item->getQty(), 'pz_cart_properties' => $item->getBuyRequest()->getData('pz_cart_properties')];
            $pageType = $this->request->getParam('page_type');
            if (isset($pageType) && $pageType == 'collaboration') {
                $params = ['id' => $itemId, 'product' => $productId, 'qty' => $item->getQty(), 'page_type' => 'collaboration', 'pz_cart_properties' => $item->getBuyRequest()->getData('pz_cart_properties')];
            }
            return $this->postHelper->getPostData($url, $params);
        }

        return false;
    }

}
