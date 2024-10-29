<?php
/**
 * Resolve ambiguous when filter company grid
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */

namespace Perficient\Company\Model\Company\Grid;

use Magento\Company\Model\ResourceModel\Company\Grid\Collection as ParentCollection;

/**
 * Company grid collection. Provides data for companies grid.
 */
class Collection extends ParentCollection
{
    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap('syspro_customer_id', 'main_table.syspro_customer_id');
        return $this;
    }
}
