<?php
/**
 * Retrieve all available statuses
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\JobListing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Perficient\CronScheduler\Helper\Job;

/**
 * Class Options
 * @package Perficient\CronScheduler\Ui\Component\TaskListing\Column\Status
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options = null;

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $options = [
                Job::STATUS_ENABLED => Job::STATUS_ENABLED,
                Job::STATUS_DISABLED => Job::STATUS_DISABLED
            ];
            foreach ($options as $key => $value) {
                $this->options[] = [
                    "label" => $key, "value" => $value
                ];
            }
        }
        return $this->options;
    }
}
