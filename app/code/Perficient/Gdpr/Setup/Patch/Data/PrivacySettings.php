<?php
/**
 * Admin configuration of Gdpr
 * @category: Magento
 * @package: Perficient/Gdpr
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Nikhil Atkare<Nikhil.Atkare@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Gdpr
 */

namespace Perficient\Gdpr\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Patch script for gdpr config data
 */
class PrivacySettings implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final public const ENABLED_WENDOVER_CONTACT_BLOCK = 'amasty_gdpr/customer_access_control/show_wendover_contact_block';
    final public const WENDOVER_CONTACT_BLOCK = 'amasty_gdpr/customer_access_control/wendover_contact_block';
    final public const WENDOVER_DOWNLOAD = 'amasty_gdpr/customer_access_control/download';
    final public const WENDOVER_ANONYMIZE = 'amasty_gdpr/customer_access_control/anonymize';
	//const WENDOVER_DEL = 'amasty_gdpr/customer_access_control/delete';
	//const WENDOVER_OPT_OUT = 'amasty_gdpr/customer_access_control/consent_opting';
    final public const SCOPE_ID = 0;

    private array $configData = [
        self::ENABLED_WENDOVER_CONTACT_BLOCK => '1',
        self::WENDOVER_CONTACT_BLOCK => 'wendover_privacy_settings_gdpr',
		self::WENDOVER_DOWNLOAD => '0',
		self::WENDOVER_ANONYMIZE => '0',
		//self::WENDOVER_DEL => '0',
		//self::WENDOVER_OPT_OUT => '0'
    ];

    /**
     * ConfigData constructor.
     */
    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly BlockFactory $blockFactory
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        foreach($this->configData as $key=>$value){
            $this->configWriter->save($key, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        }
        $this->createPrivacySettingBlock();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * createPrivacySettingBlock
     */
    public function createPrivacySettingBlock() {

        $cmsBlocks = [
            [
                'title' => 'Wendover Privacy Settings GDPR',
                'identifier' => 'wendover_privacy_settings_gdpr',
                'stores' => [0],
                'is_active' => 1,
                'content' => '<p>If you would like to request a copy of your personal information on file, please email <a href="mailto:info@wendoverart.com">info@wendoverart.com</a>.  You will receive a CSV file via email within 3 business days.</p>',
            ],
        ];

        try {
            foreach ($cmsBlocks as $cmsBlock) {
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
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
