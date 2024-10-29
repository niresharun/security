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

namespace Perficient\Reports\Block\Adminhtml\Details\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Render class of Source Name
 */
class Sourcename extends AbstractRenderer
{
    /**
     * Render the grid cell value
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        if (!$data) {
            return '';
        }
        $html = '<div class="source-data"><span class="source-name">' . $row->getSourceName() . '</span>';
        if ($row->getSourceId()) {
            $html .= ' <span class="source-id">#' . $row->getSourceId() . '</span>';
        }
        return $html;
    }
}
