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
use Perficient\Rabbitmq\Api\Data\MediaInterface;
use Perficient\Rabbitmq\Model\ResourceModel\Media as MediaResource;

/**
 * Class Media
 * @package Perficient\Rabbitmq\Model
 */
class Media extends AbstractModel implements MediaInterface
{
    /**
     * Media constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly MediaResource $mediaResource,
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
    public function getMediaId()
    {
        return $this->getData('media_id');
    }

    /**
     * @inheritdoc
     */
    public function setMediaId($value)
    {
        return $this->setData('media_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->getData('sku');
    }

    /**
     * @inheritdoc
     */
    public function setSku($value)
    {
        return $this->setData('sku', $value);
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
    public function getMinImageSizeShort()
    {
        return $this->getData('min_image_size_short');
    }

    /**
     * @inheritdoc
     */
    public function setMinImageSizeShort($value)
    {
        return $this->setData('min_image_size_short', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMinImageSizeLong()
    {
        return $this->getData('min_image_size_long');
    }

    /**
     * @inheritdoc
     */
    public function setMinImageSizeLong($value)
    {
        return $this->setData('min_image_size_long', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMaxImageSizeShort()
    {
        return $this->getData('max_image_size_short');
    }

    /**
     * @inheritdoc
     */
    public function setMaxImageSizeShort($value)
    {
        return $this->setData('max_image_size_short', $value);
    }

    /**
     * @inheritdoc
     */
    public function getMaxImageSizeLong()
    {
        return $this->getData('max_image_size_long');
    }

    /**
     * @inheritdoc
     */
    public function setMaxImageSizeLong($value)
    {
        return $this->setData('max_image_size_long', $value);
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
        $this->_init(\Perficient\Rabbitmq\Model\ResourceModel\Media::class);
    }
}