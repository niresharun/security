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

/**
 * Interface PageInterface
 * @package Perficient\MyCatalog\Api\Data
 */
interface PageInterface
{
    /**
     * @return int|null
     */
    public function getPageId();

    /**
     * @param int $value
     * @return $this
     */
    public function setPageId($value);

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
    public function getPageTemplateId();

    /**
     * @param int $value
     * @return $this
     */
    public function setPageTemplateId($value);

    /**
     * @return string|null
     */
    public function getDropSpotConfig();

    /**
     * @param string $value
     * @return $this
     */
    public function setDropSpotConfig($value);

    /**
     * @return string|null
     */
    public function getPagePosition();

    /**
     * @param string $value
     * @return $this
     */
    public function setPagePosition($value);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

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
    public function getPageUuid();

    /**
     * @param string $value
     * @return $this
     */
    public function setPageUuid($value);
}
