<?php

namespace Perficient\Wishlist\Model;
class DeleteWishlist extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Define resource model
     *
     * @return void
     */

    public function _construct()
    {
        $this->_init(\Perficient\Wishlist\Model\ResourceModel\DeleteWishlist::class);
    }
}
