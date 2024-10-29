<?php

namespace Perficient\MyDisplayInformation\Model;
use Magento\Framework\Model\AbstractModel;

class MyDisplayInformation extends AbstractModel
{
    public function _construct()
    {
        $this->_init(ResourceModel\MyDisplayInformation::class);
    }
}
