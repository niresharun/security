<?php
/**
 * Magento Rabbitmq module to make API request/response.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
namespace Perficient\Rabbitmq\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface BaseCostSearchResultsInterface
 * @package Perficient\Rabbitmq\Api\Data
 */
interface BaseCostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get BaseCost
     *
     * @return \Perficient\Rabbitmq\Api\Data\BaseCostInterface[]
     */
    public function getItems();

    /**
     * Set BaseCost
     *
     * @param \Perficient\Rabbitmq\Api\Data\BaseCostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
