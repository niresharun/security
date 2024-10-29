<?php

namespace Perficient\Catalog\Model\Config\Source;

/**
 * Class ListMode
 * @package Perficient\Catalog\Model\Config\Source
 */
class ListMode implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array $options
     */
    private static $options;

    const CRON_EACH_1MINUTES = '1M';
    const CRON_EACH_2MINUTES = '2M';
    const CRON_EACH_5MINUTES = '5M';
    const CRON_EACH_10MINUTES = '10M';
    const CRON_EACH_15MINUTES = '15M';
    const CRON_EACH_30MINUTES = '30';
    const CRON_HOURLY = '1H';
    const CRON_EACH_2HOURS = '2H';
    const CRON_EACH_4HOURS = '4H';
    const CRON_EACH_6HOURS = '6H';
    const CRON_EACH_8HOURS = '8H';
    const CRON_EACH_12HOURS = '12H';
    const CRON_DAILY = 'D';
    const CRON_WEEKLY = 'W';
    const CRON_MONTHLY = 'M';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!self::$options) {
            self::$options = [
                ['label' => __('Every 1 Minute'), 'value' => self::CRON_EACH_1MINUTES],
                ['label' => __('Every 2 Minutes'), 'value' => self::CRON_EACH_2MINUTES],
                ['label' => __('Every 5 Minutes'), 'value' => self::CRON_EACH_5MINUTES],
                ['label' => __('Every 10 Minutes'), 'value' => self::CRON_EACH_10MINUTES],
                ['label' => __('Every 15 Minutes'), 'value' => self::CRON_EACH_15MINUTES],
                ['label' => __('Every 30 Minutes'), 'value' => self::CRON_EACH_30MINUTES],
                ['label' => __('Every 1 Hour'), 'value' => self::CRON_HOURLY],
                ['label' => __('Every 2 Hours'), 'value' => self::CRON_EACH_2HOURS],
                ['label' => __('Every 4 Hours'), 'value' => self::CRON_EACH_4HOURS],
                ['label' => __('Every 6 Hours'), 'value' => self::CRON_EACH_6HOURS],
                ['label' => __('Every 8 Hours'), 'value' => self::CRON_EACH_8HOURS],
                ['label' => __('Every 12 Hours'), 'value' => self::CRON_EACH_12HOURS],
                ['label' => __('Daily'), 'value' => self::CRON_DAILY],
                ['label' => __('Weekly'), 'value' => self::CRON_WEEKLY],
                ['label' => __('Monthly'), 'value' => self::CRON_MONTHLY],
            ];
        }
        return self::$options;
    }
}
