<?php
/**
 * Configure Social Share Icons
 * @category: Magento
 * @package: Perficient/SocialShare
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: purushottam rathi<purushottam.rathi@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_SocialShare
 */
declare(strict_types=1);

namespace Perficient\SocialShare\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for Social Share
 */
class SocialShareIconConfig implements DataPatchInterface
{
    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_GENERAL_TUMBLR_ENABLED = 'mpsocialshare/general/tumblr/enabled';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_GENERAL_TWITTER_ENABLED = 'mpsocialshare/general/twitter/enabled';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_GENERAL_ADD_MORE_SHARE_ENABLED = 'mpsocialshare/general/add_more_share/enabled';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_GENERAL_SHARE_COUNTER = 'mpsocialshare/general/share_counter';

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    private array $configData = [
        self::XML_PATH_SOCIAL_SHARE_GENERAL_TUMBLR_ENABLED => '0',
        self::XML_PATH_SOCIAL_SHARE_GENERAL_TWITTER_ENABLED => '0',
        self::XML_PATH_SOCIAL_SHARE_GENERAL_ADD_MORE_SHARE_ENABLED => '0',
        self::XML_PATH_SOCIAL_SHARE_GENERAL_SHARE_COUNTER => '0',
    ];
    /**
     * ConfigData constructor.
     */
    public function __construct(
        WriterInterface $configWriter,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->configWriter = $configWriter;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setupSocialShare();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Setup Social Share
     */
    public function setupSocialShare() {

        $scopeId = 0;
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

        foreach($this->configData as $config => $value){
            $this->configWriter->save($config, $value, $scope, $scopeId);
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
