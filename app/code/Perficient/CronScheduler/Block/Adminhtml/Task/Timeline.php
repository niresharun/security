<?php
/**
 * set the timeline structure for the cron execution
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */

namespace Perficient\CronScheduler\Block\Adminhtml\Task;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Cron\Model\Schedule;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Perficient\CronScheduler\Helper\Task;
use Perficient\CronScheduler\Helper\Url;
use Perficient\CronScheduler\Model\ResourceModel\Task\CollectionFactory;

/**
 * Class Timeline
 * @package Perficient\CronScheduler\Block\Adminhtml\Task
 */
class Timeline extends Template
{
    /**
     * @var string
     */
    protected $magentoVersion = "";

    /**
     * Class constructor
     * @param Context $context
     * @param DateTime $datetime
     * @param CollectionFactory $collectionFactory
     * @param ProductMetadataInterface $productMetaData
     */
    public function __construct(
        Context $context,
        protected DateTime $datetime,
        protected Task $taskHelper,
        protected CollectionFactory $collectionFactory,
        ProductMetadataInterface $productMetaData,
        array $data = []
    ) {
        $explodedVersion = explode("-", (string) $productMetaData->getVersion()); // in case of 2.2.0-dev
        $this->magentoVersion = $explodedVersion[0];
        parent::__construct($context, $data);
        $this->setTemplate('task/timeline.phtml');
    }

    /**
     * Get the task view modal popup url
     * @return string
     */
    public function getViewUrl()
    {
        return $this->getUrl(Url::TASK_VIEW);
    }

    /**
     * Add the timezone offset to a datetime
     * @param string $datetime
     * @return string
     */
    private function addOffset($datetime)
    {
        if ($datetime == null) {
            return null;
        }
        if (version_compare($this->magentoVersion, "2.2.0") >= 0) {
            return $this->datetime->date(
                "Y-m-d H:i:s",
                strtotime($datetime) + $this->datetime->getGmtOffSet('seconds')
            );
        } else {
            return $datetime;
        }
    }

    /**
     * Get the system current date for javascript use
     * @return string
     */
    public function getCurrentJsDate()
    {
        $current = $this->datetime->date('U') + $this->datetime->getGmtOffSet('seconds');
        return "new Date(" . $this->datetime->date("Y,", $current) .
        ($this->datetime->date("m", $current) - 1) . $this->datetime->date(",d,H,i,s", $current) . ")";
    }

    /**
     * Get the data to construct the timeline
     * @return array
     */
    public function getTimelineData()
    {
        $data = [];
        $tasks = $this->collectionFactory->create();
        $tasks->getSelect()->order('job_code');

        foreach ($tasks as $task) {
            $start = $this->addOffset($task->getExecutedAt());
            $end = $this->addOffset($task->getFinishedAt());
            [$type, $inner] = $this->taskHelper->getStatusRenderer($task->getStatus());

            $messages = $task->getMessages();
            if (strlen((string) $messages) > 200) {
                $messages = substr((string) $messages, 0, 200) . "...";
            }
            $messages = nl2br((string) $messages);
            $tooltip = "<table class='task " . $type . "'>"
                    . "<tr><td colspan='2'>"
                    . $task->getJobCode()
                    . "</td></tr>"
                    . "<tr><td>"
                    . __('Id')
                    . "</td><td>"
                    . $task->getId() . "</td></tr>"
                    . "<tr><td>"
                    . __('Status')
                    . "</td><td>"
                    . "<span class='grid-severity-" . $type . "'>" . $inner . "</span>"
                    . "</td></tr>"
                    . "<tr><td>"
                    . __('Created at')
                    . "</td><td>"
                    . $this->addOffset($task->getCreatedAt())
                    . "</td></tr>"
                    . "<tr><td>"
                    . __('Scheduled at')
                    . "</td><td>"
                    . $this->addOffset($task->getScheduledAt())
                    . "</td></tr>"
                    . "<tr><td>"
                    . __('Executed at')
                    . "</td><td>"
                    . ($start != null ? $start : "")
                    . "</td></tr>"
                    . "<tr><td>"
                    . __('Finished at')
                    . "</td><td>"
                    . ($end != null ? $end : "")
                    . "</td></tr>";
            if ($messages != "") {
                $tooltip .= "<tr><td>"
                        . __('Messages')
                        . "</td><td>"
                        . $messages
                        . "</td></tr>";
            }
            $tooltip .= "</table>";

            if ($start == null) {
                $start = $this->addOffset($task->getScheduledAt());
                $end = $this->addOffset($task->getScheduledAt());
            }

            if ($task->getStatus() == Schedule::STATUS_RUNNING) {
                $end = $this->addOffset($this->datetime->date('Y-m-d H:i:s'));
            }

            if ($task->getStatus() == Schedule::STATUS_ERROR && $end == null) {
                $end = $start;
            }

            $data[] = [
                $task->getJobCode(),
                $task->getStatus(),
                $tooltip,
                "new Date(" . $this->datetime->date('Y,', $start) .
                ($this->datetime->date('m', $start) - 1) . $this->datetime->date(',d,H,i,s,0', $start) . ")",
                "new Date(" . $this->datetime->date('Y,', $end) .
                ($this->datetime->date('m', $end) - 1) . $this->datetime->date(',d,H,i,s,0', $end) . ")",
                $task->getScheduleId()
            ];
        }

        return $data;
    }
}
