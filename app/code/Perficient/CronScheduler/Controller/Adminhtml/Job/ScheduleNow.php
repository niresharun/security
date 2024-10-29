<?php
/**
 * Schedule specific/selected Job
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Job;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Cron\Model\ScheduleFactory;
use Perficient\CronScheduler\Helper\Url;
use Perficient\CronScheduler\Model\ManagerFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class ScheduleNow
 * @package Perficient\CronScheduler\Controller\Adminhtml\Job
 */
class ScheduleNow extends Action
{
    /**
     * @var string
     */
    protected $redirectUrl = Url::JOB_LISTING;

    /**
     * ScheduleNow constructor.
     * @param DateTime $dateTime
     * @param ScheduleFactory $scheduleFactory
     * @param ManagerFactory $managerFactory
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        protected DateTime $dateTime,
        protected ScheduleFactory $scheduleFactory,
        protected ManagerFactory $managerFactory,
        Context $context,
        protected RequestInterface $request
    ) {
        parent::__construct($context);
    }

    /**
     * Save cronjob
     */
    public function execute()
    {
        $manager = $this->managerFactory->create();
        $jobCode = $this->request->getParam('code');

        if (!$jobCode) {
            $this->messageManager->addErrorMessage(__("Something went wrong when recieving the request"));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);
            return $resultRedirect;
        }
        try {
            $now = $this->dateTime->gmtTimestamp();
            $time = $manager->getFormattedDateTime($now);
            $schedule = $manager->generateSchedule($jobCode, $time);
            $schedule->save();
            $this->messageManager->addSuccessMessage(__('The job "%1" scheduled successfully".', $jobCode));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);
            return $resultRedirect;
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->redirectUrl);
        return $resultRedirect;
    }
}
