<?php
/**
 * Extend ParadoxLabs Authnetcim to change eCheckType form WEB to PPD
 * for savings and checking
 * @category: Magento
 * @package: Perficient/ParadoxLabsAuthnetcim
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar
 * @project: Wendover
 * @keywords: Module Perficient_ParadoxLabsAuthnetcim
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Perficient_ParadoxLabsAuthnetcim',
    __DIR__
);
