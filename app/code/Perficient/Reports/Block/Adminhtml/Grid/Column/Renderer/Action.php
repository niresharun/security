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

namespace Perficient\Reports\Block\Adminhtml\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Action extends AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param DataObject $row
     * @return string
     */

    const ACTION_LIST = ['update' => 'Update'];
    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if(array_key_exists($row->getData($this->getColumn()->getIndex()), self::ACTION_LIST)) {
            $value = self::ACTION_LIST[$row->getData($this->getColumn()->getIndex())];
        }
        return $value;
    }
}
