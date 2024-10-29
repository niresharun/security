<?php
/**
 * Retrieve collection of the jobs
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Model\ResourceModel\Task;

/**
 * Class Collection
 * @package Perficient\CronScheduler\Model\ResourceModel\Task
 */
class Collection extends \Magento\Cron\Model\ResourceModel\Schedule\Collection
{
    /**
     * @var string
     */
    protected $_idFieldName = "schedule_id";

    /**
     * Sort by schedule descending
     * @return $this
     */
    public function sortByScheduledAtDesc()
    {
        $this->getSelect()->order('scheduled_at DESC');
        return $this;
    }
    
    /**
     * Get distinct job codes based on the tasks scheduled
     * @return \Perficient\CronScheduler\Model\ResourceModel\Task\Collection
     */
    public function getJobCodes()
    {
        $this->getSelect()->reset('columns')
                ->columns('DISTINCT(job_code) as job_code')
                ->order('job_code ASC');

        return $this;
    }

    /**
     * Get distinct status based on the tasks scheduled
     *
     * @return $this
     */
    public function getTaskStatuses()
    {
        $this->getSelect()->reset('columns')
                ->columns('DISTINCT(status) as status')
                ->order('status ASC');

        return $this;
    }
}
