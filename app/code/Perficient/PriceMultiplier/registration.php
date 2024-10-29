<?php
/**
 * Custom Module for price multiplier
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: price multiplier
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Perficient_PriceMultiplier',
    __DIR__
);
