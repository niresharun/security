<?php

namespace Wendover\FindYourRep\Model;

class Rep extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Wendover\FindYourRep\Model\ResourceModel\Rep::class);
    }
}
