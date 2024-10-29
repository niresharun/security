<?php
namespace  Wendover\ReportImages\Model\ResourceModel\MissingImages;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Wendover\ReportImages\Model\MissingImages as Model;
use Wendover\ReportImages\Model\ResourceModel\MissingImages as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
