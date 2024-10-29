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

namespace Perficient\Catalog\Api\Data;
/**
 * Interface CustomProductImageInterface
 * @package Perficient\Catalog\Api\Data
 */
interface CustomProductImageInterface
{
    /**
     * @return int
     */
    public function getCustomProductImageId();

    /**
     * @param int $value
     * @return $this
     */
    public function setCustomProductImageId($value);

    /**
     * @return string
     */
    public function getCustomProductImageSku();

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomProductImageSku($value);

    /**
     * @return string
     */
    public function getCustomProductImageImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomProductImageImage($value);

    /**
     * @return string
     */
    public function getCustomProductImageType();

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomProductImageType($value);

}
