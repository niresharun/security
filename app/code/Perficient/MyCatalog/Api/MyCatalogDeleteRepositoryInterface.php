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

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterface;
use Magento\Framework\Api\SearchCriteriaInterface;;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteSearchResultsInterface;

/**
 * Interface MyCatalogDeleteRepositoryInterface
 * @package Perficient\MyCatalog\Api
 */
interface MyCatalogDeleteRepositoryInterface
{
    /**
     * @param int $id
     * @return MyCatalogDeleteInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @return MyCatalogDeleteInterface
     * @throws AlreadyExistsException
     */
    public function save(MyCatalogDeleteInterface $myCatalogDelete);

    /**
     * @return bool
     */
    public function delete(MyCatalogDeleteInterface $myCatalogDelete);

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id);


    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return MyCatalogDeleteSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
