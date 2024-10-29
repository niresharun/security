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
use Perficient\MyCatalog\Api\Data\PageInterface;
use Perficient\MyCatalog\Model\ResourceModel\Page\Collection as PageCollectionFactory;

/**
 * Class Page
 * @package Perficient\MyCatalog\Model
 */
class Page extends AbstractModel implements PageInterface
{
    /**
     * Page constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        private readonly PageCollectionFactory $pageCollection,
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
        $this->_init(\Perficient\MyCatalog\Model\ResourceModel\Page::class);
    }

    /**
     * @inheritdoc
     */
    public function getPageId()
    {
        return $this->getData('page_id');
    }

    /**
     * @inheritdoc
     */
    public function setPageId($value) {
        return $this->setData('page_id', $value);
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
    public function getPageTemplateId()
    {
        return $this->getData('page_template_id');

    }

    /**
     * @inheritdoc
     */
    public function setPageTemplateId($value)
    {
        return $this->setData('page_template_id', $value);

    }

    /**
     * @inheritdoc
     */
    public function getDropSpotConfig()
    {
        return $this->getData('drop_spot_config');

    }

    /**
     * @inheritdoc
     */
    public function setDropSpotConfig($value)
    {
        return $this->setData('drop_spot_config', $value);

    }

    /**
     * @inheritdoc
     */
    public function getPagePosition()
    {
        return $this->getData('page_position');

    }

    /**
     * @inheritdoc
     */
    public function setPagePosition($value)
    {
        return $this->setData('page_position', $value);

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
    public function getPageUuid()
    {
        return $this->getData('page_uuid');
    }

    /**
     * @inheritdoc
     */
    public function setPageUuid($value)
    {
        return $this->setData('page_uuid', $value);
    }

    /**
     * Gets the page_id if page exists, otherwise returns false
     *
     * @param $catalogId
     * @param $pagePosition
     * @return int
     */
    public function getCatalogPageID($catalogId, $pagePosition)
    {
        $page = $this->pageCollection
            ->addFieldTofilter('catalog_id', $catalogId)
            ->addFieldTofilter('page_position', $pagePosition);

        try {
            $dataArray = $page->getData();
            $data = $dataArray[0];
        } catch (\Exception) {
            $data = [];
        }

        if (isset($data['page_id']) && !empty($data['page_id'])) {
            return (int)$data['page_id'];
        } else {
            return 0;
        }
    }
}
