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

namespace Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Perficient\Reports\Model\PerficientLoggingEventChanges as PerficientLoggingEventChangesModel ;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges as PerficientLoggingEventChangesResourceModel;

/**
 * Collection Class of PerficientLoggingEventChanges
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Construct class of PerficientLoggingEventChanges
     */
    public function _construct()
    {
        $this->_init(PerficientLoggingEventChangesModel::class, PerficientLoggingEventChangesResourceModel::class);
    }
}
