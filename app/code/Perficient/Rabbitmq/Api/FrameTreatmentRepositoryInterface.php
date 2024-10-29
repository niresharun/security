<?php
/**
 * Custom Table Data Management
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterface;

/**
 * Interface FrameTreatmentRepositoryInterface
 * @package Perficient\Rabbitmq\Api
 */
interface FrameTreatmentRepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @return mixed
     */
    public function save(FrameTreatmentInterface $frameTreatment);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
