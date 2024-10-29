<?php
/**
 * This file is used to create CMS pages, CMS Bloges
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class CmsBlockWallCovering implements DataPatchInterface
{
    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly BlockFactory $blockFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->createCmsBlocks();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create CMS Bloges.
     */
    private function createCmsBlocks()
    {
        $cmsBlocks = [
            [
                'title' => 'Wall Covering',
                'identifier' => 'wallcovering_content',
                'stores' => [0],
                'is_active' => 1,
                'content' => "<div class=\"specialty\">Also available as a Wall Covering, please contact <a href=\"mailto:quotes.residential@wendoverart.com\">quotes.residential@wendoverart.com</a></div>",
            ],
        ];
        try {
            foreach ($cmsBlocks as $cmsBlock) {
                $blockFactory = $this->blockFactory->create()->load($cmsBlock['identifier'], 'identifier');
                if (!$blockFactory->getId()) {
                    $blockFactory->setData($cmsBlock)->save();
                } else {
                    $blockFactory->setContent($cmsBlock['content'])->save();
                }
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