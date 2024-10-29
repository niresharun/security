<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DCKAP\Productimize\Block\Wishlist\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Framework\View\Element\Template;
use Magento\Wishlist\Helper\Data;
use Magento\Framework\Data\Helper\PostHelper;

/**
 * Class MoveToWishlist
 *
 * @api
 * @since 100.0.2
 */
class MoveToWishlist extends \Magento\Wishlist\Block\Cart\Item\Renderer\Actions\MoveToWishlist
{
    /**
     * @var Data
     */
    protected $wishlistHelper;

    protected $_postDataHelper;

    public function __construct(Template\Context $context, Data $wishlistHelper, \Magento\Framework\UrlInterface $urlBuilder, \Magento\Framework\Data\Helper\PostHelper $postDataHelper, array $data = [])
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_postDataHelper = $postDataHelper;
        parent::__construct($context, $wishlistHelper, $data);
    }

    public function getMoveFromCartParams()
    {
        $item = $this->getItem();
        $itemId = $item->getId();
        $pzCartProperties = $item->getBuyRequest()->getPzCartProperties();

        $url = $this->_getUrl('wishlist/index/fromcart');
        // $params = ['item' => $itemId];
        $params = ['item' => $itemId,
            'pz_cart_properties' => $pzCartProperties,
            //'params_addtocart' => $pzCartProperties,
            'product' => $item->getProduct()->getId()
        ];
        return $this->_postDataHelper->getPostData($url, $params);
    }

    protected function _getUrl($route, $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }
}
