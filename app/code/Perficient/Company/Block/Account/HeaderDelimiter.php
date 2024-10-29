<?php
/**
 * Wendover Custom attributes
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright � 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Block\Account;

use Magento\Customer\Block\Account\SortLinkInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class for delimiter.
 */
class HeaderDelimiter extends Template implements SortLinkInterface
{

    /**
     * @const string
     */
    const HEADER_LABEL = 'label';

    public function getSortOrder(): mixed
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function getLabel(): mixed
    {
        return $this->getData(self::HEADER_LABEL);
    }
}
