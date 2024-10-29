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
 * Interface MyCatalogInterface
 * @package Perficient\MyCatalog\Api\Data
 */
interface MyCatalogInterface
{
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
    public function getCustomerId();

    /**
     * @param int $value
     * @return $this
     */
    public function setCustomerId($value);

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
    public function getLogoImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setLogoImage($value);

    /**
     * @return string|null
     */
    public function getCatalogTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setCatalogTitle($value);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @return string|null
     */
    public function getPhoneNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setPhoneNumber($value);

    /**
     * @return string|null
     */
    public function getWebsiteUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setWebsiteUrl($value);

    /**
     * @return string|null
     */
    public function getCompanyName();

    /**
     * @param string $value
     * @return $this
     */
    public function setCompanyName($value);

    /**
     * @return string|null
     */
    public function getAdditionalInfo1();

    /**
     * @param string $value
     * @return $this
     */
    public function setAdditionalInfo1($value);

    /**
     * @return string|null
     */
    public function getAdditionalInfo2();

    /**
     * @param string $value
     * @return $this
     */
    public function setAdditionalInfo2($value);

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
    public function getPriceModifier();

    /**
     * @param string $value
     * @return $this
     */
    public function setPriceModifier($value);

    /**
     * @return string|null
     */
    public function getCatalogUuid();

    /**
     * @param string $value
     * @return $this
     */
    public function setCatalogUuid($value);
}
