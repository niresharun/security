<?php

namespace Wendover\FindYourRep\Model\ResourceModel\Rep;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Wendover\FindYourRep\Model\Rep::class,
            \Wendover\FindYourRep\Model\ResourceModel\Rep::class
        );
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
