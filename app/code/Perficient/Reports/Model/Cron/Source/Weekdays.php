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
 * Cron Weekdays source model
 */
class Weekdays implements OptionSourceInterface
{
    /**
     * @var array
     */
    private static $options;

    /**
     * @const string
     */
    const CRON_SUNDAY   = '7';
    /**
     * @const string
     */
    const CRON_MONDAY   = '1';
    /**
     * @const string
     */
    const CRON_TUESDAY  = '2';
    /**
     * @const string
     */
    const CRON_WEDNESDAY = '3';
    /**
     * @const string
     */
    const CRON_THURSDAY  = '4';
    /**
     * @const string
     */
    const CRON_FRIDAY    = '5';
    /**
     * @const string
     */
    const CRON_SATURDAY  = '6';

    /**
     * @return array
     */
    public function toOptionArray()
    {

        if (!self::$options) {
            self::$options = [
                ['label' => __('Sunday'), 'value' => self::CRON_SUNDAY],
                ['label' => __('Monday'), 'value' => self::CRON_MONDAY],
                ['label' => __('Tuesday'), 'value' => self::CRON_TUESDAY],
                ['label' => __('Wednesday'), 'value' => self::CRON_WEDNESDAY],
                ['label' => __('Thursday'), 'value' => self::CRON_THURSDAY],
                ['label' => __('Friday'), 'value' => self::CRON_FRIDAY],
                ['label' => __('Saturday'), 'value' => self::CRON_SATURDAY]
            ];
        }
        return self::$options;
    }
}