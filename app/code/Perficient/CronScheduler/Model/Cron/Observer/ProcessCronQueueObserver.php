<?php
/**
 * Generate Jobs for cron defined in the Configuration
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Model\Cron\Observer;

use Magento\Cron\Model\ConfigInterface;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Console\Request;
use Magento\Framework\App\State;
use Magento\Framework\Lock\LockManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Process\PhpExecutableFinderFactory;
use Magento\Framework\Profiler\Driver\Standard\StatFactory;
use Magento\Framework\ShellInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Perficient\CronScheduler\Helper\Job;
use Psr\Log\LoggerInterface;

class ProcessCronQueueObserver extends \Magento\Cron\Observer\ProcessCronQueueObserver
{
    /**
     * ProcessCronQueueObserver constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ScheduleFactory $scheduleFactory
     * @param CacheInterface $cache
     * @param ConfigInterface $config
     * @param ScopeConfigInterface $scopeConfig
     * @param Request $request
     * @param ShellInterface $shell
     * @param DateTime $dateTime
     * @param PhpExecutableFinderFactory $phpExecutableFinderFactory
     * @param LoggerInterface $logger
     * @param State $state
     * @param StatFactory $statFactory
     * @param LockManagerInterface $lockManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScheduleFactory $scheduleFactory,
        CacheInterface $cache,
        ConfigInterface $config,
        ScopeConfigInterface $scopeConfig,
        Request $request,
        ShellInterface $shell,
        DateTime $dateTime,
        PhpExecutableFinderFactory $phpExecutableFinderFactory,
        LoggerInterface $logger,
        State $state,
        StatFactory $statFactory,
        LockManagerInterface $lockManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Cron\Model\DeadlockRetrierInterface $retrier,
        public Job $jobHelper
    ) {
        parent::__construct(
            $objectManager,
            $scheduleFactory,
            $cache,
            $config,
            $scopeConfig,
            $request,
            $shell,
            $dateTime,
            $phpExecutableFinderFactory,
            $logger,
            $state,
            $statFactory,
            $lockManager,
            $eventManager,
            $retrier
        );
    }

    /**
     * Generate jobs for config information
     *
     * @param   array $jobs
     * @param   array $exists
     * @param   string $groupId
     */
    protected function _generateJobs($jobs, $exists, $groupId)
    {
        $disabledCrons = $this->jobHelper->getDisableCrons();
        $newJobs = [];
        foreach ($jobs as $jobCode => $jobConfig) {
            if (!in_array($jobCode, $disabledCrons)) {
                $newJobs[$jobCode] = $jobConfig;
            }
        }
        return parent::_generateJobs($newJobs, $exists, $groupId);
    }
}
