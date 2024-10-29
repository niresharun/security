<?php
/**
 * This file is used to create CMS Blocks
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

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class CmsBlogs
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsBlogs implements DataPatchInterface
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
        $this->createCmsBlock();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create CMS Bloges.
     */
    private function createCmsBlock(): void
    {
        $cmsBlocks = [
            [
                'title' => 'Inspire',
                'identifier' => 'inspire',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div class="inspire-inner-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 0px;"><div class="top-heading" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 5px; padding: 0px;"><h2>Inspire</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam.</p>
<p><a href="#" target="_blank" rel="noopener">view more</a></p>
</div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">

<div class="tiles-wrapper">
<ul class="tiles-container" role="list">
<li role="listitem">
<div class="content-item">
<img src="{{media url=wysiwyg/inspire_1.jpg}}" alt="" />
  <div class="overlay"></div>
  <div class="corner-overlay-content"><i class="fa fa-undo" aria-hidden="true"></i></div>
  <div class="overlay-content">
<button class="action-close" data-role="closeBtn" type="button">
                <span>Close</span>
            </button>
    <div class="heading">LILLIAN AUGUST</div>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed</p>
  <p><a href="#" target="_blank">view more</a></p>
  </div>
</div>
</li>
<li role="listitem">
<div class="content-item">
<img src="{{media url=wysiwyg/inspire_4.jpg}}" alt="" />
  <div class="overlay"></div>
  <div class="corner-overlay-content"><i class="fa fa-undo" aria-hidden="true"></i></div>
  <div class="overlay-content">
<button class="action-close" data-role="closeBtn" type="button">
                <span>Close</span>
            </button>
     <div class="heading">LILLIAN AUGUST</div>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed</p>
<p><a href="#" target="_blank">view more</a></p>
  </div>
</div>
</li>
<li role="listitem">
<div class="content-item">
<img src="{{media url=wysiwyg/inspire_2.jpg}}" alt="" />
  <div class="overlay"></div>
  <div class="corner-overlay-content"><i class="fa fa-undo" aria-hidden="true"></i></div>
  <div class="overlay-content">
<button class="action-close" data-role="closeBtn" type="button">
                <span>Close</span>
            </button>
   <div class="heading">LILLIAN AUGUST</div>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed</p>
<p><a href="#" target="_blank">view more</a></p>
</div>
</div>
</li>
<li role="listitem">
<div class="content-item">
<img src="{{media url=wysiwyg/inspire_5.jpg}}" alt="" />
  <div class="overlay"></div>
  <div class="corner-overlay-content"><i class="fa fa-undo" aria-hidden="true"></i></div>
  <div class="overlay-content">
<button class="action-close" data-role="closeBtn" type="button">
                <span>Close</span>
            </button>
    <div class="heading">LILLIAN AUGUST</div>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed</p>
<p><a href="#" target="_blank">view more</a></p>
  </div>
</div>
</li>
<li role="listitem">
<div class="content-item"> <img src="{{media url=wysiwyg/inspire_3.jpg}}" alt="" />
  <div class="overlay"></div>
  <div class="corner-overlay-content"><i class="fa fa-undo" aria-hidden="true"></i></div>
  <div class="overlay-content">
<button class="action-close" data-role="closeBtn" type="button">
                <span>Close</span>
            </button>
    <div class="heading">LILLIAN AUGUST</div>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed</p>
<p><a href="#" target="_blank">view more</a></p>
 </div>
</div>
</li>
<li role="listitem">
<div class="content-item"><img src="{{media url=wysiwyg/inspire_5.jpg}}" alt="" />
  <div class="overlay"></div>
  <div class="corner-overlay-content"><i class="fa fa-undo" aria-hidden="true"></i></div>
  <div class="overlay-content">
<button class="action-close" data-role="closeBtn" type="button">
                <span>Close</span>
            </button>
     <div class="heading">LILLIAN AUGUST</div>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed</p>
<p><a href="#" target="_blank">view more</a></p>
</div>
</div>
</li>
</ul>
</div></div></div></div>',
            ],
            [
                'title' => 'Locations',
                'identifier' => 'locations',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="top-heading" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 5px; padding: 0px;"><h2>Locations</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel locations-section">
<div class="image-container">
<img src="{{media url=wysiwyg/locations_largo.png}}" alt="Largo" />

<div class="title"><a href="#">Largo<span>Headquarters</span></a></div>
</div>
<div class="image-container">
 <img src="{{media url=wysiwyg/locations_atlanta.png}}" alt="Atlanta Art Consulting" />
<div class="title"><a href="#">Atlanta<span>Art Consulting</span></a></div>
</div>
<div class="image-container">
 <img src="{{media url=wysiwyg/locations_atlanta_showroom.png}}" alt="Atlanta Showroom" />
<div class="title"><a href="#">Atlanta<span>Showroom</span></a></div>
</div>
<div class="image-container">
 <img src="{{media url=wysiwyg/locations_atlanta_showroom.png}}" alt="Atlanta Showroom" />
<div class="title"><a href="#">Atlanta<span>Showroom</span></a></div>
</div>
<div class="image-container">
 <img src="{{media url=wysiwyg/locations_atlanta_showroom.png}}" alt="Atlanta Showroom" />
<div class="title"><a href="#">Atlanta<span>test4</span></a></div>
</div>
<div class="image-container">
 <img src="{{media url=wysiwyg/locations_atlanta_showroom.png}}" alt="Atlanta Showroom" />
<div class="title"><a href="#">Atlanta<span>test5</span></a></div>
</div>
</div>
<div class="slider"></div>
	</div></div></div>>',
            ],
        ];

        try {
            foreach ($cmsBlocks as $cmsBlock) {
                $createCmsBlock = $this->blockRepository->getById($cmsBlock['identifier']);

                if (!$createCmsBlock->getId()) {
                    $createCmsBlock->setData($cmsBlock)->save();
                } else {
                    $createCmsBlock->setContent($cmsBlock['content'])->save();
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
