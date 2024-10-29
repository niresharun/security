<?php
/**
 * Log Company Change Information
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

declare(strict_types=1);

namespace Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perficient\Reports\Model\PerficientLoggingEvent as PerficientLoggingEventModel ;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent as PerficientLoggingEventResourceModel;

/**
 * Collection class of PerficientLoggingEvent
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     *
     */
    public function _construct()
    {
        $this->_init(PerficientLoggingEventModel::class, PerficientLoggingEventResourceModel::class);
    }
}
