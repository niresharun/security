<?php
/**
 * Log Company Change Information
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

declare(strict_types=1);

namespace Perficient\Reports\Model\Cron\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Cron Frequency Source Model
 */
class Frequency implements OptionSourceInterface
{
    /**
     * @var array $options
     */
    private static $options;

    /**
     * @const string
     */
    const CRON_EACH_1MINUTES  = '1M';
    /**
     * @const string
     */
    const CRON_EACH_2MINUTES  = '2M';
    /**
     * @const string
     */
    const CRON_EACH_5MINUTES  = '5M';
    /**
     * @const string
     */
    const CRON_EACH_10MINUTES = '10M';
    /**
     * @const string
     */
    const CRON_EACH_15MINUTES = '15M';
    /**
     * @const string
     */
    const CRON_EACH_30MINUTES = '30';
    /**
     * @const string
     */
    const CRON_HOURLY         = '1H';
    /**
     * @const string
     */
    const CRON_EACH_2HOURS    = '2H';
    /**
     * @const string
     */
    const CRON_EACH_4HOURS    = '4H';
    /**
     * @const string
     */
    const CRON_EACH_6HOURS    = '6H';
    /**
     * @const string
     */
    const CRON_EACH_8HOURS    = '8H';
    /**
     * @const string
     */
    const CRON_EACH_12HOURS   = '12H';
    /**
     * @const string
     */
    const CRON_DAILY          = 'D';
    /**
     * @const string
     */
    const CRON_WEEKLY         = 'W';
    /**
     * @const string
     */
    const CRON_MONTHLY        = 'M';

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