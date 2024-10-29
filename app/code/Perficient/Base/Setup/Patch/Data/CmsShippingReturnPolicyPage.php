<?php
/**
 * This file is used to create CMS Shipping and Return Policy
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
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Class CmsShippingReturnPolicyPage
 * @package Perficient\Base\Setup\Patch\Data
 */
class CmsShippingReturnPolicyPage implements DataPatchInterface
{
    /**
     * Scope Id
     */
    final const SCOPE_ID = '0';

    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Factory $configFactory
     * @param PageFactory $pageFactory
     * @param WriterInterface $configWriter
     * @param ConfigInterface $resourceConfig
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Factory $configFactory,
        private readonly PageFactory $pageFactory,
        private readonly WriterInterface $configWriter,
        private readonly ConfigInterface $resourceConfig
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->CmsShippingReturnPolicyPage();
        $this->UpdateShippingPolicyContent();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Update Shipping Policy Content
     */
    private function UpdateShippingPolicyContent()
    {
        $this->resourceConfig->saveConfig(
            'shipping/shipping_policy/shipping_policy_content',
            'All orders are FOB Largo, Florida. Should they apply, freight charges will be added to your invoice at time of shipping. We will do our best to accommodate special instructions and delivery requests. Depending on order size and the overall size of the product ordered, Wendover will either ship your order via commercial carrier (on pallets) or via UPS Ground (parcel). <a href="/shipping-and-return-policy" aria-label="Shipping Policy">Shipping Policy</a>',
            'default',
            self::SCOPE_ID
        );
    }

    /**
     * Create hospitality page.
     */
    private function CmsShippingReturnPolicyPage()
    {
        $page = $this->pageFactory->create();
        $page->setTitle(__('Shipping and Return Policy'))
            ->setIdentifier('shipping-and-return-policy')
            ->setIsActive(true)
            ->setPageLayout('1column')
            ->setStores([0])
            ->setContent('<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="html" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;"><p>All orders are FOB Largo, Florida. Should they apply, freight charges will be added to your invoice at time of shipping. We will do our best to accommodate special instructions and delivery requests. Depending on order size and the overall size of the product ordered, Wendover will either ship your order via commercial carrier (on pallets) or via UPS Ground (parcel).
</p></div></div></div>')
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
