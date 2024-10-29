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

namespace Perficient\Reports\Controller\Adminhtml\Company;

/**
 * Ajax Report Render Class
 */
class Grid extends ChangeReport
{
    /**
     * @const string
     */
    const ADMIN_RESOURCE = 'Perficient_Reports::perficient_company_change_report';

    /**
     * Execute function of gid action
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}