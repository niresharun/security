<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Ccpa
 */


namespace Perficient\Ccpa\Block;

use Amasty\Ccpa\Model\CalifornianDetector;
use Amasty\Ccpa\Model\Config;
use Magento\Customer\Block\Account\Navigation;
use Magento\Customer\Block\Account\SortLinkInterface as M22LinkClass;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Link\Current as M21LinkClass;
use Magento\Framework\View\Element\Html\Links;

class AccountLinkPlugin extends \Amasty\Ccpa\Block\AccountLinkPlugin
{
    final public const SORT_ORDER = 223;
    final public const INSERT_AFTER = 'customer-account-navigation-account-edit-link';
    final public const LINK_BLOCK_NAME = 'customer-account-amasty-ccpa-settings';
    final public const LINK_BLOCK_ALIAS = 'amasty-ccpa-link';

    public function __construct(
        private readonly Config              $configProvider,
        private readonly Session             $customerSession,
        private readonly CalifornianDetector $californianDetector
    ){
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
        $customerId = (int)$this->customerSession->getId();
        if ($customerId > 0) {
            if ($subject->getNameInLayout() != 'customer_account_navigation'
                || !$this->configProvider->isModuleEnabled()
                || ($this->configProvider->isOnlyCalifornians() && !$this->californianDetector->isCalifornian($customerId))
                || !$this->customerSession->isLoggedIn()
                || !$this->configProvider->isAnySectionVisible()
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
                            'path' => 'ccpa/customer/settings',
                            'label' => __('California Privacy Settings'),
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


}
