<?php
/**
 * Configure GDPR
 * @category: Magento
 * @package: Perficient/Gdpr
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde<trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Gdpr
 */
\Magento\Framework\Component\ComponentRegistrar::register(
        \Magento\Framework\Component\ComponentRegistrar::MODULE,
        'Perficient_Gdpr',
        __DIR__
    );