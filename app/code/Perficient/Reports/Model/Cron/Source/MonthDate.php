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
 * Cron Month Date Source Model
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
