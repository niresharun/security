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

namespace Perficient\Reports\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container as GridContainer;

/**
 * General Logging container
 *
 * @api
 * @since 100.0.2
 */
/**
 * Report Container Block Class
 */
class Container extends GridContainer
{
    /**
     * Remove add button
     * Set block group and controller
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Perficient_Reports';
        $this->_controller = 'adminhtml_container';

        parent::_construct();
        $this->buttonList->remove('add');
    }

    /**
     * Header text getter
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __($this->getData('header_text'));
    }
}