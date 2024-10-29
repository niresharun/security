<?php
/**
 * Custom Product Image
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain <hiral.jain@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\Catalog\Api\Data\CustomProductImageInterface;
use Perficient\Catalog\Api\Data\CustomProductImageInterfaceFactory;
use Perficient\Catalog\Api\CustomProductImageRepositoryInterface;
use Perficient\Catalog\Model\ResourceModel\CustomProductImage as ResourceCustomProductImage;
use Perficient\Catalog\Model\ResourceModel\CustomProductImage\CollectionFactory;

/**
 * Class CustomProductImageRepository
 * @package Perficient\Catalog\Model
 */
class CustomProductImageRepository implements CustomProductImageRepositoryInterface
{
    /**
     * CustomProductImageRepository constructor.
     * @param CustomProductImageInterfaceFactory $customProductImageInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly ResourceCustomProductImage         $resource,
        private readonly CustomProductImageInterfaceFactory $customProductImageInterfaceFactory,
        private readonly CollectionFactory                  $collectionFactory,
        private readonly CollectionProcessorInterface       $collectionProcessor
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $imageModel = $this->customProductImageInterfaceFactory->create();
        $this->resource->load($imageModel, $id);
        if (!$imageModel->getCustomProductImageId()) {
            throw new NoSuchEntityException(__('Unable to find  Custom Image Product Data with ID %s', $id));
        }
        return $imageModel;
    }

    /**
     * @inheritdoc
     */
    public function save(CustomProductImageInterface $customProductImage)
    {
        $this->resource->save($customProductImage);
        return $customProductImage;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->customProductImageInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}
