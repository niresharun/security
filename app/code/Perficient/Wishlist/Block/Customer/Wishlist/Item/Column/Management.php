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

namespace Perficient\Wishlist\Block\Customer\Wishlist\Item\Column;


class Management extends \Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column\Management
{
    /**
     * Management constructor.
     * @param \Magento\Framework\View\ConfigInterface|null $config
     * @param \Magento\Catalog\Model\Product\Image\UrlBuilder|null $urlBuilder
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context            $context,
        \Magento\Framework\App\Http\Context               $httpContext,
        private readonly \Perficient\Wishlist\Helper\Data $helper,
        array                                             $data = [],
        \Magento\Framework\View\ConfigInterface           $config = null,
        \Magento\Catalog\Model\Product\Image\UrlBuilder   $urlBuilder = null
    )
    {
        parent::__construct(
            $context,
            $httpContext,
            $data,
            $config,
            $urlBuilder
        );
    }

    /**
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection|mixed
     * @throws \Exception
     */
    public function getWishlists()
    {
        return $this->helper->getCombinedWishlist();
    }

}
