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
 * Class Mode
 * @package Perficient\BlueshipShipping\Model\Config\Source
 */
class Mode implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'LTL', 'label' => __('LTL')]
        ];
    }
}
