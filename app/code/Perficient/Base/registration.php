<?php
/**
 * Base  module to add all system configuration.
 *
 * @category: PHP
 * @package: Perficient/Base
 * @copyright:
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Perficient_Base',
    __DIR__
);
