<?php
/**
 * Provide Details for the tasks
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Helper;

use Magento\Cron\Model\Schedule;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Task
 * @package Perficient\CronScheduler\Helper
 */
class Task extends AbstractHelper
{
    /**
     * Get the cron task status renderer information
     * @param string $status
     * @return array first item : css class / second item : label
     */
    public function getStatusRenderer($status)
    {
        $type = "";
        $inner = "";
        switch ($status) {
            case Schedule::STATUS_ERROR:
                $type = 'major';
                $inner = __("ERROR");
                break;
            case Schedule::STATUS_MISSED:
                $type = 'major';
                $inner = __("MISSED");
                break;
            case Schedule::STATUS_RUNNING:
                $type = 'running';
                $inner = __("RUNNING");
                break;
            case Schedule::STATUS_PENDING:
                $type = 'minor';
                $inner = __("PENDING");
                break;
            case Schedule::STATUS_SUCCESS:
                $type = 'notice';
                $inner = __("SUCCESS");
                break;
        }
        return [$type, $inner];
    }
}
