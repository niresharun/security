<?php
/**
 * Magento Rabbitmq module to make API request/response.
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Perficient\Rabbitmq\Api\Data\BaseCostSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Class BaseCostSearchResults
 * @package Perficient\Rabbitmq\Model
 */
class BaseCostSearchResults extends SearchResults implements BaseCostSearchResultsInterface
{
}
