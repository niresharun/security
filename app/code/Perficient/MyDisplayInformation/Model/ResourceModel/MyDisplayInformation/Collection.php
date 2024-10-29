<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Model\ResourceModel\MyDisplayInformation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Perficient\MyDisplayInformation\Model\ResourceModel\MyDisplayInformation
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    public function _construct()
    {
        $this->_init(
            \Perficient\MyDisplayInformation\Model\MyDisplayInformation::class,
            \Perficient\MyDisplayInformation\Model\ResourceModel\MyDisplayInformation::class
        );
    }
}
