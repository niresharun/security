<?php
/**
 * Magento Productimize module to make API request/response.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface ArtrulesInterface
 * @package Perficient\Productimize\Api
 */
interface ArtrulesInterface
{
    /**
     * @param null
     * @return array
     */
    public function getList();
}
