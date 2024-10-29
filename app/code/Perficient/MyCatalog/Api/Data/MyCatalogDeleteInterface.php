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

/**
 * Interface MyCatalogInterface
 * @package Perficient\MyCatalog\Api\Data
 */
interface MyCatalogDeleteInterface
{
    /**
     * @return int|null
     */
    public function getDeletionEventId();

    /**
     * @param int $value
     * @return $this
     */
    public function setDeletionEventId($value);

    /**
     * @return int|null
     */
    public function getCatalogId();

    /**
     * @param int $value
     * @return $this
     */
    public function setCatalogId($value);

    /**
     * @return int|null
     */
    public function getWishlistId();

    /**
     * @param int $value
     * @return $this
     */
    public function setWishlistId($value);

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * @return string|null
     */
    public function getAction();

    /**
     * @param string $value
     * @return $this
     */
    public function setAction($value);
}
