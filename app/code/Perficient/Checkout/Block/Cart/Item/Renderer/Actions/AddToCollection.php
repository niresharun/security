<?php
/**
 * This module is used to prepare add to collection configurable url on checkout
 *
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */

namespace Perficient\Checkout\Block\Cart\Item\Renderer\Actions;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Perficient\QuickShip\Helper\Data as QuickShipHelper;

/**
 * Class AddToCollection
 * @package Perficient\Checkout\Block\Cart\Item\Renderer\Actions
 */
class AddToCollection extends Generic
{
    /**
     * @param Template\Context $context
     * @codeCoverageIgnore
     */
    public function __construct(
        Template\Context                                 $context,
        private readonly \Magento\Checkout\Model\Session $checkoutSession,
        array                                            $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * This method is use to get the configurable add to collection url
     * @return string
     */
    public function getAddToCollectionConfigureUrl()
    {
        if ($this->checkoutSession->getQuote()->getData(QuickShipHelper::QUICK_SHIP_ATTRIBUTE)) {
            return;
        }
        $productIdsInCart = [];
        $cartItems = $this->checkoutSession->getQuote()->getAllVisibleItems();

        foreach ($cartItems as $cartItem) {
            $productId = $cartItem->getProduct()->getId();
            array_push($productIdsInCart, $productId);
        }

        $showCollectionFlag = false;
        $relatedProductIds = $this->getItem()->getProduct()->getRelatedProductIds();

        foreach ($relatedProductIds as $relatedProduct) {
            if (!in_array($relatedProduct, $productIdsInCart)) {
                $showCollectionFlag = true;
                break;
            }
        }

        $addToCollectionUrl = '';
        if (!empty($relatedProductIds) && $showCollectionFlag) {
            $addToCollectionUrl = $this->getUrl(
                'mycheckout/product/addtocollection',
                [
                    'id' => $this->getItem()->getId(),
                    'product' => $this->getItem()->getProduct()->getId(),
                    'cart_customizer' => true
                ]
            );
        }
        return $addToCollectionUrl;
    }
}
