<?php
/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Perficient_PaymentMethodAdditionalData',
    __DIR__
);
