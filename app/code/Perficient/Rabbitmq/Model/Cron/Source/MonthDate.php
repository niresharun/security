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
 * Class MonthDate
 * @package Perficient\Rabbitmq\Model\Cron\Source
 */
class MonthDate implements OptionSourceInterface
{
    /**
     * @var array
     */
    private static $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {

        if (!self::$options) {
            $i = 1;
            $options = [];
            while ($i <= 31) {
                $options[$i] = ['label' => __($i), 'value' => $i];
                $i++;
            }
            self::$options = $options;
        }
        return self::$options;
    }
}
