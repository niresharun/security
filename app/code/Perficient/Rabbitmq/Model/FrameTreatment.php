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
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterface;
use Perficient\Rabbitmq\Model\ResourceModel\FrameTreatment as FrameTreatmentResource;

/**
 * Class FrameTreatment
 * @package Perficient\Rabbitmq\Model
 */
class FrameTreatment extends AbstractModel implements FrameTreatmentInterface
{
    /**
     * FrameTreatment constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly FrameTreatmentResource $frameTreatmentResource,
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
    public function getFrameTreatmentId()
    {
        return $this->getData('frame_treatment_id');
    }

    /**
     * @inheritdoc
     */
    public function setFrameTreatmentId($value)
    {
        return $this->setData('frame_treatment_id', $value);
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
    public function getFrameType()
    {
        return $this->getData('frame_type');
    }

    /**
     * @inheritdoc
     */
    public function setFrameType($value)
    {
        return $this->setData('frame_type', $value);
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
     * resource model
     */
    protected function _construct()
    {
        $this->_init(\Perficient\Rabbitmq\Model\ResourceModel\FrameTreatment::class);
    }

}