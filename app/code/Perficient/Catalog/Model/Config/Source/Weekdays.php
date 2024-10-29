<?php
/**
 * This file is used to display the week days.
 *
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright  - 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Weekdays
 * @package Perficient\Catalog\Model\Config\Source
 */
class Weekdays implements OptionSourceInterface
{
    /**
     * @var array
     */
    private static $options;

    /**
     * Constants for week days.
     */
    const CRON_SUNDAY = '7';
    const CRON_MONDAY = '1';
    const CRON_TUESDAY = '2';
    const CRON_WEDNESDAY = '3';
    const CRON_THURSDAY = '4';
    const CRON_FRIDAY = '5';
    const CRON_SATURDAY = '6';

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
