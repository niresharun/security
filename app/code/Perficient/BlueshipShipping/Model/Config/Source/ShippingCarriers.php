<?php
/**
 * Blueship Shipping Config option source
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: Blueship Shipping
 */

namespace Perficient\BlueshipShipping\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ShippingCarriers
 * @package Perficient\BlueshipShipping\Model\Config\Source
 */
class ShippingCarriers implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'ODFL', 'label' => __('Old Dominion Freight Line')],
            ['value' => 'SAIA', 'label' => __('SAIA')],
            ['value' => 'FWRA', 'label' => __('Forward Air')],
            ['value' => 'SEFL', 'label' => __('Southeastern Freight Lines')],
            ['value' => 'CNWY', 'label' => __('XPO Logistics')],
            ['value' => 'EXLA', 'label' => __('Estes Express')],
            ['value' => 'SCTP', 'label' => __('Cargo Transportation Services')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $tmpOptions = $this->toOptionArray();
        $options = [];
        foreach ($tmpOptions as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
