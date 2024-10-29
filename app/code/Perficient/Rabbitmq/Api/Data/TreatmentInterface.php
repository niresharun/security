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
 * Interface TreatmentInterface
 * @package Perficient\Rabbitmq\Api\Data
 */
interface TreatmentInterface
{
    /**
     * @return mixed
     */
    public function getTreatmentId();

    /**
     * @param $value
     * @return mixed
     */
    public function setTreatmentId($value);

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
    public function getBaseCostTreatment();

    /**
     * @param $value
     * @return mixed
     */
    public function setBaseCostTreatment($value);

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
    public function getMinGlassSizeShort();

    /**
     * @param $value
     * @return mixed
     */
    public function setMinGlassSizeShort($value);

    /**
     * @return mixed
     */
    public function getMinGlassSizeLong();

    /**
     * @param $value
     * @return mixed
     */
    public function setMinGlassSizeLong($value);

    /**
     * @return mixed
     */
    public function getMaxGlassSizeShort();

    /**
     * @param $value
     * @return mixed
     */
    public function setMaxGlassSizeShort($value);

    /**
     * @return mixed
     */
    public function getMaxGlassSizeLong();

    /**
     * @param $value
     * @return mixed
     */
    public function setMaxGlassSizeLong($value);

    /**
     * @return mixed
     */
    public function getMinRabbetDepth();

    /**
     * @param $value
     * @return mixed
     */
    public function setMinRabbetDepth($value);

    /**
     * @return mixed
     */
    public function getRequiresTopMat();

    /**
     * @param $value
     * @return mixed
     */
    public function setRequiresTopMat($value);

    /**
     * @return mixed
     */
    public function getRequiresBottomMat();

    /**
     * @param $value
     * @return mixed
     */
    public function setRequiresBottomMat($value);

    /**
     * @return mixed
     */
    public function getRequiresLiner();

    /**
     * @param $value
     * @return mixed
     */
    public function setRequiresLiner($value);

    /**
     * @return mixed
     */
    public function getImageEdgeTreatment();

    /**
     * @param $value
     * @return mixed
     */
    public function setImageEdgeTreatment($value);

    /**
     * @return mixed
     */
    public function getNewTopMatSizeLeft();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewTopMatSizeLeft($value);

    /**
     * @return mixed
     */
    public function getNewTopMatSizeTop();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewTopMatSizeTop($value);

    /**
     * @return mixed
     */
    public function getNewTopMatSizeRight();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewTopMatSizeRight($value);

    /**
     * @return mixed
     */
    public function getNewTopMatSizeBottom();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewTopMatSizeBottom($value);

    /**
     * @return mixed
     */
    public function getNewBottomMatSizeLeft();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewBottomMatSizeLeft($value);

    /**
     * @return mixed
     */
    public function getNewBottomMatSizeTop();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewBottomMatSizeTop($value);

    /**
     * @return mixed
     */
    public function getNewBottomMatSizeRight();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewBottomMatSizeRight($value);

    /**
     * @return mixed
     */
    public function getNewBottomMatSizeBottom();

    /**
     * @param $value
     * @return mixed
     */
    public function setNewBottomMatSizeBottom($value);

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

    /**
     * @param $value
     * @return mixed
     */
    public function setLinerDepthCheck($value);

    /**
     * @return mixed
     */
    public function getLinerDepthCheck();

    /**
     * @return mixed
     */
     public function getTreatmentWeightPerSqFtUpToThreshold();

    /**
     * @param $value
     * @return mixed
     */
    public function setTreatmentWeightPerSqFtUpToThreshold($value);

    /**
     * @return mixed
     */
    public function getTreatmentWeightPerSqFtOverThreshold();

    /**
     * @param $value
     * @return mixed
     */
    public function setTreatmentWeightPerSqFtOverThreshold($value);
}
