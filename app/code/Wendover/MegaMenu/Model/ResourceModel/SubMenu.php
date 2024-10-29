<?php

namespace Wendover\MegaMenu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SubMenu extends AbstractDb
{
    public function _construct()
    {
        $this->_init("megamenu_submenu", "submenu_id");
    }
}
