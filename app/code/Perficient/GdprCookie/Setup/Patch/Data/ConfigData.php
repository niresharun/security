<?php
/**
 * Admin configuration of Gdpr Cookie
 * @category: Magento
 * @package: Perficient/GdprCookie
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde<trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_GdprCookie
 */

namespace Perficient\GdprCookie\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr cookie config data
 */
class ConfigData implements DataPatchInterface
{

    /**#@+
     * Constants defined for xpath of system configuration
     */
    final public const COOKIE_POLICY_BAR = 'cookie_policy/bar';

    final public const COOKIE_POLICY_BAR_TYPE = 'cookie_bar_customisation/cookies_bar_style';

    final public const CONFIRMATION_BAR_TEXT = 'cookie_policy/confirmation_bar_text';

    final public const COOKIE_WEBSITE_INTERACTION = 'cookie_policy/website_interaction';

    final public const FIRST_VISIT_SHOW = 'cookie_policy/first_visit_show';

    final public const ALLOWED_URLS = 'cookie_policy/allowed_urls';

    final public const SETTINGS_PAGE = 'cookie_policy/cms_to_show';

    final public const COOKIE_POLICY_BAR_VISIBILITY = 'cookie_policy/bar_visibility';

    final public const COOKIE_POLICY_BAR_COUNTRIES = 'cookie_policy/bar_countries';

    final public const AUTO_CLEAR_LOG_DAYS = 'cookie_policy/auto_cleaning_days';

    final public const BACKGROUND_BAR_COLLOR = 'cookie_bar_customisation/background_color_cookies';

    final public const BUTTONS_BAR_COLLOR = 'cookie_bar_customisation/buttons_color_cookies';

    final public const TEXT_BAR_COLLOR = 'cookie_bar_customisation/text_color_cookies';

    final public const LINK_BAR_COLLOR = 'cookie_bar_customisation/link_color_cookies';

    final public const BUTTONS_TEXT_BAR_COLLOR = 'cookie_bar_customisation/buttons_text_color_cookies';

    final public const COOKIE_BAR_LOCATION = 'cookie_bar_customisation/cookies_bar_location';

    final public const BUTTON_BAR_SAVE_COLOR = 'cookie_bar_customisation/buttons_color_cookies_save';

    final public const BUTTON_SAVE_BAR_TEXT_COLOR = 'cookie_bar_customisation/buttons_text_color_cookies_save';

    final public const BUTTON_SETTINGS_BAR_TEXT_COLOR = 'cookie_bar_customisation/buttons_text_color_cookies_settings';

    final public const BUTTON_BAR_SETTINGS_COLOR = 'cookie_bar_customisation/buttons_color_cookies_settings';

    final public const HEADER_TEXT_BAR_COLOR = 'cookie_bar_customisation/header_text_color_cookies';

    final public const DESCRIPTION_TEXT_BAR_COLOR = 'cookie_bar_customisation/description_text_color_cookies';

    final public const PATH_PREFIX = 'amasty_gdprcookie/';

    final public const SCOPE_ID = 0;

    final public const  COOKIE_POLICY_BAR_VALUE = 1;

    final public const  FIRST_VISIT_SHOW_VALUE = 1;

    final public const  COOKIE_WEBSITE_INTERACTION_VALUE = 0;

    final public const  ALLOWED_URLS_VALUE = 'privacy-policy-cookie-restriction-mode';

    final public const  SETTINGS_PAGE_VALUE = 'cookie-settings';

    final public const  COOKIE_POLICY_BAR_VISIBILITY_VALUE = 0;

    final public const  AUTO_CLEAR_LOG_DAYS_VALUE = 180;

    final public const  COOKIE_POLICY_BAR_TYPE_VALUE = 1;

    final public const  COOKIE_BAR_LOCATION_VALUE = 0;

    final public const  BACKGROUND_BAR_COLLOR_VALUE = '#FFFFFF';

    final public const  TEXT_BAR_COLLOR_VALUE = '#000000';

    final public const  HEADER_TEXT_BAR_COLOR_VALUE = '#000000';

    final public const  DESCRIPTION_TEXT_BAR_COLOR_VALUE = '#000000';

    final public const  BUTTONS_BAR_COLLOR_VALUE = '#A82B19';

    final public const  BUTTONS_TEXT_BAR_COLLOR_VALUE = '#FFFFFF';

    final public const  BUTTON_BAR_SETTINGS_COLOR_VALUE = '#A82B19';

    final public const  BUTTON_SETTINGS_BAR_TEXT_COLOR_VALUE = '#FFFFFF';

    final public const  LINK_BAR_COLLOR_VALUE = '#A82B19';

    final public const  BUTTON_BAR_SAVE_COLOR_VALUE = '#A82B19';

    final public const  BUTTON_SAVE_BAR_TEXT_COLOR_VALUE = '#FFFFFF';


    /**#@-*/

    private array $speciedCountries = ['AF','AX','DZ','AS','AD','IN','US'];

    private array $configData = [
        self::PATH_PREFIX . self::COOKIE_POLICY_BAR => self::COOKIE_POLICY_BAR_VALUE,
        self::PATH_PREFIX . self::FIRST_VISIT_SHOW => self::FIRST_VISIT_SHOW_VALUE,
        self::PATH_PREFIX . self::COOKIE_WEBSITE_INTERACTION => self::COOKIE_WEBSITE_INTERACTION_VALUE,
        self::PATH_PREFIX . self::ALLOWED_URLS => self::ALLOWED_URLS_VALUE,
        self::PATH_PREFIX . self::SETTINGS_PAGE => self::SETTINGS_PAGE_VALUE,
        self::PATH_PREFIX . self::COOKIE_POLICY_BAR_VISIBILITY => self::COOKIE_POLICY_BAR_VISIBILITY_VALUE,
        self::PATH_PREFIX . self::AUTO_CLEAR_LOG_DAYS => self::AUTO_CLEAR_LOG_DAYS_VALUE,
        self::PATH_PREFIX . self::COOKIE_POLICY_BAR_TYPE => self::COOKIE_POLICY_BAR_TYPE_VALUE,
        self::PATH_PREFIX . self::COOKIE_BAR_LOCATION => self::COOKIE_BAR_LOCATION_VALUE,
        self::PATH_PREFIX . self::BACKGROUND_BAR_COLLOR => self::BACKGROUND_BAR_COLLOR_VALUE,
        self::PATH_PREFIX . self::TEXT_BAR_COLLOR => self::TEXT_BAR_COLLOR_VALUE,
        self::PATH_PREFIX . self::HEADER_TEXT_BAR_COLOR => self::HEADER_TEXT_BAR_COLOR_VALUE,
        self::PATH_PREFIX . self::DESCRIPTION_TEXT_BAR_COLOR => self::DESCRIPTION_TEXT_BAR_COLOR_VALUE,
        self::PATH_PREFIX . self::BUTTONS_BAR_COLLOR => self::BUTTONS_BAR_COLLOR_VALUE,
        self::PATH_PREFIX . self::BUTTONS_TEXT_BAR_COLLOR => self::BUTTONS_TEXT_BAR_COLLOR_VALUE,
        self::PATH_PREFIX . self::BUTTON_BAR_SETTINGS_COLOR => self::BUTTON_BAR_SETTINGS_COLOR_VALUE,
        self::PATH_PREFIX . self::BUTTON_SETTINGS_BAR_TEXT_COLOR => self::BUTTON_SETTINGS_BAR_TEXT_COLOR_VALUE,
        self::PATH_PREFIX . self::LINK_BAR_COLLOR => self::LINK_BAR_COLLOR_VALUE,
        self::PATH_PREFIX . self::BUTTON_BAR_SAVE_COLOR => self::BUTTON_BAR_SAVE_COLOR_VALUE,
        self::PATH_PREFIX . self::BUTTON_SAVE_BAR_TEXT_COLOR => self::BUTTON_SAVE_BAR_TEXT_COLOR_VALUE

    ];


    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

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
        foreach($this->configData as $key=>$value){
            $this->configWriter->save($key, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        }
        $this->setCookiePolicyBarCountries();
        $this->setConfirmationBarText();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Cookie Bar Text
     */
    public function setConfirmationBarText() {
        $pathConfirmationBarText = self::PATH_PREFIX . self::CONFIRMATION_BAR_TEXT;
        $valueConfirmationBarText = $this->getConfirmationBarText();
        $this->configWriter->save($pathConfirmationBarText, $valueConfirmationBarText, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Display for Specific Countries
     */
    public function setCookiePolicyBarCountries() {
        $pathCookiePolicyBarCountries = self::PATH_PREFIX . self::COOKIE_POLICY_BAR_COUNTRIES;
        $valueCookiePolicyBarCountries = implode(',',$this->speciedCountries);
        $this->configWriter->save($pathCookiePolicyBarCountries, $valueCookiePolicyBarCountries, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    /**
     * Get Cookie Bar Text Content
     * @return string
     */
    private function getConfirmationBarText() {
        $barTextContent = <<<EOT
 We use cookies to help improve our services, make personal offers, and enhance your experience. If you do not accept optional cookies below, your experience may be affected. If you want to know more, please read the <a href="privacy-policy-cookie-restriction-mode" title="Cookie Policy" target="_blank">Cookie Policy</a>. We use cookies to improve our services, make personal offers, and enhance your experience. If you do not accept optional cookies below, your experience may be affected. If you want to know more, please, read the <a href="privacy-policy-cookie-restriction-mode" title="Cookie Policy" target="_blank">Cookie Policy</a>.
EOT;

        return $barTextContent;
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
