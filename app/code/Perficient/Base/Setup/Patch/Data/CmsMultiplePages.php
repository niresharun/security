<?php
/**
 * This file is used to create Multiple CMS pages
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

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class CmsMultiplePages
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsMultiplePages implements DataPatchInterface
{
    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param PageFactory $pageFactory
     */
    public function __construct(
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Factory $configFactory,
        protected PageFactory $pageFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->createMultipleCMSPages();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create Multiple CMS pages.
     */
    private function createMultipleCMSPages(): void
    {
        $cmsPages = [
            [
                'title' => 'Senior Living',
                'identifier' => 'senior-living',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Senior Living" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Senior Living" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Senior Living
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Locations',
                'identifier' => 'locations',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Locations" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Locations" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Locations
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Our Showroom',
                'identifier' => 'our-showroom',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Our Showroom" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Our Showroom" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Our Showroom
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Inspiration Contest',
                'identifier' => 'inspiration-contest',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Inspiration Contest" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Inspiration Contest" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Inspiration Contest
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Sustainability',
                'identifier' => 'sustainability',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Sustainability" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Sustainability" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Sustainability
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
				',
            ],
            [
                'title' => 'Trade Shows',
                'identifier' => 'trade-shows',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Trade Shows" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Trade Shows" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Trade Shows
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Healthcare',
                'identifier' => 'healthcare',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Healthcare" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Healthcare" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Healthcare
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Multi-Family',
                'identifier' => 'multi-family',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Multi-Family" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Multi-Family" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Multi-Family
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'One Wendover',
                'identifier' => 'one-wendover',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image One Wendover" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image One Wendover" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       One Wendover
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Residential',
                'identifier' => 'residential',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Residential" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Residential" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Residential
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
                ',
            ],
            [
                'title' => 'Workspace',
                'identifier' => 'workspace',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Workspace" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/landing_banner_image.png}}" alt="Image Workspace" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading">
       Workspace
</h1>
<p class="banner-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo et ea rebum.</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-slider-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="pagebuilder-slider pagebuilder-slider-common" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="4000" data-fade="false" data-infinite-loop="false" data-show-arrows="true" data-show-dots="true" data-element="main" style="min-height: 578px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div><div class="common-element-slick" data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider.png}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/landing_page_slider_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 40px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"></div></div></div></div></div></div></div></div><div class="pagebuilder-column landing-page-slider-info-right" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 50%; margin: 0px; padding: 0px; align-self: stretch;"><div class="slider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Why Wendover</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo.</p>
<a href="#">Show More</a></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 20px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Lorem Ipsum</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo.</p></div></div><div class="pagebuilder-column multislider-container" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="slick-wrapper">
<div id="multislider">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">Marriott</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">Wyndham</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">IHG</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">Choice</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/hilton.png}}" alt="" />
  <div><a href="#">Hilton</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
 <div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/lorem_Ipsum.jpg}}" alt="" />
  <div><a href="#">Lorem Ipsum</a></div>
</div>
</div>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">Project Showcase</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
				',
            ],
            [
                'title' => 'Art Consulting',
                'identifier' => 'art-consulting',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '
                    <div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
       Art Consulting
</h1></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider landing-page-separation" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info landing-page-info" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>section Title</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam volu ptua. At vero eos et accusam et justo duo dolo res et ea rebum. Stet clita kasd gubergren, no sea. Lorem ipsum dolor sit amet, consetetur firmed tempor et dolore. At vero eos et accusam et justo. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut labore dolore magna aliquyam erat, sed diam dolor sit llita dasd voluptua.</p></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><p class="landing-page-more"><a href="#">Show More</a></p></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 10px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="landing-image-one">
<img src="{{media url=wysiwyg/image-placeholder.png}}" alt="Image Placeholder" /></div>
<div class="landing-image-two">
<div class="landing-images-one">
<img src="{{media url=wysiwyg/image-placeholder-1.png}}" alt="Image Placeholder 1" />
</div>
<div class="landing-images-two">
<img src="{{media url=wysiwyg/image-placeholder-2.png}}" alt="Image Placeholder 2" />
</div>
</div>




            </div><div data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><p class="landing-credits" style="text-align: right;"><em><strong>Photo Credits:</strong> Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor ut labore</em></p></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">
         <h2 id="G1O6R6N">Other Projects</h2>
      </div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="carousel project-showcase-container">

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Image -1" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Image -2" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -3" />
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</a></div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -4" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -5" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary"  tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -6" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -7" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -8" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -9" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>

<div class="image-container common-element-slick" tabindex="0">
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Image -10" />
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Project Name</div>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut laboreLorem ipsum dolor sit amet, consetetur sadipscing.</p>
<button type="button" title="" class="action primary" tabindex="-1"><span aria-hidden="true">Learn More</span></button>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="landing-page-forms" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title">Start Your Project</div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>
				',
            ]
            /*,
            [
                'title' => 'Create Catalog Help',
                'identifier' => 'create-catalog-help',
                'stores' => [0],
                'page_layout' => '1column',
                'is_active' => 1,
                'content' => '
                  <h1>Creating Catalogs</h1>
<p>With the Catalog Creator you can take artwork from project galleries and create catalogs to share with clients. The tools allow you set custom pricing (or display no pricing) and drag and drop artwork and place it in any sequence you choose. You can download, print or email your catalog to share with others.</p>
<p>All your catalogs can be viewed at the <a href="{{store url=\'mycatalog\'}}">My Catalogs</a> link, within <a href="{{store url=\'customer/account\'}}">My Account</a>.</p>
<h2>Catalog Creator Help</h2>
<p>You&rsquo;ll notice three main areas on the Create Catalog page:</p>
<p><strong>CATALOG NAVIGATOR&nbsp;</strong>- This area at the top provides an overview of all your pages, helps you navigate to individual pages, edit covers and set pricing.</p>
<p><strong>PAGE EDITOR</strong> &ndash; in the middle section of the Create Catalog page you can add or delete pages, choose the layout of each page and drag artwork from the Art Selector into the pages as you create them.</p>
<p><strong>ART SELECTOR</strong> &ndash; Along the bottom, the Art Selector displays artwork you&rsquo;ve stored in your galleries; use those images to create the pages of your catalog.</p>
<p>View your galleries at the <a href="{{store url=\'wishlist\'}}">My Gallery</a> link.</p>
<h2>Catalog Navigator Tools</h2>
<p><strong>ACTION</strong> - Click here to create a PDF of the catalog, print it or delete it.</p>
<p><strong>EDIT COVER</strong> &ndash; This takes you to the Catalog Setup page where you can edit the front and back covers of the catalog.</p>
<p><strong>PAGE NAVIGATOR</strong> &ndash; With the page navigator, you can see all the pages of your catalog; the page you&rsquo;re currently working on will be highlighted. Click on a page to edit it.</p>
<p>You can change the order of pages by clicking and dragging them here. A subtle line will appear between the pages to display the placement.</p>
<p><strong>SHOW PRICING/HIDE PRICING</strong> - This button will show or hide prices in your catalog. If you choose to show prices, they will display along with the product information below the images on the catalog pages. If you don&rsquo;t want to include prices in your catalog, choose hide prices.</p>
<p>Set price multiplier &ndash; The <strong>PRICING</strong> link includes a price multiplier option you can use to set custom pricing. With the multiplier, you can choose a factor ranging from 1x to 4x; prices in your catalog will be multiplied by the amount you choose.</p>
<p><strong>SAVE</strong> &ndash; Simply click or tap this button to save your catalog. (Your catalog is also saved any time you switch to another page or add a page).</p>
<h2>Page Editor Tools</h2>
<p><strong>PAGE LAYOUT OPTIONS</strong> - Click or tap the page icon on the left to choose from eight available layouts for the pages of your catalog. Each page layout has slots where you can drag artwork from the Art Selector.</p>
<p><strong>PRODUCT DETAILS</strong> - As you add artwork to each page, you&rsquo;ll notice space below for specific details about that product. As the page is created, product details will display in the space, including price if applicable and side mark.</p>
<h2>Art Selector Tools</h2>
<p>A "carousel" at the bottom of the page displays all the artwork you have saved to the Project that this catalog is part of. Choose artwork from the selector to drag to the open slots in pages in the Page Editor. You&rsquo;ll notice a visual cue to confirm that when you let go, the artwork will drop into the slot.</p>
<p>You can remove artwork from a slot by simply clicking on it again and dragging it out of the slot. You can place it in another slot or on another page.</p>
<p>The artwork remains in your Artwork Selector, even when placed on a catalog page so you can select it and place it again.</p>
                ',
            ],*/
        ];

        try {
            foreach ($cmsPages as $cmsPage) {
                $createCmsPages = $this->pageFactory->create()->load(
                    $cmsPage['identifier'],
                    'identifier'
                );

                if (!$createCmsPages->getId()) {
                    $createCmsPages->setData($cmsPage)->save();
                } else {
                    $createCmsPages->setContent($cmsPage['content'])->save();
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
