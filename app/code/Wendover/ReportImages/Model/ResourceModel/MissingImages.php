<?php

namespace Wendover\ReportImages\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MissingImages extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('product_images_missing_report', 'entity_id');
    }
}
