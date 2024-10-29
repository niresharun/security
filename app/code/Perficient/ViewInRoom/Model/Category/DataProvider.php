<?php
/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Tahir Aziz <tahir.aziz@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_ViewInRoom
 */

declare(strict_types=1);

namespace Perficient\ViewInRoom\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{

    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'vir_background_img';

        return $fields;
    }
}
