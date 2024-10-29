<?php
/**
 * @author Perficient Team
 * @copyright Copyright (c) 2021 Perficient
 * @package Perficient_Gdpr
 */

namespace Perficient\Gdpr\Plugin\Block;

use Amasty\Gdpr\Model\Config;
use Amasty\Gdpr\Model\Consent\DataProvider\PrivacySettingsDataProvider;
use Amasty\Gdpr\Model\ConsentLogger;
use Magento\Customer\Block\Account\Navigation;
use Magento\Customer\Block\Account\SortLinkInterface as M22LinkClass;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Link\Current as M21LinkClass;
use Magento\Framework\View\Element\Html\Links;

class AccountLinkPlugin extends \Amasty\Gdpr\Block\AccountLinkPlugin
{
    final public const SORT_ORDER = 224;
    final public const INSERT_AFTER = 'customer-account-navigation-account-edit-link';
    final public const LINK_BLOCK_NAME = 'customer-account-amasty-gdpr-settings';
    final public const LINK_BLOCK_ALIAS = 'amasty-gdpr-link';

    /**
     * Cache for consent opting section check
     * @var bool|null
     */
    private $isConsentOpting = null;

    public function __construct(
        private readonly Config                      $configProvider,
        private readonly Session                     $customerSession,
        private readonly PrivacySettingsDataProvider $privacySettingsDataProvider
    ) {
    }

    /**
     * Insert menu item depending on Magento version
     *
     * @param Links|Navigation $subject
     *
     * @throws LocalizedException
     */
    public function beforeGetLinks($subject)
    {
        if ($this->isConsentOpting === null) {
            $this->isConsentOpting = !empty($this->privacySettingsDataProvider->getData(
                ConsentLogger::FROM_PRIVACY_SETTINGS
            ));
        }
        if ($subject->getNameInLayout() != 'customer_account_navigation'
            || !$this->configProvider->isModuleEnabled()
            || !$this->customerSession->isLoggedIn()
            || (!$this->configProvider->isAnySectionVisible()
                && !($this->configProvider->isAllowed(Config::CONSENT_OPTING)
                    //because consent opting is dynamic section need to check it
                    && $this->isConsentOpting)
            )
        ) {
            return;
        }

        $linkClass = interface_exists(M22LinkClass::class) ? M22LinkClass::class : M21LinkClass::class;

        if (!$subject->getLayout()->hasElement(self::LINK_BLOCK_NAME)) {
            $subject->getLayout()->createBlock(
                $linkClass,
                self::LINK_BLOCK_NAME,
                [
                    'data' => [
                        'path' => 'gdpr/customer/settings',
                        'label' => __('Privacy Settings'),
                        'sortOrder' => self::SORT_ORDER
                    ]
                ]
            );
        }

        if (!$subject->getChildBlock(self::LINK_BLOCK_ALIAS)) {
            $subject->insert(
                $subject->getLayout()->getBlock(self::LINK_BLOCK_NAME),
                self::INSERT_AFTER,
                true,
                self::LINK_BLOCK_ALIAS
            );
        }
    }
}
