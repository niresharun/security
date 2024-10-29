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
use Magento\Framework\Registry;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalog as MyCatalogResource;
use Perficient\MyCatalog\Api\Data\TemplateInterface;
use Perficient\MyCatalog\Api\PageRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class Template
 * @package Perficient\MyCatalog\Model
 */
class Template extends AbstractModel implements TemplateInterface
{
    /**
     * Template constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Json $json
     * @param LoggerInterface $logger
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly MyCatalogResource $catalogResource,
        private readonly Json $json,
        private readonly LoggerInterface $logger,
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
        $this->_init(\Perficient\MyCatalog\Model\ResourceModel\Template::class);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId()
    {
        return $this->getData('template_id');
    }

    /**
     * @inheritdoc
     */
    public function setTemplateId($value) {
        return $this->setData('template_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateDropSpotsCount()
    {
        return $this->getData('template_drop_spots_count');
    }

    /**
     * @inheritdoc
     */
    public function setTemplateDropSpotsCount($value) {
        return $this->setData('template_drop_spots_count', $value);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateFile()
    {
        return $this->getData('template_file');

    }

    /**
     * @inheritdoc
     */
    public function setTemplateFile($value)
    {
        return $this->setData('template_file', $value);

    }

    /**
     * @inheritdoc
     */
    public function getTemplateName()
    {
        return $this->getData('template_name');

    }

    /**
     * @inheritdoc
     */
    public function setTemplateName($value)
    {
        return $this->setData('template_name', $value);

    }

    /**
     * @param $params
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTemplateData($params)
    {
        $pageId = $this->pageRepository->getCatalogPageId($params['catalog_id'], $params['page']);
        $templateData = [
            'images' => $this->catalogResource->getGalleryImages($params['catalog_id']),
            'data'   => ''
        ];

        if ($pageId) {
            try {
                $page = $this->pageRepository->getById($pageId);
                $templateData['data'] = $this->json->unserialize($page->getDropSpotConfig());
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $templateData;
    }
}
