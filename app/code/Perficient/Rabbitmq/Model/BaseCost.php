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
use Perficient\Rabbitmq\Api\Data\BaseCostInterface;
use Perficient\Rabbitmq\Model\ResourceModel\BaseCost as BaseCostResource;

/**
 * Class BaseCost
 * @package Perficient\Rabbitmq\Model
 */
class BaseCost extends AbstractModel implements BaseCostInterface
{
    /**
     * BaseCost constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly BaseCostResource $baseCostResource,
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
    public function getBaseCostId()
    {
        return $this->getData('base_cost_id');
    }

    /**
     * @inheritdoc
     */
    public function setBaseCostId($value)
    {
        return $this->setData('base_cost_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getBaseCostMedia()
    {
        return $this->getData('base_cost_media');
    }

    /**
     * @inheritdoc
     */
    public function setBaseCostMedia($value)
    {
        return $this->setData('base_cost_media', $value);
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
    public function getGlassSizeShort()
    {
        return $this->getData('glass_size_short');
    }

    /**
     * @inheritdoc
     */
    public function setGlassSizeShort($value)
    {
        return $this->setData('glass_size_short', $value);
    }

    /**
     * @inheritdoc
     */
    public function getGlassSizeLong()
    {
        return $this->getData('glass_size_long');
    }

    /**
     * @inheritdoc
     */
    public function setGlassSizeLong($value)
    {
        return $this->setData('glass_size_long', $value);
    }

    /**
     * @inheritdoc
     */
    public function getBaseCost()
    {
        return $this->getData('base_cost');
    }

    /**
     * @inheritdoc
     */
    public function setBaseCost($value)
    {
        return $this->setData('base_cost', $value);
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
        $this->_init(\Perficient\Rabbitmq\Model\ResourceModel\BaseCost::class);
    }

}