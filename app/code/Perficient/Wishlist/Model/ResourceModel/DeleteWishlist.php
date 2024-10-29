<?php

namespace Perficient\Wishlist\Model\ResourceModel;
class DeleteWishlist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("perficient_wishlist_deletions", "deletion_event_id");
    }
}
