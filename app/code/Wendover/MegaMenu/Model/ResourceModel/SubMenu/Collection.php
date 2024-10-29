<?php

namespace Wendover\MegaMenu\Model\ResourceModel\SubMenu;

use Wendover\MegaMenu\Model\ResourceModel\SubMenu;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(\Wendover\MegaMenu\Model\SubMenu::class, SubMenu::class);
    }
}
