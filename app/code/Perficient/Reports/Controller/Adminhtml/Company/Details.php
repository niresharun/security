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

use Magento\Framework\App\RequestInterface as Request;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent as PerficientLoggingEventResourceModel;
use Perficient\Reports\Model\PerficientLoggingEvent as PerficientLoggingEventModel;

/**
 * Action Class for Change details
 */
class Details extends ChangeReport
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Perficient_Reports::perficient_company_change_report';

    /**
     * ChangeReport constructor.
     * @param Context $context
     * @param Registry $_coreRegistry
     * @param PageFactory $resultPageFactory
     * @param FileFactory $_fileFactory
     */
    public function __construct(
        Context $context,
        protected Registry $_coreRegistry,
        protected PageFactory $resultPageFactory,
        protected PerficientLoggingEventResourceModel $perficientLoggingEventResourceModel,
        protected PerficientLoggingEventModel $perficientLoggingEventModel,
        protected FileFactory $_fileFactory,
        private readonly Request $request,
    ) {
        parent::__construct($context, $_coreRegistry, $resultPageFactory, $perficientLoggingEventResourceModel, $perficientLoggingEventModel, $_fileFactory);
    }

    /**
     * View logging details
     *
     * @return void
     */
    public function execute()
    {
        $logId = $this->request->getParam('log_id');
        $this->perficientLoggingEventResourceModel->load($this->perficientLoggingEventModel, $logId);

        if (!$this->perficientLoggingEventModel->getId()) {
            $this->_redirect('customreports/*/changereport');
            return;
        }
        $this->_coreRegistry->register('current_change', $this->perficientLoggingEventModel);

        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__("Change Log Entry #%1", $logId));
        $this->_view->renderLayout();
    }
}
