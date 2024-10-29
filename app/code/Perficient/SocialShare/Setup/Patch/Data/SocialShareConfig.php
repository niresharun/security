<?php
/**
 * Configure Social Share
 * @category: Magento
 * @package: Perficient/SocialShare
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
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
class SocialShareConfig implements DataPatchInterface
{
    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_FLOAT_APPLY_FOR = 'mpsocialshare/float/apply_for';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_FLOAT_SELECT_PAGE = 'mpsocialshare/float/select_page';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_FLOAT_CMS_PAGE = 'mpsocialshare/float/cms_page';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_GENERAL_LINKED_IN_ENABLED = 'mpsocialshare/general/linkedin/enabled';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_GENERAL_FACEBOOK_MESSENGER_ENABLED = 'mpsocialshare/general/facebook_messenger/enabled';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_MODULE_SUBSCRIBE = 'mpsocialshare/module/subscribe';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_MODULE_CREATE = 'mpsocialshare/module/create';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_MODULE_ACTIVE = 'mpsocialshare/module/active';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_MODULE_PRODUCT_KEY = 'mpsocialshare/module/product_key';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_MODULE_EMAIL = 'mpsocialshare/module/email';

    /**
     * @const string
     */
    final public const XML_PATH_SOCIAL_SHARE_MODULE_NAME = 'mpsocialshare/module/name';

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    private array $configData = [
        self::XML_PATH_SOCIAL_SHARE_FLOAT_APPLY_FOR => 'select_pages',
        self::XML_PATH_SOCIAL_SHARE_FLOAT_SELECT_PAGE => null,
        self::XML_PATH_SOCIAL_SHARE_FLOAT_CMS_PAGE => null,
        self::XML_PATH_SOCIAL_SHARE_GENERAL_LINKED_IN_ENABLED => '0',
        self::XML_PATH_SOCIAL_SHARE_GENERAL_FACEBOOK_MESSENGER_ENABLED => '0',
        self::XML_PATH_SOCIAL_SHARE_MODULE_SUBSCRIBE => '1',
        self::XML_PATH_SOCIAL_SHARE_MODULE_CREATE => '1',
        self::XML_PATH_SOCIAL_SHARE_MODULE_ACTIVE => '1',
        self::XML_PATH_SOCIAL_SHARE_MODULE_PRODUCT_KEY => 'NRZN7S2MHSV3HAGLP0BUKQXYJX5NSE5MJRI2UU1Y',
        self::XML_PATH_SOCIAL_SHARE_MODULE_EMAIL => 'joe.estaling@wendoverart.com',
        self::XML_PATH_SOCIAL_SHARE_MODULE_NAME => 'Joe'
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
