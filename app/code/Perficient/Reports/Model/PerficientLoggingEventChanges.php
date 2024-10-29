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

namespace Perficient\Reports\Model;

use Magento\Framework\Model\AbstractModel;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges as PerficientLoggingEventChangesResourceModel;

/**
 * Model class for PerficientLoggingEventChanges
 */
class PerficientLoggingEventChanges extends AbstractModel
{
    /**
     * Resource Initialization
     */
    public function _construct()
    {
        $this->_init(PerficientLoggingEventChangesResourceModel::class);
    }
}