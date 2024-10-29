<?php
/**
 * This module is used to create custom artwork catalogs.
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

namespace Perficient\MyCatalog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perficient\MyCatalog\Api\Data\MyCatalogSearchResultsInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogInterface;

/**
 * Interface MyCatalogRepositoryInterface
 * @package Perficient\MyCatalog\Api
 */
interface MyCatalogRepositoryInterface
{
    /**
     * @param int $id
     * @return MyCatalogInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @return MyCatalogInterface
     * @throws AlreadyExistsException
     */
    public function save(MyCatalogInterface $myCatalog);

    /**
     * @return bool
     */
    public function delete(MyCatalogInterface $catalog);

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MyCatalogSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MyCatalogSearchResultsInterface
     */
    public function getSharedCatalogList(SearchCriteriaInterface $searchCriteria);

    /**
     * Mark As Shared Catalog
     *
     * @param int $customerId
     * @param int $catalogId
     * @return bool
     */
    public function markAsSharedCatalog($customerId, $catalogId, mixed $priceMultiplier);

    /**
     * Is Shared Catalog
     *
     * @param int $catalogId
     * @param int $customerId
     * @return bool
     */
    public function isSharedCatalog($catalogId, $customerId);
}
