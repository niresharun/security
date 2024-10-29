<?php
/**
 * Retrieve all cron jobs in array format
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\JobListing\Column\Code;

use Magento\Cron\Model\ConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 * @package Perficient\CronScheduler\Ui\Component\JobListing\Column\Code
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options = null;

    /**
     * Class constructor
     * @param ConfigInterface $cronConfig
     */
    public function __construct(public ConfigInterface $cronConfig)
    {
    }

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        if ($this->options === null) {
            $configJobs = $this->cronConfig->getJobs();
            foreach (array_values($configJobs) as $jobs) {
                foreach (array_keys($jobs) as $code) {
                    $options[] = $code;
                }
            }
        }

        sort($options);
        foreach ($options as $option) {
            $this->options[] = [
                "label" => $option, "value" => $option
            ];
        }
        return $this->options;
    }
}
