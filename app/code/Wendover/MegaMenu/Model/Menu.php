<?php

namespace Wendover\MegaMenu\Model;

use Magento\Framework\Model\AbstractModel;

class Menu extends AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceModel\Menu::class);
    }
}
