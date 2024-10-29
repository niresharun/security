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

namespace Perficient\Catalog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\Catalog\Api\Data\CustomProductImageInterface;

/**
 * Interface CustomProductImageRepositoryInterface
 * @package Perficient\Catalog\Api
 */
interface CustomProductImageRepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @return mixed
     */
    public function save(CustomProductImageInterface $customProductImage);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
