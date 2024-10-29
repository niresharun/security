<?php
/**
 * Display all actions run now/schedule now etc.
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\JobListing\Column;

use Magento\Framework\Exception\NotFoundException;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 * @package Perficient\CronScheduler\Ui\Component\JobListing\Column
 */
class Actions extends Column
{
    const JOB_CODE = 'code';

    /**
     * @return array
     * @throws NotFoundException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                if (!isset($item[self::JOB_CODE])) {
                    throw new NotFoundException(
                        __('Missing Job Code: %1.', $item[self::JOB_CODE])
                    );
                }
                $item[$name]["run_now"] = [
                    "href" => $this->getContext()->getUrl(
                        "cronscheduler/job/runNow",
                        ['code' => $item[self::JOB_CODE]]
                    ),
                    "label"=>__("Run Now")
                ];
                $item[$name]["schedule_now"] = [
                    "href" => $this->getContext()->getUrl(
                        "cronscheduler/job/scheduleNow",
                        ['code' => $item[self::JOB_CODE]]
                    ),
                    "label"=>__("Schedule Now")
                ];
            }
        }
        return $dataSource;
    }
}
