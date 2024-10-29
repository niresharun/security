<?php
/**
 * Config Source for price multiplier attribute
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: Config Source for price multiplier attribute
 */
declare(strict_types=1);
namespace Perficient\PriceMultiplier\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class MultiplierOptions extends AbstractSource
{
    protected $_options;

    /**
     * getAllOptions
     *
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '0', 'label' => __('0.00X')],
                ['value' => '1.00', 'label' => __('1.00X')],
                ['value' => '1.25', 'label' => __('1.25X')],
                ['value' => '1.50', 'label' => __('1.50X')],
                ['value' => '1.75', 'label' => __('1.75X')],
                ['value' => '2.00', 'label' => __('2.00X')],
                ['value' => '2.25', 'label' => __('2.25X')],
                ['value' => '2.50', 'label' => __('2.50X')],
                ['value' => '2.75', 'label' => __('2.75X')],
                ['value' => '3.00', 'label' => __('3.00X')],
                ['value' => '3.25', 'label' => __('3.25X')],
                ['value' => '3.50', 'label' => __('3.50X')],
                ['value' => '3.75', 'label' => __('3.75X')],
                ['value' => '4.00', 'label' => __('4.00X')],
            ];
        }
        return $this->_options;
    }

    final public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('0.00X')],
            ['value' => '1.00', 'label' => __('1.00X')],
            ['value' => '1.25', 'label' => __('1.25X')],
            ['value' => '1.50', 'label' => __('1.50X')],
            ['value' => '1.75', 'label' => __('1.75X')],
            ['value' => '2.00', 'label' => __('2.00X')],
            ['value' => '2.25', 'label' => __('2.25X')],
            ['value' => '2.50', 'label' => __('2.50X')],
            ['value' => '2.75', 'label' => __('2.75X')],
            ['value' => '3.00', 'label' => __('3.00X')],
            ['value' => '3.25', 'label' => __('3.25X')],
            ['value' => '3.50', 'label' => __('3.50X')],
            ['value' => '3.75', 'label' => __('3.75X')],
            ['value' => '4.00', 'label' => __('4.00X')],
        ];
    }
}
