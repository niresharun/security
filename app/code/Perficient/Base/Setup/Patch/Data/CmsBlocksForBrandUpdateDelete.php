<?php
/**
 * This file is used to update and delete CMS Blocks and pages of brand page
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: purushottam rathi <purushottam.rathi@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class CmsBlocksForBrandUpdateDelete
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsBlocksForBrandUpdateDelete implements DataPatchInterface
{
    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param PageFactory $pageFactory
     */
    public function __construct(
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Factory $configFactory,
        protected BlockRepositoryInterface $blockRepository,
        protected PageFactory $pageFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->updateCmsBlockTemplate();
        $this->deleteCmsBlocks();
        $this->deleteCmsPages();
        $this->moduleDataSetup->endSetup();
    }


    /**
     * Update CMS Blocks Template.
     */
    private function updateCmsBlockTemplate(): void
    {
        $cmsBrandBlocks = [
            [
                'title' => 'Brand Horizontal Banner Template',
                'stores' => [0],
                'identifier' => 'brand-horizontal-banner-template',
                'content' => '{{block class="Magento\Catalog\Block\Category\View" name="horizontal_template" template="Magento_Catalog::category/brand-horizontal-banner-template.phtml"}}',
            ],
            [
                'title' => 'Brand Vertical Banner Template',
                'stores' => [0],
                'identifier' => 'brand-vertical-banner-template',
                'content' => '{{block class="Magento\Catalog\Block\Category\View" name="vertical_template" template="Magento_Catalog::category/brand-vertical-banner-template.phtml"}}',
            ],
            [
                'title' => 'Standard Caetgory Banner Template',
                'stores' => [0],
                'identifier' => 'standard-category-banner-template',
                'content' => '{{block class="Magento\Catalog\Block\Category\View" name="standard_template" template="Magento_Catalog::category/standard-category-banner-template.phtml"}}',
            ],
        ];

        try {
            foreach ($cmsBrandBlocks as $cmsBrandBlock) {
                $myBlockTemplate = $this->blockRepository->getById($cmsBrandBlock['identifier']);

                if (!$myBlockTemplate->getId()) {
                    $myBlockTemplate->setData($cmsBrandBlock)->save();
                } else {
                    $myBlockTemplate->setContent($cmsBrandBlock['content'])->save();
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * Delete CMS Brand Schemes Block
     */
    private function deleteCmsBlocks(): void
    {
        $deleteCmsBlocks = [
            [
                'identifier' => 'brand_page_vertical'
            ],
            [
                'identifier' => 'brand-schemes-horizontal'
            ],
            [
                'identifier' => 'brand-schemes-vertical'
            ]
        ];

        try {
            foreach ($deleteCmsBlocks as $deleteCmsBlock) {
                $myBlockTemplate = $this->blockRepository->getById($deleteCmsBlock['identifier']);
                if ($myBlockTemplate->getId()) {
                    $myBlockTemplate->delete();
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * Delete CMS Brand Schemes Pages
     */
    private function deleteCmsPages(): void
    {
        $deleteCmsPages = [
            [
                'identifier' => 'brands'
            ],
            [
                'identifier' => 'brand-scheme'
            ],
            [
                'identifier' => 'brand-page-vertical'
            ]
        ];

        try {
            foreach ($deleteCmsPages as $deleteCmsPage) {
                $myPageTemplate = $this->pageFactory->create()->load(
                    $deleteCmsPage['identifier'],
                    'identifier'
                );
                if ($myPageTemplate->getId()) {
                    $myPageTemplate->delete();
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
