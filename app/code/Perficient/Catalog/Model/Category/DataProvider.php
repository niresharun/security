<?php

namespace Perficient\Catalog\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{

    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'additional_image'; // custom image field

        return $fields;
    }
}
