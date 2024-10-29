<?php
/**
 * This file is used to create CMS blocks for Lead Time Notifications.
 *
 * @category: Magento
 * @package: Perficient/LeadTime
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_LeadTime LeadTime CMS Block
 */
declare(strict_types=1);

namespace Perficient\LeadTime\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Config\Model\Config\Factory;

/**
 * Class LeadTimeBlocks
 * @package Perficient\LeadTime\Setup\Patch\Data
 */
class LeadTimeBlocks implements DataPatchInterface
{
    /**
     * LeadTimeBlocks constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Factory                  $configFactory,
        private readonly BlockFactory             $blockFactory
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->createLeadTimeCmsBlocks();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Method used to create cms blocks for lead time notifications.
     *
     * @return void
     * @throws \Exception
     */
    private function createLeadTimeCmsBlocks()
    {
        $cmsBlocks = [
            [
                'title' => 'Standard Lead Time Notification',
                'identifier' => 'standard_lead_time',
                'stores' => [0],
                'is_active' => 1,
                'content' => 'Standard lead time is currently three weeks.',
            ],
            [
                'title' => 'Quick Ship Lead Time Notification',
                'identifier' => 'quick_ship_lead_time',
                'stores' => [0],
                'is_active' => 1,
                'content' => 'Quick Ship lead time is currently three business days.</div>',
            ],
        ];

        try {
            foreach ($cmsBlocks as $cmsBlock) {
                $this->blockFactory->create()->setData($cmsBlock)->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
