<?php
/**
 * Display job code in the Cron list Grid
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\TaskListing\Column\Code;

use Magento\Framework\Data\OptionSourceInterface;
use Perficient\CronScheduler\Model\ResourceModel\Task\CollectionFactory;

/**
 * Class Options
 * @package Perficient\CronScheduler\Ui\Component\TaskListing\Column\Code
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options = null;

    /**
     * @var \Perficient\CronScheduler\Model\ResourceModel\Task\Collection
     */
    public $taskCollection = null;

    /**
     * Options constructor.
     * @param CollectionFactory $taskCollectionFactory
     */
    public function __construct(
        CollectionFactory $taskCollectionFactory
    ) {
        $this->taskCollection = $taskCollectionFactory->create();
    }

    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        
        if ($this->options === null) {
            $this->options = [];
            $jobCodes = $this->taskCollection->getJobCodes();
            foreach ($jobCodes as $jobCode) {
                $options[] = $jobCode->getJobCode();
            }
            sort($options);
            foreach ($options as $option) {
                $this->options[] = [
                    "label" => $option, "value" => $option
                ];
            }
        }
        return $this->options;
    }
}
