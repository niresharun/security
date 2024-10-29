<?php
/**
 * Hide add to cart button and product price from guest user.
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_CatalogPermissions
 */
\Magento\Framework\Component\ComponentRegistrar::register(
        \Magento\Framework\Component\ComponentRegistrar::MODULE,
        'Perficient_CatalogPermissions',
        __DIR__
    );