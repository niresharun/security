<?php
/**
 * This file is used to Update Footer Static Block
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
 * Class CmsFooterStaticBlockUpdate
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsFooterStaticBlockUpdate implements DataPatchInterface
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
        $this->updateFooterBlock();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Update Footer Static Block.
     */
    private function updateFooterBlock(): void
    {
        $footerBlock = [
                'title' => 'Footer',
                'identifier' => 'footer_static_links',
                'stores' => [0],
                'content' => '
            <div data-content-type="row" data-appearance="contained" data-element="main"><div class="page-footer" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; width: 33.3333%; margin: 0px; padding: 0px; align-self: stretch;"><div class="address-section" data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><a href="/" aria-label="Wendover Art Group" class="logo-footer"><img src="{{media url=wysiwyg/logo_footer.png}}" alt="Wendover Art Group Logo" /></a>
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
            <li role="listitem"><a href="#">Floral</a></li>
            <li role="listitem"><a href="#">Animals</a></li>
            <li role="listitem"><a href="#">For Kids</a></li>
            <li role="listitem"><a href="#">Architecture</a></li>
        </ul>

        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Nature</a></li>
            <li role="listitem"><a href="#">Still Life</a></li>
            <li role="listitem"><a href="#">Cultural</a></li>
            <li role="listitem"><a href="#">Typography</a></li>
        </ul>

        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Coastal</a></li>
            <li role="listitem"><a href="#">Decorative</a></li>
            <li role="listitem"><a href="#">Vintage</a></li>
            <li role="listitem"><a href="#">Abstract</a></li>
        </ul>

        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Europe</a></li>
            <li role="listitem"><a href="#">Figurative</a></li>
            <li role="listitem"><a href="#">Men\'s Club</a></li>
            <li role="listitem"><a href="#">Landscape</a></li>
        </ul>
        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Photography</a></li>
            <li role="listitem"><a href="#">Lodge</a></li>
        </ul>
    </div>
</div>
<div class="footer-columns two">
     <div class="footer-block-heading-container">
       <h3 class="footer-block-heading">One Wendover</h3>
    </div>
    <div class="block-container">
        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Overview</a></li>
            <li role="listitem"><a href="#">History</a></li>
            <li role="listitem"><a href="#">Team</a></li>
            <li role="listitem"><a href="#">In The News</a></li>
            <li role="listitem"><a href="#">Careers</a></li>
        </ul>
        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Locations</a></li>
            <li role="listitem"><a href="#">Community Outreach</a></li>
            <li role="listitem"><a href="#">Newsletter</a></li>
            <li role="listitem"><a href="#">Sustainability</a></li>
        </ul>
    </div>
</div>
<div class="footer-columns three">
     <div class="footer-block-heading-container">
       <h3 class="footer-block-heading">Customer Service</h3>
    </div>
    <div class="block-container">
        <ul class="block-content" role="list">
            <li role="listitem"><a href="/contact/">Contact Us</a></li>
            <li role="listitem"><a href="#">FAQ</a></li>
            <li role="listitem"><a href="/sales/guest/form/">Shipping/Return Policy</a></li>
            <li role="listitem"><a href="/terms-of-use">Privacy Policy</a></li>
            <li role="listitem"><a href="/terms-of-use">Terms of Use</a></li>
        </ul>
    </div>

</div>
<div class="footer-columns four">
    <div class="footer-block-heading-container">
        <h3 class="footer-block-heading">Artists &amp; Vendors</h3>
    </div>
    <div class="block-container">
        <ul class="block-content" role="list">
            <li role="listitem"><a href="#">Artist Submission</a></li>
            <li role="listitem"><a href="#">Vendor Submission</a></li>
        </ul>
    </div>
</div>
</div> </div></div></div></div></div>'];
        $updatePage = $this->blockRepository->getById($footerBlock['identifier']);
        if (!$updatePage->getId()) {
            $updatePage->setData($footerBlock)->save();
        } else {
            $updatePage->setContent($footerBlock['content'])->save();
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
