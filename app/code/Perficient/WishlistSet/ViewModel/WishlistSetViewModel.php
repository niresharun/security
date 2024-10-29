<?php
declare(strict_types=1);

namespace Perficient\WishlistSet\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Helper\Data;

class WishlistSetViewModel implements ArgumentInterface
{
    public function __construct( private readonly Data $wishListHelper){}

    public function isAllow()
    {
        return $this->wishListHelper->isAllow();
    }

    public function getListUrl()
    {
        return $this->wishListHelper->getListUrl();
    }
}
