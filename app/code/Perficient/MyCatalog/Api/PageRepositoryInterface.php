<?php
/**
 * This module is used to create custom artwork catalogs.
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

namespace Perficient\MyCatalog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\AlreadyExistsException;
use Perficient\MyCatalog\Api\Data\PageSearchResultsInterface;
use Perficient\MyCatalog\Api\Data\PageInterface;

/**
 * Interface PageRepositoryInterface
 * @package Perficient\MyCatalog\Api
 */
interface PageRepositoryInterface
{
    /**
     * @param int $id
     * @return PageInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @return PageInterface
     * @throws AlreadyExistsException
     */
    public function save(PageInterface $page);

    /**
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PageInterface $page);

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return PageSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $catalogId
     * @param int $pagePosition
     * @return int
     */
    public function getCatalogPageID($catalogId, $pagePosition);
}
