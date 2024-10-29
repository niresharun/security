<?php
/**
 * Business logic to retrieve complete tasks information
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Perficient\CronScheduler\Model\ResourceModel\Task\CollectionFactory;

/**
 * Class TaskProvider
 * @package Perficient\CronScheduler\Ui\DataProvider
 */
class TaskProvider extends AbstractDataProvider
{
    /**
     * @var \Perficient\CronScheduler\Model\ResourceModel\Task\Collection
     */
    protected $collection;

    /**
     * Class constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
