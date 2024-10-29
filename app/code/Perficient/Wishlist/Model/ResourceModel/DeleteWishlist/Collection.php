<?php

namespace Perficient\Wishlist\Model\ResourceModel\DeleteWishlist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init(\Perficient\Wishlist\Model\DeleteWishlist::class, \Perficient\Wishlist\Model\ResourceModel\DeleteWishlist::class);
    }
}
