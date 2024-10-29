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

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Config\Model\Config\Factory;

/**
 * Class UpdatedCmsPages
 * @package Perficient\Base\Setup\Patch\Data
 */
class UpdatedCmsPages implements DataPatchInterface
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
        private readonly Factory $configFactory,
        private readonly BlockRepositoryInterface $blockRepository,
        private readonly PageFactory $pageFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->createCmsBlocks();
        $this->updateHomePage();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create CMS Bloges.
     */
    private function createCmsBlocks(): void
    {
        $cmsBlocks = [
            [
                'title' => 'Home hero banner',
                'identifier' => 'home_hero_banner',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; min-height: 700px; margin: 0px; padding: 0px;"><div class="pagebuilder-slider home-banner common-img-slider" data-content-type="slider" data-appearance="default" data-autoplay="false" data-autoplay-speed="2000" data-fade="false" data-infinite-loop="true" data-show-arrows="false" data-show-dots="true" data-element="main" style="min-height: 800px; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/banner1.jpg}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/banner1_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 0px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"><div class="copy-inner-container"><h2 class="title">Lillian August</h2>
 COLLECTION</div><div class="cms-content-important"><span style="font-size: 14px;"><a class="btn-link" tabindex="0" href="#" target="_blank" rel="noopener">VIEW THE COLLECTION</a></span></div></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/banner1.jpg}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/banner1_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 0px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"><div class="copy-inner-container"><h2 class="title">Lillian August</h2>
 COLLECTION</div><div class="cms-content-important"><span style="font-size: 14px;"><a class="btn-link" tabindex="0" href="#" target="_blank" rel="noopener">VIEW THE COLLECTION</a></span></div></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/banner1.jpg}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/banner1_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 0px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"><div class="copy-inner-container"><h2 class="title">Lillian August</h2>
 COLLECTION</div><div class="cms-content-important"><span style="font-size: 14px;"><a class="btn-link" tabindex="0" href="#" target="_blank" rel="noopener">VIEW THE COLLECTION</a></span></div></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/banner1.jpg}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/banner1_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 0px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"><div class="copy-inner-container"><h2 class="title">Lillian August</h2>
 COLLECTION</div><div class="cms-content-important"><span style="font-size: 14px;"><a class="btn-link" tabindex="0" href="#" target="_blank" rel="noopener">VIEW THE COLLECTION</a></span></div></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/banner1.jpg}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/banner1_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 0px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"><div class="copy-inner-container"><h2 class="title">Lillian August</h2>
 COLLECTION</div><div class="cms-content-important"><span style="font-size: 14px;"><a class="btn-link" tabindex="0" href="#" target="_blank" rel="noopener">VIEW THE COLLECTION</a></span></div></div></div></div></div></div></div><div data-content-type="slide" data-slide-name="" data-appearance="poster" data-show-button="never" data-show-overlay="never" data-element="main" style="margin: 0px;"><div data-element="empty_link"><div class="pagebuilder-slide-wrapper" data-background-images="{\&quot;desktop_image\&quot;:\&quot;{{media url=wysiwyg/banner1.jpg}}\&quot;,\&quot;mobile_image\&quot;:\&quot;{{media url=wysiwyg/banner1_mobile.jpg}}\&quot;}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="wrapper" style="background-position: left top; background-size: cover; background-repeat: no-repeat; border-style: none; border-width: 1px; border-radius: 0px;"><div class="pagebuilder-overlay pagebuilder-poster-overlay" data-overlay-color="" data-element="overlay" style="padding: 0px; background-color: transparent;"><div class="pagebuilder-poster-content"><div data-element="content"><div class="copy-inner-container"><h2 class="title">Lillian August</h2>
 COLLECTION</div><div class="cms-content-important"><span style="font-size: 14px;"><a class="btn-link" tabindex="0" href="#" target="_blank" rel="noopener">VIEW THE COLLECTION</a></span></div></div></div></div></div></div></div></div></div></div>',
            ],
            [
                'title' => 'The Source for Art slider',
                'identifier' => 'source-for-Art-slider',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div class="art-inner-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 140px; padding: 0px;"><div class="top-heading" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 5px; padding: 0px;"><h1>The Source for Art</h1>
 <p>Hand-crafted, domestically made, unique, highest quality, and breadth of selection describes the art created by Wendover Art Group.</p>
 <a href="#" target="_blank" rel="noopener">Explore Artwork</a></div><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">
 <div class="carousel source-art-section">
 <div class="image-container">
 <img src="{{media url=wysiwyg/residential.png}}" alt="" role="presentation" />
 <div class="title"><a href="/residential">Residential</a></div>
 </div>

  <div class="image-container">
 <img src="{{media url=wysiwyg/hospitality.png}}" alt="" role="presentation" />
 <div class="title"><a href="/hospitality">Hospitality</a></div>
 </div >

 <div class="image-container">
 <img src="{{media url=wysiwyg/healthcare.png}}" alt=""  role="presentation" />
 <div class="title"><a href="/healthcare">Healthcare</a></div>
 </div >

 <div class="image-container">
 <img src="{{media url=wysiwyg/senior-living.png}}" alt="" role="presentation" />
 <div class="title"><a href="/senior-living">Senior Living</a></div>
 </div >

 <div class="image-container">
 <img src="{{media url=wysiwyg/senior-living.png}}" alt="" role="presentation" />
 <div class="title"><a href="/workspace">Workspace</a></div>
 </div >

 <div class="image-container">
 <img src="{{media url=wysiwyg/senior-living.png}}" alt="" role="presentation"/>
 <div class="title"><a href="/multi-family">Multi-Family </a></div>
 </div >

 <div class="image-container">
 <img src="{{media url=wysiwyg/senior-living.png}}" alt="" role="presentation" />
 <div class="title"><a href="/art-consulting">Art Consulting</a></div>
 </div >

   <div class="image-container">
 <img src="{{media url=wysiwyg/senior-living.png}}" alt="" role="presentation" />
 <div class="title"><a href="/brand-schemes">Brand Schemes</a></div>
 </div >

 </div>

 <div class="slider"></div>


 	</div></div></div>',
            ],
            [
                'title' => 'customer service static link section',
                'identifier' => 'customer-service-static-link-section',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><ul>
 <li>
 <div class="icon-section"><img src="{{media url=wysiwyg/customer_service_icon.png}}" role="presentation" alt="" /></div>
 <div class="static-link">  <a href="{{store url=""}}customer-service" class="customer-service">Customer Service</a> </div>
 </li>
 <li>
 <div class="icon-section"><img src="{{media url=wysiwyg/about_us_icon.png}}" role="presentation"  alt="" /> </div>
 <div class="static-link"> <a href="{{store url=""}}about-us" class="about-us">About Us</a> </div>
 </li>
 <li>
 <div class="icon-section"><img src="{{media url=wysiwyg/careers_icon.png}}"role="presentation"  alt="" /> </div>
 <div class="static-link">  <a href="{{store url=""}}careers" class="careers">Careers</a> </div>
 </li>
 </ul>
  </div></div></div>',
            ],
            [
                'title' => 'Footer',
                'identifier' => 'footer_static_links',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div class="page-footer" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 33.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div class="address-section" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><a href="/" aria-label="Wendover Art Group" class="logo-footer"><img src="{{media url=wysiwyg/logo_footer.png}}" alt="Wendover Art Group Logo" /></a>
 <h2 class="title">Wendover Art Group</h2>
 <address>
 <p>6465 126th Avenue North <br role="presentation"/>Largo, FL 33773</p>
 </address>

 <a href="tel:+888-743-9232" class="phone-no">888-743-9232</a>


 {{block class="Magento\Theme\Block\Html\Footer" name="copyright" template="Magento_Theme::html/copyright.phtml"}}


 </div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 66.6667%; margin: 0px; padding: 0px; align-self: stretch;"><div class="footer-right-block" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="footer-right-inner">
 <div class="footer-columns one">
     <div class="footer-block-heading-container">
          <h3 class="footer-block-heading">artwork by category</h3>
     </div>
     <div class="block-container">
         <ul class="block-content" role="list">
             <li role="listitem"><a aria-label="Floral Category" href="{{store url=""}}browse/categories/floral">Floral</a></li>
             <li role="listitem"><a aria-label="Animals Category" href="{{store url=""}}browse/categories/animals">Animals</a></li>
             <li role="listitem"><a aria-label="For Kids Category" href="{{store url=""}}browse/categories/for-kids">For Kids</a></li>
             <li role="listitem"><a aria-label="Architecture Category" href="{{store url=""}}browse/categories/architecture">Architecture</a></li>
         </ul>

         <ul class="block-content" role="list">
             <li role="listitem"><a aria-label="Nature Category" href="{{store url=""}}browse/categories/nature">Nature</a></li>
             <li role="listitem"><a aria-label="Still Life Category" href="{{store url=""}}browse/categories/still-life">Still Life</a></li>
             <li role="listitem"><a aria-label="Cultural Category" href="#">Cultural</a></li>
             <li role="listitem"><a aria-label="Typography Category" href="{{store url=""}}browse/categories/typography">Typography</a></li>
         </ul>

         <ul class="block-content" role="list">
             <li role="listitem"><a aria-label="Coastal Category" href="{{store url=""}}browse/categories/coastal">Coastal</a></li>
             <li role="listitem"><a aria-label="Decorative Category" href="{{store url=""}}browse/categories/decorative">Decorative</a></li>
             <li role="listitem"><a aria-label="Vintage Category" href="{{store url=""}}browse/categories/vintage">Vintage</a></li>
             <li role="listitem"><a aria-label="Abstract Category" href="{{store url=""}}browse/categories/abstract">Abstract</a></li>
         </ul>

         <ul class="block-content" role="list">
             <li role="listitem"><a aria-label="Europe Category" href="{{store url=""}}browse/categories/europe">Europe</a></li>
             <li role="listitem"><a aria-label="Figurative Category" href="{{store url=""}}browse/categories/figurative">Figurative</a></li>
             <li role="listitem"><a aria-label="Men\'s Club Category" href="#">Men\'s Club</a></li>
             <li role="listitem"><a aria-label="Landscape Category" href="{{store url=""}}browse/categories/landscape">Landscape</a></li>
         </ul>
         <ul class="block-content" role="list">
             <li role="listitem"><a aria-label="Photography Category" href="{{store url=""}}browse/categories/photography">Photography</a></li>
             <li role="listitem"><a aria-label="Lodge Category" href="{{store url=""}}browse/categories/lodge">Lodge</a></li>
         </ul>
     </div>
 </div>
 <div class="footer-columns two">
      <div class="footer-block-heading-container">
        <h3 class="footer-block-heading">One Wendover</h3>
     </div>
     <div class="block-container">
         <ul class="block-content" role="list">
             <li role="listitem"><a href="{{store url=""}}overview">Overview</a></li>
             <li role="listitem"><a href="{{store url=""}}history">History</a></li>
             <li role="listitem"><a href="{{store url=""}}team">Team</a></li>
             <li role="listitem"><a href="{{store url=""}}in-the-news">In The News</a></li>
             <li role="listitem"><a href="{{store url=""}}careers">Careers</a></li>
         </ul>
         <ul class="block-content" role="list">
             <li role="listitem"><a href="{{store url=""}}locations">Locations</a></li>
             <li role="listitem"><a href="{{store url=""}}community-outreach">Community Outreach</a></li>
             <li role="listitem"><a href="#">Newsletter</a></li>
             <li role="listitem"><a href="{{store url=""}}sustainability">Sustainability</a></li>
         </ul>
     </div>
 </div>
 <div class="footer-columns three">
      <div class="footer-block-heading-container">
        <h3 class="footer-block-heading">Customer Service</h3>
     </div>
     <div class="block-container">
         <ul class="block-content" role="list">
             <li role="listitem"><a href="{{store url=""}}contact-us">Contact Us</a></li>
             <li role="listitem"><a href="{{store url=""}}faq">FAQ</a></li>
             <li role="listitem"><a href="{{store url=""}}shipping-and-return-policy">Shipping/Return Policy</a></li>
             <li role="listitem"><a href="{{store url=""}}privacy-policy">Privacy Policy</a></li>
             <li role="listitem"><a href="{{store url=""}}terms-of-use">Terms of Use</a></li>
         </ul>
     </div>

 </div>
 <div class="footer-columns four">
     <div class="footer-block-heading-container">
         <h3 class="footer-block-heading">Artists & Vendors</h3>
     </div>
     <div class="block-container">
         <ul class="block-content" role="list">
             <li role="listitem"><a href="#">Artist Submission</a></li>
             <li role="listitem"><a href="#">Vendor Submission</a></li>
         </ul>
     </div>
 </div>
 </div></div></div></div></div></div>',
            ],
            [
                'title' => 'footer newsletter text',
                'identifier' => 'footer-newsletter-text',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="newsletter-left-section" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="sub-heading"><p><strong>SIGN UP</strong> to receive exclusive orem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt labore dolore magna.</p></div>
  </div></div></div>',
            ],
        ];

        try {
            foreach ($cmsBlocks as $cmsBlock) {
                $blockFactory = $this->blockRepository->getById($cmsBlock['identifier']);
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
     * Update Home page.
     */
    private function updateHomePage(): void
    {
        $homePage = [
            'title' => 'Wendover Art Group',
            'identifier' => 'home',
            'stores' => [0],
            'is_active' => 1,
            'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; min-height: 700px; margin: 0px 0px 10px; padding: 0px;"><div class="home-hero-banner" data-content-type="block" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="20" type_name="CMS Static Block"}}</div><div class="source-art-container" data-content-type="block" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="21" type_name="CMS Static Block"}}</div><div class="inspire-container" data-content-type="block" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="32" type_name="CMS Static Block"}}</div><div class="location-container" data-content-type="block" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="33" type_name="CMS Static Block"}}</div><div class="services-static-block" data-content-type="block" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="22" type_name="CMS Static Block"}}</div></div></div>',
        ];

        $pageFactory = $this->pageFactory->create()->load($homePage['identifier'], 'identifier');
        if (!$pageFactory->getId()) {
            $pageFactory->setData($homePage)->save();
        } else {
            $pageFactory->setTitle($homePage['title'])->setContent($homePage['content'])->save();
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
