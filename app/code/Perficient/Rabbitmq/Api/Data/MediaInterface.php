<?php
/**
 * Custom Table Data Management
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Api\Data;

/**
 * Interface MediaInterface
 * @package Perficient\Rabbitmq\Api\Data
 */
interface MediaInterface
{
    /**
     * @return mixed
     */
    public function getMediaId();

    /**
     * @param $value
     * @return mixed
     */
    public function setMediaId($value);

    /**
     * @return mixed
     */
    public function getSku();

    /**
     * @param $value
     * @return mixed
     */
    public function setSku($value);

    /**
     * @return mixed
     */
    public function getBaseCostMedia();

    /**
     * @param $value
     * @return mixed
     */
    public function setBaseCostMedia($value);

    /**
     * @return mixed
     */
    public function getDisplayName();

    /**
     * @param $value
     * @return mixed
     */
    public function setDisplayName($value);

    /**
     * @return mixed
     */
    public function getMinImageSizeShort();

    /**
     * @param $value
     * @return mixed
     */
    public function setMinImageSizeShort($value);

    /**
     * @return mixed
     */
    public function getMinImageSizeLong();

    /**
     * @param $value
     * @return mixed
     */
    public function setMinImageSizeLong($value);

    /**
     * @return mixed
     */
    public function getMaxImageSizeShort();

    /**
     * @param $value
     * @return mixed
     */
    public function setMaxImageSizeShort($value);

    /**
     * @return mixed
     */
    public function getMaxImageSizeLong();

    /**
     * @param $value
     * @return mixed
     */
    public function setMaxImageSizeLong($value);

    /**
     * @param $value
     * @return mixed
     */
    public function setUpdatedAt($value);

    /**
     * @return mixed
     */
    public function getUpdatedAt();

    /**
     * @param $value
     * @return mixed
     */
    public function setStatus($value);

    /**
     * @return mixed
     */
    public function getStatus();


}
