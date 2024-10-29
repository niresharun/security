<?php
/**
 * Custom Roles Permission Template
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_RolesPermission
 */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Perficient_RolesPermission',
    __DIR__
);
