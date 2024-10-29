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
 * Interface BaseCostInterface
 * @package Perficient\Rabbitmq\Api\Data
 */
interface BaseCostInterface
{
    /**
     * @return mixed
     */
    public function getBaseCostId();

    /**
     * @param $value
     * @return mixed
     */
    public function setBaseCostId($value);

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
    public function getBaseCostTreatment();

    /**
     * @param $value
     * @return mixed
     */
    public function setBaseCostTreatment($value);

    /**
     * @return mixed
     */
    public function getGlassSizeShort();

    /**
     * @param $value
     * @return mixed
     */
    public function SetGlassSizeShort($value);

    /**
     * @return mixed
     */
    public function getGlassSizeLong();

    /**
     * @param $value
     * @return mixed
     */
    public function setGlassSizeLong($value);

    /**
     * @return mixed
     */
    public function getBaseCost();

    /**
     * @param $value
     * @return mixed
     */
    public function setBaseCost($value);

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
