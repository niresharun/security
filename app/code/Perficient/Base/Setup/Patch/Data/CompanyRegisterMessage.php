<?php
/**
 * Static Block for Company registration Message.
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Soni <Sachin.Soni@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\ResourceModel\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CompanyRegisterMessage
 * @package Perficient\Base\Setup\Patch\Data
 */
class CompanyRegisterMessage implements DataPatchInterface
{
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     * @param Block $blockResource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly BlockFactory $blockFactory,
        private readonly Block $blockResource,
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $compayStaticBlock = [
            'title' => 'Company Registration Message',
            'identifier' => 'company_registeration_message',
            'content' => $this->getBlockContent(),
            'is_active' => 1,
            'stores' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
        ];

        $this->moduleDataSetup->startSetup();

        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        $block->setStoreId($compayStaticBlock['stores']);
        $this->blockResource->load($block, $compayStaticBlock['identifier'], BlockInterface::IDENTIFIER);
        if (!$block->getId()) {
            $block->setData($compayStaticBlock)->save();
        }

        $this->moduleDataSetup->endSetup();
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
    public static function getVersion(): string
    {
        return '2.3.4';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /*
     * @return string
     */
    private function getBlockContent(): string {
        $email = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        return '
             <div>
                   <h4>Only Customer Admin should register on the site.
                   If you are a Customer Employee, no need to register, you can contact the <a href="'.$email.'">Site Admin</a> to get the site access.</h4>
            </div>
        ';
    }
}
