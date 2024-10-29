<?php
/**
 * This module is used to add mailchimp configurations
 *
 * @category: Magento
 * @package: Perficient/MailChimp
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MailChimp
 */

namespace Perficient\MailChimp\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\Config\Backend\Encrypted;

/**
 * Patch script for mailchimp config data
 */
class ConfigData implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const MODULE_ACTIVE = 'mailchimp/general/active';

    const MAILCHIMP_APIKEY = 'mailchimp/general/apikey';

    const LOG = 'mailchimp/general/log';

    const MAGENTOEMAIL = 'mailchimp/general/magentoemail';

    const WEBHOOK_ACTIVE = 'mailchimp/general/webhook_active';

    const WEBHOOK_DELETE = 'mailchimp/general/webhook_delete';

    const SCOPE_ID = 0;
    /**#@-*/

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Encrypted $encrypted
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Encrypted                $encrypted,
        protected ScopeConfigInterface     $scopeConfig
    )
    {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setEnabled();
        $this->setEnabledLog();
        $this->setPublicKey();
        $this->setMagentoEmail();
        $this->setWebhookActive();
        $this->setWebhookDelete();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Api Key
     */
    public function setPublicKey()
    {
        $valueApiKey = $this->scopeConfig->getValue('mailchimp/general/apikey',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            self::SCOPE_ID
        );
        $this->encrypted->setPath(self::MAILCHIMP_APIKEY);
        $this->encrypted->setScopeId(0);
        $this->encrypted->setScope('default');
        $this->encrypted->setScopeCode('');
        $this->encrypted->setValue($valueApiKey);
        $this->encrypted->save();
        $this->configWriter->save(self::MAILCHIMP_APIKEY, $this->encrypted->getValue(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Enabled
     */
    public function setEnabled()
    {
        $this->configWriter->save(self::MODULE_ACTIVE, 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Log
     */
    public function setEnabledLog()
    {
        $this->configWriter->save(self::LOG, 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * MagentoEmail
     */
    public function setMagentoEmail()
    {
        $this->configWriter->save(self::MAGENTOEMAIL, 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * WebhookActive
     */
    public function setWebhookActive()
    {
        $this->configWriter->save(self::WEBHOOK_ACTIVE, 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * WebhookDelete
     */
    public function setWebhookDelete()
    {
        $this->configWriter->save(self::WEBHOOK_DELETE, 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
