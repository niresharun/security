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
use Perficient\Rabbitmq\Api\Data\MediaTreatmentInterface;
use Perficient\Rabbitmq\Model\ResourceModel\MediaTreatment as MediaTreatmentResource;

/**
 * Class MediaTreatment
 * @package Perficient\Rabbitmq\Model
 */
class MediaTreatment extends AbstractModel implements MediaTreatmentInterface
{
    /**
     * MediaTreatment constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly MediaTreatmentResource $mediaTreatmentResource,
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
    public function getMediaTreatmentId()
    {
        return $this->getData('media_treatment_id');
    }

    /**
     * @inheritdoc
     */
    public function setMediaTreatmentId($value)
    {
        return $this->setData('media_treatment_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMediaSku()
    {
        return $this->getData('media_sku');
    }

    /**
     * @inheritdoc
     */
    public function setMediaSku($value)
    {
        return $this->setData('media_sku', $value);
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
    public function getDisplayToCustomer()
    {
        return $this->getData('display_to_customer');
    }

    /**
     * @inheritdoc
     */
    public function setDisplayToCustomer($value)
    {
        return $this->setData('display_to_customer', $value);
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
        $this->_init(\Perficient\Rabbitmq\Model\ResourceModel\MediaTreatment::class);
    }
}