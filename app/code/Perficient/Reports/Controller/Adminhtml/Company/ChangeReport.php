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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent as PerficientLoggingEventResourceModel;
use Perficient\Reports\Model\PerficientLoggingEvent as PerficientLoggingEventModel;

/**
 * Company Change Log Report Action Class
 */
class ChangeReport extends Action
{
    /**
     * @const string
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
        protected FileFactory $_fileFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $this->_setActiveMenu(self::ADMIN_RESOURCE );
        $resultPage->getConfig()->getTitle()->prepend((__('Company Change Report')));

        return $resultPage;
    }

    /**
     * @return bool
     */
    public function _isAllowed() {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE );
    }

}
