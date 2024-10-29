<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Perficient\MyCatalog\Api\Data\MyCatalogInterface;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalog as MyCatalogResource;

/**
 * Class MyCatalog
 * @package Perficient\MyCatalog\Model
 */
class MyCatalog extends AbstractModel implements MyCatalogInterface
{
    /**
     * MyCatalog constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        private readonly MyCatalogResource $catalogResource,
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
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Perficient\MyCatalog\Model\ResourceModel\MyCatalog::class);
    }

    /**
     * @inheritdoc
     */
    public function getCatalogId()
    {
        return $this->getData('catalog_id');
    }

    /**
     * @inheritdoc
     */
    public function setCatalogId($value) {
        return $this->setData('catalog_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');

    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($value)
    {
        return $this->setData('customer_id', $value);

    }

    /**
     * @inheritdoc
     */
    public function getWishlistId()
    {
        return $this->getData('wishlist_id');

    }

    /**
     * @inheritdoc
     */
    public function setWishlistId($value)
    {
        return $this->setData('wishlist_id', $value);

    }

    /**
     * @inheritdoc
     */
    public function getLogoImage()
    {
        return $this->getData('logo_image');

    }

    /**
     * @inheritdoc
     */
    public function setLogoImage($value)
    {
        return $this->setData('logo_image', $value);

    }

    /**
     * @inheritdoc
     */
    public function getCatalogTitle()
    {
        return $this->getData('catalog_title');

    }

    /**
     * @inheritdoc
     */
    public function setCatalogTitle($value)
    {
        return $this->setData('catalog_title', $value);

    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData('name');

    }

    /**
     * @inheritdoc
     */
    public function setName($value)
    {
        return $this->setData('name', $value);

    }

    /**
     * @inheritdoc
     */
    public function getPhoneNumber()
    {
        return $this->getData('phone_number');

    }

    /**
     * @inheritdoc
     */
    public function setPhoneNumber($value)
    {
        return $this->setData('phone_number', $value);

    }

    /**
     * @inheritdoc
     */
    public function getWebsiteUrl()
    {
        return $this->getData('website_url');

    }

    /**
     * @inheritdoc
     */
    public function setWebsiteUrl($value)
    {
        return $this->setData('website_url', $value);

    }

    /**
     * @inheritdoc
     */
    public function getCompanyName()
    {
        return $this->getData('company_name');

    }

    /**
     * @inheritdoc
     */
    public function setCompanyName($value)
    {
        return $this->setData('company_name', $value);

    }

    /**
     * @inheritdoc
     */
    public function getAdditionalInfo1()
    {
        return $this->getData('additional_info_1');

    }

    /**
     * @inheritdoc
     */
    public function setAdditionalInfo1($value)
    {
        return $this->setData('additional_info_1', $value);

    }

    /**
     * @inheritdoc
     */
    public function getAdditionalInfo2()
    {
        return $this->getData('additional_info_2');

    }

    /**
     * @inheritdoc
     */
    public function setAdditionalInfo2($value)
    {
        return $this->setData('additional_info_2', $value);

    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');

    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData('created_at', $value);

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
    public function getPriceModifier()
    {
        return $this->getData('price_modifier');
    }

    /**
     * @inheritdoc
     */
    public function setPriceModifier($value)
    {
        return $this->setData('price_modifier', $value);
    }

    /**
     * @inheritdoc
     */
    public function getCatalogUuid()
    {
        return $this->getData('catalog_uuid');
    }

    /**
     * @inheritdoc
     */
    public function setCatalogUuid($value)
    {
        return $this->setData('catalog_uuid', $value);
    }

    /**
     * @param $catalogId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGalleryImages($catalogId)
    {
        return $this->catalogResource->getGalleryImages($catalogId);
    }

    /**
     * @param $catalogId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGalleryImagesPdf($catalogId)
    {
        return $this->catalogResource->getGalleryImagesPdf($catalogId);
    }
}
