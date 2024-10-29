<?php
/**
 * This file is used to create CMS Blocks for PDP page
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: neha samuel <neha.samuel@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;
use Magento\Cms\Api\BlockRepositoryInterface;

/**
 * Class CmsBlockForPDP
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsBlockForPDP implements DataPatchInterface
{
    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param PageFactory $pageFactory
     * @param BlockRepositoryInterface $blockRepository
     */
    public function __construct(
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Factory $configFactory,
        protected PageFactory $pageFactory,
        protected BlockRepositoryInterface $blockRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->createCmsBlock();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create CMS Block.
     */
    private function createCmsBlock(): void
    {
        $cmsBlocks = [
[
                'title' => 'Customize PDP Contact',
                'identifier' => 'customize-pdp-contact-block',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="pdp-static-content">
	<ul>
		<li>
		<div class="static-link"> <div class="static-title">Help With Your Order</div>
<ul><li class="static-web">On the web</li>
<li class="static-phone">(888)743-9232</li>
<li class="static-emails">info@wendoverart.com</li></ul>
</div>
		</li>
		<li>
		<div class="static-link"><div class="static-title">Customize This Artwork</div>
<p>Wendover Art Group offers a variety of customization options to help you create unique products for your clients. </p>
<p>Contact us for custom pricing:<span class="static-email"> quotes.residential@wendoverart.com</span></p></div>
		</li>
		<li>
		<div class="static-link"><div class="static-title">Wendover’s Customer Dedication</div>
<p>Our commitment to excellence is uncompromising in consistently offering our customers a broad selection of fresh and unique artwork and mirrors of the highest quality</p></div>
		</li>
	</ul>
</div></div></div></div>'
]
        ];

        try {
            foreach ($cmsBlocks as $cmsBlock) {
                $pdpCmsBlock = $this->blockRepository->getById($cmsBlock['identifier']) ?? "";

                if ($pdpCmsBlock->getId()) {
                    $pdpCmsBlock->setContent($cmsBlock['content'])->save();
                } else {
                    $pdpCmsBlock->setData($cmsBlock)->save();
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
