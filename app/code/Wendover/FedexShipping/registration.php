<?php
/**
 * Blueship Shipping module
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: Blueship Shipping
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Wendover_FedexShipping',
    __DIR__
);
