<?php
/**
 * Config Source for Discount type attribute
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: Config Source for Discount type attribute
 */

declare(strict_types=1);
namespace Perficient\PriceMultiplier\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class DiscountOptions extends AbstractSource
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
                ['value' => 'standard', 'label' => __('Before Your Discount')],
                ['value' => 'post-discounted', 'label' => __('After Your Discount')],
            ];
        }
        return $this->_options;
    }

    final public function toOptionArray(): array
    {
        return [
            ['value' => 'standard', 'label' => __('Before Your Discount')],
            ['value' => 'post-discounted', 'label' => __('After Your Discount')],
        ];
    }
}
