<?php

namespace Wendover\MegaMenu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Menu extends AbstractDb
{
    public function _construct()
    {
        $this->_init("megamenu_mainmenu", "menu_id");
    }
}
