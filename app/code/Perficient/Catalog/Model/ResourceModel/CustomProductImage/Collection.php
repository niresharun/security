<?php
/**
 * Custom Product Image
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain <hiral.jain@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Model\ResourceModel\CustomProductImage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Perficient\Catalog\Model\ResourceModel\CustomProductImage
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Perficient\Catalog\Model\CustomProductImage::class,
            \Perficient\Catalog\Model\ResourceModel\CustomProductImage::class
        );
    }
}
