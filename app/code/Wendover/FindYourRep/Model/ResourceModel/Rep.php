<?php

namespace Wendover\FindYourRep\Model\ResourceModel;

class Rep extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('find_your_rep_main', 'id');
    }
}
