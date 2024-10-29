<?php
/**
 * Override block for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Block\Customer\Wishlist\Button;


class Collaborate extends \Magento\Wishlist\Block\Customer\Wishlist\Button
{
    /**
     * Collaborate constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context       $context,
        \Magento\Wishlist\Helper\Data                          $wishlistData,
        \Magento\Wishlist\Model\Config                         $wishlistConfig,
        private readonly \Magento\MultipleWishlist\Helper\Data $wishlistHelper,
        array                                                  $data = []
    )
    {
        parent::__construct($context, $wishlistData, $wishlistConfig, $data);
    }

    /**
     * Build block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->wishlistHelper->isMultipleEnabled() && $this->isWishlistCollaborable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Check whether current wishlist can be collaborated
     *
     * @return bool
     */
    protected function isWishlistCollaborable()
    {
        return !$this->wishlistHelper->isWishlistDefault($this->getWishlist());
    }

}
