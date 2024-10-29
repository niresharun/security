<?php
/**
 * This module is used to create custom artwork catalogs.
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey Pali <Kartikey.Pali@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface PageSearchResultsInterface
 * @package Perficient\MyCatalog\Api\Data
 */
interface PageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     * @return \Perficient\MyCatalog\Api\Data\PageInterface[]
     */
    public function getItems();

    /**
     * Set items
     * @param \Perficient\MyCatalog\Api\Data\PageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
