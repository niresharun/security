<?php
/**
 * This module is used to create custom artwork catalogs.
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MyCatalogDeleteSearchResultsInterface
 * @package Perficient\MyCatalog\Api\Data
 */
interface MyCatalogDeleteSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     * @return \Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterface[]
     */
    public function getItems();

    /**
     * Set items
     * @param \Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
