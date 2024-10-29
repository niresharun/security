<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Model;

use Magento\Framework\Model\AbstractModel;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterface;

/**
 * Class MyCatalog
 * @package Perficient\MyCatalog\Model
 */
class MyCatalogDelete extends AbstractModel implements MyCatalogDeleteInterface
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Perficient\MyCatalog\Model\ResourceModel\MyCatalogDelete::class);
    }

    /**
     * @return int|null
     */
    public function getDeletionEventId()
    {
        return $this->getData('deletion_event_id');
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setDeletionEventId($value)
    {
        return $this->setData('deletion_event_id', $value);
    }

    /**
     * @return int|null
     */
    public function getCatalogId()
    {
        return $this->getData('catalog_id');
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setCatalogId($value)
    {
        return $this->setData('catalog_id', $value);
    }

    /**
     * @return int|null
     */
    public function getWishlistId()
    {
        return $this->getData('wishlist_id');
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setWishlistId($value)
    {
        return $this->setData('wishlist_id', $value);
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        return $this->setData('updated_at', $value);
    }

    /**
     * @return string|null
     */
    public function getAction()
    {
        return $this->getData('action');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAction($value)
    {
        return $this->setData('action', $value);
    }
}
