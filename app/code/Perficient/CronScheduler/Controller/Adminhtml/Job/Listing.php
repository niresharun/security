<?php
/**
 * List complete available crons
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Job;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Listing
 * @package Perficient\CronScheduler\Controller\Adminhtml\Job
 */
class Listing extends Action
{
    /**
     * @var string
     */
    protected $aclResource = "job_listing";

    /**
     * Listing constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        protected PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Magento_Backend::system");
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Scheduler > Jobs Configuration'));
        $resultPage->addBreadcrumb(__('Cron Scheduler'), __('Cron Scheduler'));
        return $resultPage;
    }

    /**
     * Is the action allowed?
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Perficient_CronScheduler::'.$this->aclResource);
    }
}
