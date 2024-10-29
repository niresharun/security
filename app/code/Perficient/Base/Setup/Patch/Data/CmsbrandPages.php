<?php
/**
 * This file is used to create CMS brand page
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: harshal dantalwar <harshal.dantalwar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class CmsbrandPages
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsbrandPages implements DataPatchInterface
{
    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param PageFactory $pageFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Factory $configFactory,
        private readonly PageFactory $pageFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->createBrandPage();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create Brand page.
     */
    private function createBrandPage(): void
    {
        $page =
            [
                'title' => 'Brand Schemes',
                'stores' => [0],
                'identifier' => 'brand-schemes',
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' =>'<div data-content-type="row" data-appearance="contained" data-element="main"><div class="brand-schemes-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; text-align: center; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/brand_background.jpg}}" alt="" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/brand_background.jpg}}" alt="" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div class="brand-copy-section" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1>Lillian August</h1>
<div class="brand-inner-container">
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea </p>

<a href="#" class="show-more-link">Show More</a>
</div></div><figure class="brand-foreground-image" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/brand_foreground.jpg}}" alt="" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/brand_foreground.jpg}}" alt="" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure></div></div>'];
        $createPage = $this->pageFactory->create()->load(
            $page['identifier'],
            'identifier'
        );
        if (!$createPage->getId()) {
            $createPage->setData($page)->save();
        } else {
            $createPage->setContent($page['content'])->save();
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
