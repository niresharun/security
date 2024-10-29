<?php
/**
 * Display Timeline for the cron job
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Task;

use Perficient\CronScheduler\Controller\Adminhtml\Task;

/**
 * Class Timeline
 * @package Perficient\CronScheduler\Controller\Adminhtml\Task
 */
class Timeline extends Task
{
    /**
     * @var string
     */
    protected $aclResource = "task_timeline";
    
    /**
     * Action to display the tasks timeline
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Magento_Backend::system");
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Scheduler > Timeline'));
        $resultPage->addBreadcrumb(__('CronScheduler'), __('CronScheduler'));
        return $resultPage;
    }
}
