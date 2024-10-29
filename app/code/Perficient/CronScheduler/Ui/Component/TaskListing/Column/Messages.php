<?php
/**
 * Display Error message of cron failure
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\TaskListing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Messages
 * @package Perficient\CronScheduler\Ui\Component\TaskListing\Column
 */
class Messages extends Column
{
    /**
     * Prepare Data Source
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $messages = nl2br((string) $item[$this->getData('name')]);
                if (strlen($messages) > 200) {
                    $messages = substr($messages, 0, 200) . "...";
                }
                $item[$this->getData('name')] = $messages;
            }
        }

        return $dataSource;
    }
}
