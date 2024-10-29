<?php
/**
 * Run specific/selected cron through Admin panel
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
use Magento\Cron\Model\ConfigInterface;
use Perficient\CronScheduler\Helper\Url;
use Perficient\CronScheduler\Model\ManagerFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

class RunNow extends Action
{
    /**
     * @var string
     */
    protected $redirectUrl = Url::JOB_LISTING;

    /**
     * RunNow constructor.
     * @param Context $context
     * @param DateTime $dateTime
     * @param ConfigInterface $config
     * @param ManagerFactory $managerFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        protected DateTime $dateTime,
        protected ConfigInterface $config,
        protected ManagerFactory $managerFactory,
        protected RequestInterface $request
    ) {
        parent::__construct($context);
    }

    /**
     * Save cronjob
     */
    public function execute()
    {
        /** @var \Perficient\CronScheduler\Model\Manager $manager */
        $manager = $this->managerFactory->create();
        $jobCode = $this->request->getParam('code');

        if (!$jobCode) {
            $this->messageManager->addErrorMessage(__("Something went wrong when receiving the request"));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);
            return $resultRedirect;
        }

        try {
            $now = $this->dateTime->gmtTimestamp();
            $formattedNow = $manager->getFormattedDateTime($now);
            $schedule = $manager->createCronJob($jobCode, $formattedNow);
            $manager->dispatchCron($jobCode, $schedule);
            $this->messageManager->addSuccessMessage(__('The job "%1" executed successfully".', $jobCode));
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
