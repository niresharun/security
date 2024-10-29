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

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Perficient\Rabbitmq\Api\Data\TreatmentInterface;
use Perficient\Rabbitmq\Model\ResourceModel\Treatment as TreatmentResource;

/**
 * Class Treatment
 * @package Perficient\Rabbitmq\Model
 */
class Treatment extends AbstractModel implements TreatmentInterface
{
    /**
     * Treatment constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly TreatmentResource $treatmentResource,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function getTreatmentId()
    {
        return $this->getData('treatment_id');
    }

    /**
     * @inheritdoc
     */
    public function setTreatmentId($value)
    {
        return $this->setData('treatment_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getTreatmentSku()
    {
        return $this->getData('treatment_sku');
    }

    /**
     * @inheritdoc
     */
    public function setTreatmentSku($value)
    {
        return $this->setData('treatment_sku', $value);
    }

    /**
     * @inheritdoc
     */
    public function getBaseCostTreatment()
    {
        return $this->getData('base_cost_treatment');
    }

    /**
     * @inheritdoc
     */
    public function setBaseCostTreatment($value)
    {
        return $this->setData('base_cost_treatment', $value);
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName()
    {
        return $this->getData('display_name');
    }

    /**
     * @inheritdoc
     */
    public function setDisplayName($value)
    {
        return $this->setData('display_name', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMinGlassSizeShort()
    {
        return $this->getData('min_glass_size_short');
    }

    /**
     * @inheritdoc
     */
    public function setMinGlassSizeShort($value)
    {
        return $this->setData('min_glass_size_short', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMinGlassSizeLong()
    {
        return $this->getData('min_glass_size_long');
    }

    /**
     * @inheritdoc
     */
    public function setMinGlassSizeLong($value)
    {
        return $this->setData('min_glass_size_long', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMaxGlassSizeShort()
    {
        return $this->getData('max_glass_size_short');
    }

    /**
     * @inheritdoc
     */
    public function setMaxGlassSizeShort($value)
    {
        return $this->setData('max_glass_size_short', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMaxGlassSizeLong()
    {
        return $this->getData('max_glass_size_long');
    }

    /**
     * @inheritdoc
     */
    public function setMaxGlassSizeLong($value)
    {
        return $this->setData('max_glass_size_long', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMinRabbetDepth()
    {
        return $this->getData('min_rabbet_depth');
    }

    /**
     * @inheritdoc
     */
    public function setMinRabbetDepth($value)
    {
        return $this->setData('min_rabbet_depth', $value);
    }

    /**
     * @inheritdoc
     */
    public function getRequiresTopMat()
    {
        return $this->getData('requires_top_mat');
    }

    /**
     * @inheritdoc
     */
    public function setRequiresTopMat($value)
    {
        return $this->setData('requires_top_mat', $value);
    }

    /**
     * @inheritdoc
     */
    public function getRequiresBottomMat()
    {
        return $this->getData('requires_bottom_mat');
    }

    /**
     * @inheritdoc
     */
    public function setRequiresBottomMat($value)
    {
        return $this->setData('requires_bottom_mat', $value);
    }

    /**
     * @inheritdoc
     */
    public function getRequiresLiner()
    {
        return $this->getData('requires_liner');
    }

    /**
     * @inheritdoc
     */
    public function setRequiresLiner($value)
    {
        return $this->setData('requires_liner', $value);
    }

    /**
     * @inheritdoc
     */
    public function getImageEdgeTreatment()
    {
        return $this->getData('image_edge_treatment');
    }

    /**
     * @inheritdoc
     */
    public function setImageEdgeTreatment($value)
    {
        return $this->setData('image_edge_treatment', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewTopMatSizeLeft()
    {
        return $this->getData('new_top_mat_size_left');
    }

    /**
     * @inheritdoc
     */
    public function setNewTopMatSizeLeft($value)
    {
        return $this->setData('new_top_mat_size_left', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewTopMatSizeTop()
    {
        return $this->getData('new_top_mat_size_top');
    }

    /**
     * @inheritdoc
     */
    public function setNewTopMatSizeTop($value)
    {
        return $this->setData('new_top_mat_size_top', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewTopMatSizeRight()
    {
        return $this->getData('new_top_mat_size_right');
    }

    /**
     * @inheritdoc
     */
    public function setNewTopMatSizeRight($value)
    {
        return $this->setData('new_top_mat_size_right', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewTopMatSizeBottom()
    {
        return $this->getData('new_top_mat_size_bottom');
    }

    /**
     * @inheritdoc
     */
    public function setNewTopMatSizeBottom($value)
    {
        return $this->setData('new_top_mat_size_bottom', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewBottomMatSizeLeft()
    {
        return $this->getData('new_bottom_mat_size_left');
    }

    /**
     * @inheritdoc
     */
    public function setNewBottomMatSizeLeft($value)
    {
        return $this->setData('new_bottom_mat_size_left', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewBottomMatSizeTop()
    {
        return $this->getData('new_bottom_mat_size_top');
    }

    /**
     * @inheritdoc
     */
    public function setNewBottomMatSizeTop($value)
    {
        return $this->setData('new_bottom_mat_size_top', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewBottomMatSizeRight()
    {
        return $this->getData('new_bottom_mat_size_right');
    }

    /**
     * @inheritdoc
     */
    public function setNewBottomMatSizeRight($value)
    {
        return $this->setData('new_bottom_mat_size_right', $value);
    }

    /**
     * @inheritdoc
     */
    public function getNewBottomMatSizeBottom()
    {
        return $this->getData('new_bottom_mat_size_bottom');
    }

    /**
     * @inheritdoc
     */
    public function setNewBottomMatSizeBottom($value)
    {
        return $this->setData('new_bottom_mat_size_bottom', $value);
    }


    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($value)
    {
        return $this->setData('updated_at', $value);
    }


    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @inheritdoc
     */
    public function setStatus($value)
    {
        return $this->setData('status', $value);
    }

    /**
     * @inheritdoc
     */
    public function getLinerDepthCheck()
    {
        return $this->getData('liner_depth_check');
    }

    /**
     * @inheritdoc
     */
    public function setLinerDepthCheck($value)
    {
        return $this->setData('liner_depth_check', $value);
    }

    /**
     * @inheritdoc
     */
    public function getTreatmentWeightPerSqFtUpToThreshold()
    {
        return $this->getData('treatment_weight_per_sqFt_upto_threshold');
    }

    /**
     * @inheritdoc
     */
    public function setTreatmentWeightPerSqFtUpToThreshold($value)
    {
        return $this->setData('treatment_weight_per_sqFt_upto_threshold', $value);
    }

    /**
     * @inheritdoc
     */
    public function getTreatmentWeightPerSqFtOverThreshold()
    {
        return $this->getData('treatment_weight_per_sqFt_over_threshold');
    }

    /**
     * @inheritdoc
     */
    public function setTreatmentWeightPerSqFtOverThreshold($value)
    {
        return $this->setData('treatment_weight_per_sqFt_over_threshold', $value);
    }


    /**
     * resource model
     */
    protected function _construct()
    {
        $this->_init(\Perficient\Rabbitmq\Model\ResourceModel\Treatment::class);
    }
}