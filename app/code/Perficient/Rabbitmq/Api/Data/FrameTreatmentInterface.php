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
 * Interface FrameTreatmentInterface
 * @package Perficient\Rabbitmq\Api\Data
 */
interface FrameTreatmentInterface
{
    /**
     * @return mixed
     */
    public function getFrameTreatmentId();

    /**
     * @param $value
     * @return mixed
     */
    public function setFrameTreatmentId($value);

    /**
     * @return mixed
     */
    public function getTreatmentSku();

    /**
     * @param $value
     * @return mixed
     */
    public function setTreatmentSku($value);

    /**
     * @return mixed
     */
    public function getFrameType();

    /**
     * @param $value
     * @return mixed
     */
    public function setFrameType($value);

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
