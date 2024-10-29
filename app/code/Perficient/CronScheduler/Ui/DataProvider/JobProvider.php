<?php
/**
 * Business logic to retrieve complete cron information
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Perficient\CronScheduler\Helper\Job;

/**
 * Class JobProvider
 * @package Perficient\CronScheduler\Ui\DataProvider
 */
class JobProvider extends AbstractDataProvider
{
    /**
     * @var integer
     */
    protected $size = 20;

    /**
     * @var integer
     */
    protected $offset = 1;

    /**
     * @var array
     */
    protected $likeFilters = [];

    /**
     * @var array
     */
    protected $rangeFilters = [];

    /**
     * @var string
     */
    protected $sortField = 'code';

    /**
     * @var string
     */
    protected $sortDir = 'asc';

    /**
     * Class constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReadFactory $directoryRead
     * @param DirectoryList $directoryList
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        private readonly ReadFactory $directoryRead,
        private readonly DirectoryList $directoryList,
        public Job $jobHelper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Set the limit of the collection
     * @param $offset
     * @param $size
     */
    public function setLimit($offset, $size)
    {
        $this->size = $size;
        $this->offset = $offset;
    }

    /**
     * Get the collection
     * @return array
     */
    public function getData()
    {
        $data = array_values($this->jobHelper->getJobData());

        // sorting
        $sortField = $this->sortField;
        $sortDir = $this->sortDir;

        usort($data, function ($a, $b) use ($sortField, $sortDir) {
            if ($sortDir == "asc") {
                return $a[$sortField] <=> $b[$sortField]; // Return -1, 0, or 1
            } else {
                return $b[$sortField] <=> $a[$sortField]; // Return -1, 0, or 1
            }
        });

        // filters
        if (!empty($this->likeFilters)) {
            foreach ($this->likeFilters as $column => $value) {
                $data = array_filter($data, fn($item) => stripos((string) $item[$column], (string) $value) !== false);
            }
        }
        $totalRecords = count($data);

        return [
            'totalRecords' => $totalRecords,
            'items' => $data,
        ];
    }

    /**
     * Add filters to the collection
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getConditionType() == "like") {
            $this->likeFilters[$filter->getField()] = substr((string) $filter->getValue(), 1, -1);
        } elseif ($filter->getConditionType() == "eq") {
            $this->likeFilters[$filter->getField()] = $filter->getValue();
        } elseif ($filter->getConditionType() == "gteq") {
            $this->rangeFilters[$filter->getField()]['from'] = $filter->getValue();
        } elseif ($filter->getConditionType() == "lteq") {
            $this->rangeFilters[$filter->getField()]['to'] = $filter->getValue();
        }
    }

    /**
     * Set the order of the collection
     * @param $field
     * @param $direction
     */
    public function addOrder($field, $direction)
    {
        $this->sortField = $field;
        $this->sortDir = strtolower((string) $direction);
    }
}
