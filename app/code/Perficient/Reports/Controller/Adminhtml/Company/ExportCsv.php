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

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportCsv
 * @package Perficient\Reports\Controller\Adminhtml\Company
 */
class ExportCsv extends ChangeReport
{
    /**
     * @var string
     */
    protected $fileNamePrefix = 'log';

    /**
     * @return ResponseInterface | null
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = $this->generateFileName();

        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('changereports.grid', 'grid.export');

        if(!$exportBlock) {
            $this->messageManager->addErrorMessage(__('Unable to export to CSV'));
            $this->_redirect('customreports/company/changereport');
            return null;
        }

        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile($fileName),
            DirectoryList::VAR_DIR
        );
    }

    /**
     * @return string
     */
    private function generateFileName() {
        return $this->fileNamePrefix . '_' . time() . '.csv';
    }
}