<?php
/**
 * This module is used to send request to Rabbitmq and display returning results
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright  - 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Swapnil Kene <swapnil.kene@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model\Cron\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Weekdays
 * @package Perficient\Rabbitmq\Model\Cron\Source
 */
class Weekdays implements OptionSourceInterface
{
    /**
     * @var array
     */
    private static $options;

    const CRON_SUNDAY   = '7';
    const CRON_MONDAY   = '1';
    const CRON_TUESDAY  = '2';
    const CRON_WEDNESDAY = '3';
    const CRON_THURSDAY  = '4';
    const CRON_FRIDAY    = '5';
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
