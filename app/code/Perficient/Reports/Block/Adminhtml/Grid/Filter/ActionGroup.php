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

namespace Perficient\Reports\Block\Adminhtml\Grid\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Logging\Model\Config;
use Perficient\Reports\Block\Adminhtml\Grid\Column\Renderer\ActionGroup as ActionGroupData;

class ActionGroup implements OptionSourceInterface
{
    /**
     * @param Config $config
     */
    public function __construct(protected ActionGroupData $actionGroup)
    {
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->actionGroup->getActionGroups();
    }
}
