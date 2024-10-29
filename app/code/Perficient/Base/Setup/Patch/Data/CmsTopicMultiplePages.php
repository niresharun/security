<?php
/**
 * This file is used to create Multiple CMS pages
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

use Exception;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class CmsTopicMultiplePages
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsTopicMultiplePages implements DataPatchInterface
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
     * @throws Exception
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->createTopicCmsMultiplePages();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create Multiple Topic pages.
     * @throws Exception
     */
    private function createTopicCmsMultiplePages(): void
    {
        $cmsPages = [
            [
                'title' => 'Atlanta',
                'identifier' => 'atlanta',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            [
                'title' => 'Dallas',
                'identifier' => 'dallas',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            [
                'title' => 'Largo',
                'identifier' => 'largo',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            [
                'title' => 'Las Vegas',
                'identifier' => 'las-vegas',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            [
                'title' => 'High Point',
                'identifier' => 'high-point',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            [
                'title' => 'Atlanta Art Consulting Office',
                'identifier' => 'atlanta-art-consulting-office',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            [
                'title' => 'Custom Design Projects',
                'identifier' => 'custom-design-projects',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Overview',
                'identifier' => 'overview',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'History',
                'identifier' => 'history',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Team',
                'identifier' => 'team',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'In The News',
                'identifier' => 'in-the-news',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Careers',
                'identifier' => 'careers',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Community Outreach',
                'identifier' => 'community-outreach',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'FAQ',
                'identifier' => 'faq',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Privacy Policy',
                'identifier' => 'privacy-policy',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Shipping/Return Policy',
                'identifier' => 'shipping-return-policy',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
            ['title' => 'Projects',
                'identifier' => 'projects',
                'stores' => [0],
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_Placeholder-min.png}}" alt="Image Art Consulting " title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading landing-page-title">
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
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>',
            ],
        ];

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
