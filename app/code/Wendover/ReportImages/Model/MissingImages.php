<?php

namespace Wendover\ReportImages\Model;

use Magento\Framework\Model\AbstractModel;
use Wendover\ReportImages\Model\ResourceModel\MissingImages as ResourceModel;

class MissingImages extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
