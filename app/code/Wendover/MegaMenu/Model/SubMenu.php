<?php

namespace Wendover\MegaMenu\Model;

use Magento\Framework\Model\AbstractModel;

class SubMenu extends AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceModel\SubMenu::class);
    }
}
