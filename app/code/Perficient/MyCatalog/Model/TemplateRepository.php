<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey Pali <Kartikey.Pali@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\MyCatalog\Api\Data\TemplateSearchResultsInterfaceFactory;
use Perficient\MyCatalog\Api\TemplateRepositoryInterface;
use Perficient\MyCatalog\Api\Data\TemplateInterfaceFactory;
use Perficient\MyCatalog\Api\Data\TemplateInterface;
use Perficient\MyCatalog\Model\ResourceModel\Template as ResourceTemplate;
use Perficient\MyCatalog\Model\ResourceModel\Template\CollectionFactory;

/**
 * Class TemplateRepository
 * @package Perficient\MyCatalog\Model
 */
class TemplateRepository implements TemplateRepositoryInterface
{
    /**
     * TemplateRepository constructor.
     *
     * @param TemplateSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     * @param TemplateInterfaceFactory $templateFactory
     */
    public function __construct(
        private readonly TemplateSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly CollectionFactory $collectionFactory,
        private readonly ResourceTemplate $resource,
        private readonly TemplateInterfaceFactory $templateFactory
    ){
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $templateModel = $this->templateFactory->create();
        $this->resource->load($templateModel, $id);
        if (!$templateModel->getTemplateId()) {
            throw new NoSuchEntityException(__('Unable to find template with ID %s', $id));
        }

        return $templateModel;
    }

    /**
     * @inheritdoc
     */
    public function delete(TemplateInterface $template)
    {
        try {
            $templateModel = $this->templateFactory->create();
            $this->resource->load($templateModel, $template->getTemplateId());
            $this->resource->delete($templateModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the template: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }


    /**
     * @inheritdoc
     */
    public function save(TemplateInterface $template)
    {
        $this->resource->save($template);
        return $template;
    }
}
