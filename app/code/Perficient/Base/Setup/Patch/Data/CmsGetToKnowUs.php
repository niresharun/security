<?php
/**
 * This file is used to create CMS Get To Know Us page
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
 * Class CmsGetToKnowUs
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsGetToKnowUs implements DataPatchInterface
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
        $this->createGetToKnowUsPage();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create Get To Know Us Page  page.
     */
    private function createGetToKnowUsPage(): void
    {
        $page =
            [
                'title' => 'Get To Know Us',
                'stores' => [0],
                'identifier' => 'get-to-know-us',
                'is_active' => 1,
                'page_layout' => 'cms-full-width',
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><figure class="top-banner-common" data-content-type="image" data-appearance="full-width" data-element="main" style="margin: 0px; padding: 0px; border-style: none;"><img class="pagebuilder-mobile-hidden" src="{{media url=careers/Image_Placeholder-min.png}}" alt="Image Careers at Wendover" title="" data-element="desktop_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"><img class="pagebuilder-mobile-only" src="{{media url=careers/Image_Placeholder-min.png}}" alt="Image Careers at Wendover" title="" data-element="mobile_image" style="border-style: none; border-width: 1px; border-radius: 0px; max-width: 100%; height: auto;"></figure><div class="career-banner-description" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h1 class="page-heading large-text-font">
     Get To Know Us
</h1>
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="join-our-team-section" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 100px 0px 50px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 41.6667%; margin: 0px; padding: 25px 30px 35px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2>Get to Know Us</h2>
<p class="mb-25">Our people are the heart of Wendover. We empower each other to succeed and challenge each other to grow. As long as we have the right team members, we will succeed no matter where the business takes us.</p>
<p class="mb-25">Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod tempor invidunt ut labore dolore magna aliquyam erat, sed diam.</p>
<p class="mb-25">Here are just a few of the team members that are helping make Wendover the best in the industry.</p>
<a href="http://www.wendoverart.com/careers/join-the-team" aria-label="Join Our Team opens in new window" target="_blank" class="show-more-link">Join Our Team</a></div></div><div class="pagebuilder-column pagebuilder-right-section" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 58.3333%; margin: 0px; padding: 10px; align-self: stretch;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="tiles-wrapper">
	<ul class="tiles-container cms-tiles-container-popup" role="list">
		<li role="listitem" class="tiles-listitem modal-wrapper">
			<div class="content-item">
				<img src="{{media url=wysiwyg/inspire_1.jpg}}" class="image-main" tabindex="0" alt="Image Josh Hernandez Operations Analyst">
				<div class="image-text">
					<span class="main-heading">JOSH HERNANDEZ</span>
					<span class="copy-text">OPERATIONS ANALYST</span>
					<button class="search-btn" aria-label="Open Josh Hernandez Operations Analyst Popup"></button>
				</div>
				<div class="overlay">
				</div>
				<div class="overlay-content modal-inner-wrap" style="display: none;">
					<div class="wrap-overlay-content">
						<h1>JOSH HERNANDEZ - OPERATIONS ANALYST</h1>
						<button class="action-close" data-role="closeBtn" type="button" tabindex="-1"><span>Close</span></button>
						<div class="popup-content">
							<div class="image-section">
								<img src="{{media url=wysiwyg/inspire_1.jpg}}" tabindex="-1" alt="Josh Hernandez Operations Analyst">
							</div>
							<div class="text-section">
								<p><em>&amp;quot;Wendover has given me the opportunity to develop my career in a fast paced environment that constantly keeps me challenged. It is great to be a part of a team that is both competitive and supportive.&amp;quot;</em></p>
								<a href="http://www.wendoverart.com/careers/join-the-team" aria-label="Join Our Operations Analyst Team" class="view-more-link" target="_blank" tabindex="-1">Join Our Team</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</li>
		<li role="listitem" class="tiles-listitem modal-wrapper">
			<div class="content-item">
				<img src="{{media url=wysiwyg/inspire_2.jpg}}" class="image-main" tabindex="0" alt="Image Mart Pat Peat Art Resourcer">
				<div class="image-text">
					<span class="main-heading">MART PAT PEAD</span>
					<span class="copy-text">ART RESOURCER</span>
					<button class="search-btn" aria-label="Open Josh Hernandez Operations Analyst Popup"></button>
				</div>
				<div class="overlay">
				</div>
				<div class="overlay-content modal-inner-wrap" style="display: none;">
					<div class="wrap-overlay-content">
						<h1>MART PAT PEAD - ART RESOURCER</h1>
						<button class="action-close" data-role="closeBtn" type="button" tabindex="-1"><span>Close</span></button>
						<div class="popup-content">
							<div class="image-section">
								<img src="{{media url=wysiwyg/inspire_2.jpg}}" tabindex="-1" alt="Mart Pat Peat Art Resourcer">
							</div>
							<div class="text-section">
								<p><em>&amp;quot;With a background in Studio Art, Art History &amp; Writing, the position of Art Resourcer is rare &amp; exceptional in how it has allowed me to combine my passions and multiple disciplines into my everyday tasks. It poses new and exciting opportunities for professional growth while also fostering a career centered in art.&amp;quot;</em></p>
								<a href="http://www.wendoverart.com/careers/join-the-team" aria-label="Join Our Art Resourcer Team" class="view-more-link" target="_blank" tabindex="-1">Join Our Team</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</li>
		<li role="listitem" class="tiles-listitem modal-wrapper">
			<div class="content-item">
				<img src="{{media url=wysiwyg/inspire_4.jpg}}" class="image-main" tabindex="0" alt="Image Richard Forsyth President &amp; CEO">
				<div class="image-text">
					<span class="main-heading">RICHARD FORSYTH</span>
					<span class="copy-text">PRESIDENT &amp; CEO</span>
					<button class="search-btn" aria-label="Open Josh Hernandez Operations Analyst Popup"></button>
				</div>
				<div class="overlay">
				</div>
				<div class="overlay-content modal-inner-wrap" style="display: none;">
					<div class="wrap-overlay-content">
						<h1>RICHARD FORSYTH - PRESIDENT &amp; CEO</h1>
						<button class="action-close" data-role="closeBtn" type="button" tabindex="-1"><span>Close</span></button>
						<div class="popup-content">
							<div class="image-section">
								<img src="{{media url=wysiwyg/inspire_4.jpg}}" tabindex="-1" alt="Richard Forsyth President &amp; CEO">
							</div>
							<div class="text-section">
								<p><em>&amp;quot;It is a daily thrill to lead this dynamic and talented team towards our unified goal to be the industry’s best. Wendover is about great people working collaboratively to serve our customers; I am fortunate to be a part of it.&amp;quot;</em></p>
								<a href="http://www.wendoverart.com/careers/join-the-team" aria-label="Join Our President &amp; CEO Team" class="view-more-link" target="_blank" tabindex="-1">Join Our Team</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</li>
		<li role="listitem" class="tiles-listitem modal-wrapper">
			<div class="content-item">
				<img src="{{media url=wysiwyg/inspire_5.jpg}}" class="image-main" tabindex="0" alt="Image Erin Vickers Senior Art Consultant">
				<div class="image-text">
					<span class="main-heading">ERIN VICKERS</span>
					<span class="copy-text">SENIOR ART CONSULTANT</span>
					<button class="search-btn" aria-label="Open Josh Hernandez Operations Analyst Popup"></button>
				</div>
				<div class="overlay">
				</div>
				<div class="overlay-content modal-inner-wrap" style="display: none;">
					<div class="wrap-overlay-content">
						<h1>ERIN VICKERS - SENIOR ART CONSULTANT</h1>
						<button class="action-close" data-role="closeBtn" type="button" tabindex="-1"><span>Close</span></button>
						<div class="popup-content">
							<div class="image-section">
								<img src="{{media url=wysiwyg/inspire_5.jpg}}" tabindex="-1" alt="Erin Vickers Senior Art Consultant">
							</div>
							<div class="text-section">
								<p><em>&amp;quot;Wendover is built on partnerships, innovation, and teamwork. It feels amazing to work with such a talented group of creative people who are committed to excellence and who have a pride and passion for what we do.&amp;quot;</em></p>
								<a href="http://www.wendoverart.com/careers/join-the-team" aria-label="Join Our Senior Art Consultant Team" class="view-more-link" target="_blank" tabindex="-1">Join Our Team</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</li>
	</ul>
</div></div></div></div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div class="project-showcase-section career-mb-0 project-showcase-section-common" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="common-title" data-content-type="text" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><h2 id="G1O6R6N">We live our values</h2></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="project-showcase-container common-showcase-container gettoknow-showcase-container">

<div class="image-container common-element-slick" tabindex="0" >
<img  src="{{media url=wysiwyg/project_showcase_1.png}}" alt=""  />
<div class="img-heading">Honor</div>
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Honor</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Honor" tabindex="0"></a>
</div>
</div>


<div class="image-container common-element-slick" tabindex="0" >
<img src="{{media url=wysiwyg/project_showcase_2.png}}" alt="" />
<div class="img-heading">Team</div>
 <div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Team</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Team" tabindex="0"></a>
</div>
</div>


<div class="image-container common-element-slick" tabindex="0" >
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt="" />
<div class="img-heading">Excellence</div>
<div class="overlay"></div>
<div class="overlay-content" aria-hidden="true">
<div class="heading">Excellence</div>
<p>We win as a Team. Lorem ipsum dolor sit amet, consetetur sadipscing elitr diam nonumy eirmod.</p>
<p>Learn more about our opportunities.</p>
<a href="#" aria-label="Learn More about Excellence" tabindex="0"></a>
</div>
</div>


<div class="image-container common-element-slick" tabindex="0" >
<img src="{{media url=wysiwyg/project_showcase_4.png}}" alt=""/>
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
</div></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><p class="banner-description career-description-info pt-75">One Wendover is meritocracy of talented people working toward one standard to be the best, supporting one another, and winning as a Team.</p></div></div></div>
                '];
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
