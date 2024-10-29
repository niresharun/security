<?php
/**
 * This file is used to Update PDP Block
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Purushottam Rathi <purushottam.rathi@Perficient.com>
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
 * Class CmsBlockForPDPUpdate
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsBlockForPDPUpdate implements DataPatchInterface
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
        $this->updatePdpBlock();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Update PDP page Block.
     */
    private function updatePdpBlock(): void
    {
        $pdpBlock =
            [
                'title' => 'Customize PDP Contact',
                'stores' => [0],
                'identifier' => 'customize-pdp-contact-block',
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="pdp-static-content">
	<ul>
		<li>
		<div class="static-link"> <div class="static-title">Help With Your Order</div>
<ul><li class="static-web">On the web</li>
<li class="static-phone"><a href="tel:(888)743-9232">(888)743-9232</li>
<li class="static-emails"><a href="mailto:info@wendoverart.com" aria-label="Mail to info@wendoverart.com">info@wendoverart.com</a></li></ul>
</div>
		</li>
		<li>
		<div class="static-link"><div class="static-title">Customize This Artwork</div>
<p>Wendover Art Group offers a variety of customization options to help you create unique products for your clients. </p>
<p>Contact us for custom pricing:<span class="static-email"><a href="mailto:quotes.residential@wendoverart.com" aria-label="Mail to quotes.residential@wendoverart.com"> quotes.residential@wendoverart.com</a></span></p></div>
		</li>
		<li>
		<div class="static-link"><div class="static-title">Wendover’s Customer Dedication</div>
<p>Our commitment to excellence is uncompromising in consistently offering our customers a broad selection of fresh and unique artwork and mirrors of the highest quality</p></div>
		</li>
	</ul>
</div></div></div></div>'];
        $updatePdpPageBlock = $this->blockRepository->getById($pdpBlock['identifier']);
        if (!$updatePdpPageBlock->getId()) {
            $updatePdpPageBlock->setData($pdpBlock)->save();
        } else {
            $updatePdpPageBlock->setContent($pdpBlock['content'])->save();
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
