<?php
/**
 * This file is used to display the month days.
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
 * Class MonthDate
 * @package Perficient\Catalog\Model\Config\Source
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
