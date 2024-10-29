<?php
/**
 * Perform all operation on the specific cron job
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Model;

use Magento\Cron\Model\Schedule;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Cron\Model\ConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Perficient\CronScheduler\Model\Cron\InstanceFactory as CronInstanceFactory;

class Manager
{
    /**
     * Manager constructor.
     * @param ScheduleFactory $scheduleFactory
     * @param ConfigInterface $config
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected ScheduleFactory $scheduleFactory,
        protected CronInstanceFactory $cronInstanceFactory,
        protected ConfigInterface $config,
        protected DateTime $dateTime,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Generate schedule
     *
     * @param $jobCode
     * @param $time
     * @return mixed
     */
    public function generateSchedule($jobCode, $time)
    {
        $filteredTime = $this->filterTimeInput($time);

        $now = $this->dateTime->gmtTimestamp();
        $formattedNow = $this->getFormattedDateTime($now);

        $schedule = $this->scheduleFactory->create()
            ->setJobCode($jobCode)
            ->setStatus(Schedule::STATUS_PENDING)
            ->setCreatedAt($formattedNow)
            ->setScheduledAt($filteredTime);

        return $schedule;
    }

    /**
     * Format a timestamp using IntlDateFormatter
     */
    public function getFormattedDateTime($timestamp, $format = 'Y-m-d H:i')
    {
        return $this->dateTime->gmtDate($format, $timestamp);
    }

    /**
     * @param $jobCode
     * @param $time
     * @return Schedule
     * @throws \Exception
     */
    public function createCronJob($jobCode, $time)
    {
        $schedule = $this->generateSchedule($jobCode, $time);
        $schedule->save();

        return $schedule;
    }

    /**
     * Generates filtered time input from user to formatted time (YYYY-MM-DD)
     *
     * @return string
     */
    protected function filterTimeInput(mixed $time)
    {
        $matches = [];
        preg_match('/(\d+-\d+-\d+) (\d+:\d+)/', (string) $time, $matches);
        $time = $matches[1] . " " . $matches[2];

        return $this->getFormattedDateTime($time);
    }

    /**
     * @param $jobCode
     * @param null $schedule
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Throwable
     */
    public function dispatchCron($jobCode, $schedule = null)
    {
        $groups = $this->config->getJobs();
        $groupId = $this->getGroupId($jobCode, $groups);
        $jobConfig = $groups[$groupId][$jobCode];

        /* We need to trick the method into thinking it should run now so we
         * set the scheduled and current time to be equal to one another
         */
        try {
            $this->runJob($jobConfig, $schedule);
            $schedule->save($schedule);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $jobConfig
     * @param $schedule
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Throwable
     */
    public function runJob($jobConfig, $schedule)
    {
        $jobCode = $schedule->getJobCode();

        if (!isset($jobConfig['instance'], $jobConfig['method'])) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            throw new \Exception('No callbacks found');
        }

        // dynamically create cron instances
        $model = $this->cronInstanceFactory->create($jobConfig['instance']);
        $callback = [$model, $jobConfig['method']];
        if (!is_callable($callback)) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            throw new \Exception(
                sprintf(
                    'Invalid callback: %s::%s can\'t be called',
                    $jobConfig['instance'],
                    $jobConfig['method']
                )
            );
        }
        $now = $this->dateTime->gmtTimestamp();
        $formattedNow = $this->getFormattedDateTime($now);
        $schedule->setExecutedAt($formattedNow)->save();

        try {
            $this->logger->info(sprintf('Cron Job %s is run', $jobCode));
            call_user_func_array($callback, [$schedule]);
        } catch (\Throwable $e) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            $this->logger->error(sprintf(
                'Cron Job %s has an error: %s.',
                $jobCode,
                $e->getMessage()
            ));
            if (!$e instanceof \Exception) {
                $e = new \RuntimeException(
                    'Error when running a cron job',
                    0,
                    $e
                );
            }
            throw $e;
        }

        $now = $this->dateTime->gmtTimestamp();
        $formattedNow = $this->getFormattedDateTime($now);
        $schedule->setStatus(Schedule::STATUS_SUCCESS)->setFinishedAt($formattedNow);
        $this->logger->info(sprintf(
            'Cron Job %s is successfully finished',
            $jobCode
        ));
    }

    public function getGroupId($jobCode, $groups = null)
    {
        if ($groups === null) {
            $groups = $this->config->getJobs();
        }

        foreach ($groups as $groupId => $crons) {
            if (isset($crons[$jobCode])) {
                return $groupId;
            }
        }
        return false;
    }

    public function getCronJobs()
    {
        return $this->config->getJobs();
    }
}
