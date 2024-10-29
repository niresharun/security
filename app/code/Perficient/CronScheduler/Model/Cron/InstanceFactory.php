<?php
/**
 * Display instance in the grid of cron list
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */

namespace Perficient\CronScheduler\Model\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class InstanceFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    public function create($className)
    {
        $cronInstance = $this->objectManager->create($className);
        if (!is_object($cronInstance)) {
            throw new LocalizedException(
                __('%1 doesn\'t exist in the system', $className)
            );
        }
        return $cronInstance;
    }
}
