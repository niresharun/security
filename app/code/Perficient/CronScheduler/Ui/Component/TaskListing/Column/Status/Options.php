<?php
/**
 * Display status in the Cron Grid
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\TaskListing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Perficient\CronScheduler\Model\ResourceModel\Task\CollectionFactory;

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
     * @var \Perficient\CronScheduler\Model\ResourceModel\Task\Collection
     */
    public $taskCollection = null;

    /**
     * Class constructor
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
        if ($this->options === null) {
            $this->options = [];
            $taskStatuses = $this->taskCollection->getTaskStatuses();
            foreach ($taskStatuses as $taskStatus) {
                $this->options[] = [
                    "label" => $taskStatus->getStatus(), "value" => $taskStatus->getStatus()
                ];
            }
        }
        return $this->options;
    }
}
