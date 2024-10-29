<?php
/**
 * Perficient_Core
 *
 * @category: PHP
 * @copyright: Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey.Pali<kartikey.pali@perficient.com>
 * @project: Wendover
 * @keywords: Core Helper
 */
declare(strict_types=1);

namespace Perficient\Core\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package Perficient\Core\Helper
 */
class Data extends AbstractHelper
{

    /**
     * Get Config flag
     *
     * @param $path
     * @return bool
     */
    public function getConfigFlag($path)
    {
        return $this->scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
