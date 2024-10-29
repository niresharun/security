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
use Perficient\MyCatalog\Api\Data\TemplateSearchResultsInterface;
use Perficient\MyCatalog\Api\Data\TemplateInterface;

/**
 * Interface TemplateRepositoryInterface
 * @package Perficient\MyCatalog\Api
 */
interface TemplateRepositoryInterface
{
    /**
     * @param int $id
     * @return TemplateInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @return TemplateInterface
     * @throws AlreadyExistsException
     */
    public function save(TemplateInterface $template);

    /**
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(TemplateInterface $template);

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return TemplateSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
