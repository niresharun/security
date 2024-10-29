<?php

namespace Wendover\MegaMenu\Model\ResourceModel\Menu;

use Wendover\MegaMenu\Model\ResourceModel\Menu;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(\Wendover\MegaMenu\Model\Menu::class, Menu::class);
    }

}
