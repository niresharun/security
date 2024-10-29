<?php
/**
 * This file is used to Update Career Page
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

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class CareerPageUpdate
 * @package Perficient\Base\Setup\Patch\Data
 */
class CareerPageUpdate implements DataPatchInterface
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
        $this->updateCareerPage();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Update Topic page.
     */
    private function updateCareerPage(): void
    {

        $cmsCarrier =
            [
                'title' => 'Careers',
                'stores' => [0],
                'identifier' => 'careers',
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=careers/Image_Placeholder-min.png}}" alt="Image Careers at Wendover" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=careers/Image_Placeholder-min.png}}" alt="Image Careers at Wendover" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div class="career-banner-description" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading large-text-font">
       Careers At Wendover
</h1>

<p class="banner-description">"Our guest is to keep getting better at what we do. It\'s about the journey and what it takes to get there, not just the destination."</p></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="careers-landing-page" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/plp-vertical_1.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="brand-copy-section vertical" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Honor Counts Most</h2>
<div class="brand-inner-container">
<p>What is right is more important that who is right.</p>
<br role="presentation">
<p>At Wendover, our core values are the foundation of everything we do. Our culture is uniquely ours; we work harder and smarter than most and see possibilities for growth that others overlook. Our success as a company drives the tremendous opportunity for success that our team members enjoy. We are always eager to find new team members who are as enthusiastic about our values and the opportunity  they provide as we are. </p>
<a href="#" class="show-more-link">Show More</a>
</div>

</div><figure class="brand-foreground-image" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_231_1.jpg}}" alt="Image Honor Counts Most" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_231_1.jpg}}" alt="Image Honor Counts Most" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="landing-page-multislider landing-page-separation" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-info landing-page-info career-multislider-info" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Our Team MAkes the difference</h2>
<p>Our people are the heart of Wendover. We empower each other to succeed and challenge each other to grow. As long as we have the right team members, we will succeed no matter where the business takes us.</p><a href="http://www.wendoverart.com/careers/meet-the-team" aria-label="Get To Know Our Extraordinary Team opens in new window" target="_blank">Get To Know Our Extraordinary Team</a></div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 10px; align-self: stretch;"><div class="multislider-container" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="career-slick-wrapper">
<div class="item slide-item">
<img src="{{media url=wysiwyg/marriott.jpg}}" alt="" />
  <div><a href="#">JOSH HERNANDEZ</a><p>OPERATIONS ANALYST</p></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/wyndham.jpg}}" alt="" />
  <div><a href="#">MART PAT PEAD</a><p>ART RESOURCER</p></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/ihg.png}}" alt="" />
  <div><a href="#">RICHARD FORSYTH</a><p>PRESIDENT &amp; CEO</p></div>
</div>
<div class="item slide-item">
<img src="{{media url=wysiwyg/choice.jpg}}" alt="" />
  <div><a href="#">ERIN VICKERS</a><p>SENIOR ART CONSULTANT</p></div>
</div>

</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section career-mb-0 project-showcase-section-common" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">We live our values</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="project-showcase-container common-showcase-container">

<div class="image-container common-element-slick" >
<img src="{{media url=wysiwyg/project_showcase_1.png}}" alt="Honor Opportunities" tabindex="0" />
<div class="img-heading">Honor</div>
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Honor</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Honor" tabindex="0"></a>
</div>
</div>

<div class="image-container common-element-slick" >
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="Team Opportunities" tabindex="0"/>
<div class="img-heading">Team</div>
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Team</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Team" tabindex="0"></a>
</div>
</div>

<div class="image-container common-element-slick" >
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Excellence Opportunities" tabindex="0"/>
<div class="img-heading">Excellence</div>
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Excellence</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Excellence" tabindex="0"></a>
</div>
</div>

<div class="image-container common-element-slick" >
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="Smart Opportunities" tabindex="0" />
<div class="img-heading">Smart</div>
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Smart</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Smart" tabindex="0"></a>
</div>
</div>
</div>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="careers-landing-page careers-landing-page-right" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/plp-vertical_2.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div class="brand-copy-section vertical" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>JOIN THE TEAM</h2>
<div class="brand-inner-container">
<p>Your experience certianly counts, but what we value most is talent and an inner drive to be exceptional. At wendover, we set a high bar for ourselves. On the good, better, best scale, we\'re looking for the "BEST". By joining Wendover, you\'ll get to work with great people who share a work ethic and commitment to excellence that is unrivaled in our industry. If you push yourself beyond your comfort zone, seize hard challenges as opportunities for growth, and strive for nothing less than excellence, we\'d love to hear from you.  </p>

<a href="https://www.paycomonline.net/v4/ats/web.php/jobs?clientkey=F366BD52DD0B72FB8636474B55E11D18" aria-label="View All Openings opens in new window" target="_blank" class="show-more-link">View All Openings</a>
</div>
</div><figure class="brand-foreground-image" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/Image_231_1_1.jpg}}" alt="Image Join The Team" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/Image_231_1_1.jpg}}" alt="Image Join The Team" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><p class="banner-description career-description-info">One Wendover is meritocracy of talented people working toward one standard to be the best, supporting one another, and winning as a Team.</p></div></div></div>',
        ];

       $updatePage = $this->pageFactory->create()->load(
           $cmsCarrier['identifier'],
            'identifier'
       );
       if (!$updatePage->getId()) {
            $updatePage->setData($cmsCarrier)->save();
       } else {
            $updatePage->setContent($cmsCarrier['content'])->save();
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
