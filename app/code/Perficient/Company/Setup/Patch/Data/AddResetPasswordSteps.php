<?php
/**
 * Reset password steps block.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 * */
declare(strict_types=1);

namespace Perficient\Company\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Class AddResetPasswordSteps
 * @package Perficient\Checkout\Setup\Patch\Data
 */
class AddResetPasswordSteps implements DataPatchInterface, PatchVersionInterface
{
    /**
     * AddResetPasswordSteps constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageFactory $blockFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly BlockFactory             $blockFactory
    )
    {
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
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $resetPasswordSteps = [
            'title' => 'Reset Password Steps',
            'identifier' => 'reset_password_steps',
            'content' => '<div class="cms-reset-password">Steps to reset password goes here</div>',
            'is_active' => 1,
            'stores' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
        ];

        $this->moduleDataSetup->startSetup();

        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory->create();
        $block->setData($resetPasswordSteps)->save();
        $this->moduleDataSetup->endSetup();
    }


    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
