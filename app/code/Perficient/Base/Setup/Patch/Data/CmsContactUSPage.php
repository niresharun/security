<?php
/**
 * This file is used to create CMS Contact us page
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
 * Class CmscontactusPage
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsContactUSPage implements DataPatchInterface
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
        $this->createContactusPage();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create hospitality page.
     */
    private function createContactusPage(): void
    {
        $page =
            [
                'title' => 'Contact Us',
                'stores' => [0],
                'identifier' => 'contact-us',
                'page_layout' => 'cms-full-width',
                'is_active' => 1,
                'content' => '<div data-content-type="row" data-appearance="contained" data-element="main"><div class="contact-container" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><div class="amcform-form-title"><h1>Contact Us</h1></div>
{{widget type="Amasty\Customform\Block\Init" template="Amasty_Customform::init.phtml" form_id="6"}}</div></div></div>'];
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
