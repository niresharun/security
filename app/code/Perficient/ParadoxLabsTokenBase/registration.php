<?php
/**
 * Extend ParadoxLabs TokenBase for removing payment address save validations
 * @category: Magento
 * @package: Perficient/ParadoxLabsTokenBase
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_ParadoxLabsTokenBase
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Perficient_ParadoxLabsTokenBase',
    __DIR__
);
