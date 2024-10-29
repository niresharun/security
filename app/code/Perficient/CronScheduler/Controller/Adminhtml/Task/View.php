<?php
/**
 * Display complete status description of the cron job run
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Task;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Cron\Model\ScheduleFactory;
use Perficient\CronScheduler\Helper\Task as TaskHelper;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class View
 * @package Perficient\CronScheduler\Controller\Adminhtml\Task
 */
class View extends Action
{
    /**
     * @var string
     */
    protected $aclResource = "task_view";

    /**
     * Class constructor
     * @param Context $context
     * @param ScheduleFactory $scheduleFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        protected ScheduleFactory $scheduleFactory,
        protected TaskHelper $taskHelper,
        protected JsonFactory $jsonResultFactory,
        protected RequestInterface $request
    ) {
        parent::__construct($context);
    }

    /**
     * Action to view the details of a task
     */
    public function execute()
    {
        $scheduleId = $this->request->getParam('schedule_id');
        $schedule = $this->scheduleFactory->create()->load($scheduleId);
        $data = $schedule->getData();
        if (!empty($data)) {
            $data['messages'] = nl2br((string) $data['messages']);
            $data['status'] = $this->taskHelper->getStatusRenderer($data['status']);
        } else {
            $data['error'] = __("This task doesn't exist anymore");
        }

        /** @var Json $resultJson */
        $resultJson = $this->jsonResultFactory->create();
        return $resultJson->setData($data);
    }
}
