<?php
/**
 * @author Perficient Team
 * @copyright Copyright (c) 2021 Perficient
 * @package Perficient_Gdpr
 */

namespace Perficient\Gdpr\Block;

use Amasty\Gdpr\Model\Consent\DataProvider\PrivacySettingsDataProviderFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\FormKey as FormKey;
use Perficient\Gdpr\Model\Config;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;

class Settings extends \Amasty\Gdpr\Block\Settings
{
    /**
     * Settings constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param FormKey $formKey
     * @param Config $configProvider
     * @param PrivacySettingsDataProviderFactory $privacySettingsDataProviderFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry         $registry,
        FormKey          $formKey,
        protected Config $configProvider,
        Manager          $moduleManager,
        PrivacySettingsDataProviderFactory $privacySettingsDataProviderFactory,
        array            $data = []
    ) {
        parent::__construct($context, $registry, $formKey, $configProvider, $moduleManager, $privacySettingsDataProviderFactory);
    }

    /**
     * @return string
     */
    public function getWendoverContactBlock()
    {
        if ($this->configProvider->isEnabledContactBlock(Config::ENABLED_WENDOVER_CONTACT_BLOCK)) {
            return $this->configProvider->allowWendoverContactBlock(Config::WENDOVER_CONTACT_BLOCK);
        }
        return '';
    }

    /**
     * @return array
     */
    public function getPrivacySettings()
    {
        return $this->getAvailableBlocks();
    }

    /**
     * @return array
     */
    private function getPrivacyBlocks()
    {
        $result = [];

        if ($this->configProvider->isModuleEnabled()) {
            if ($this->configProvider->isAllowed(Config::DELETE)) {
                $result[self::DELETE_ACCOUT_BLOCK_SHORT_NAME] = [
                    'title' => __('Delete account'),
                    'cssModifier' => '-delete',
                    'content' => __(
                        'Request to remove your account, together with all your personal data,
                         will be processed by our staff.<br>Deleting your account will remove all the purchase
                         history, discounts, orders, payment receipts and all other information that might be related to your
                         account or your purchases.<br>All your orders and similar information will be
                         lost.<br>You will not be able to restore access to your account after
                         we approve your removal request.'
                    ),
                    'checked' => true,
                    'hasCheckbox' => true,
                    'checkboxText' => __('I understand and I want to delete my account'),
                    'hidePassword' => true,
                    'checkboxDataValidate' => '{required:true}',
                    'needPassword' => $this->isNeedPassword(),
                    'submitText' => __('Agree and Submit request'),
                    'action' => $this->getUrl('gdpr/customer/addDeleteRequest'),
                    'actionCode' => Config::DELETE
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getAvailableBlocks()
    {
        $result = [];
        $allBlocks = $this->getPrivacyBlocks();
        $visibleBlocks = $this->getData(self::VISIBLE_BLOCK_LAYOUT_VARIABLE_NAME)
            ? explode(',', $this->getData(self::VISIBLE_BLOCK_LAYOUT_VARIABLE_NAME)) : [];

        if (!$visibleBlocks) {
            return $allBlocks;
        }

        foreach ($visibleBlocks as $blockName) {
            if (array_key_exists($blockName, $allBlocks)) {
                $result[$blockName] = $allBlocks[$blockName];
            }
        }

        return $result;
    }

}
