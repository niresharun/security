<?php
/**
 * Admin configuration of Gdpr
 * @category: Magento
 * @package: Perficient/Gdpr
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde<trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Gdpr
 */

namespace Perficient\Gdpr\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr config data
 */
class ConfigData implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final public const MODULE_ENABLED = 'general/enabled';

    final public const LOG_GUEST = 'general/log_guest';

    final public const EU_COUNTRIES = 'general/country/eu_countries';

    final public const AUTO_CLEANING = 'general/auto_cleaning';

    final public const AUTO_CLEANING_DAYS = 'general/auto_cleaning_days';

    final public const AVOID_ANONYMIZATION = 'general/avoid_anonymisation';

    final public const ORDER_STATUSES = 'general/order_statuses';

    final public const NOTIFICATE_ADMIN = 'deletion_notification/enable_admin_notification';

    final public const NOTIFICATE_ADMIN_TEMPLATE = 'deletion_notification/admin_template';

    final public const NOTIFICATE_ADMIN_SENDER = 'deletion_notification/admin_sender';

    final public const NOTIFICATE_ADMIN_RECIEVER = 'deletion_notification/admin_reciever';

    final public const EMAIL_NOTIFICATION_TEMPLATE = 'deletion_notification/template';

    final public const EMAIL_NOTIFICATION_SENDER = 'deletion_notification/sender';

    final public const EMAIL_NOTIFICATION_REPLY_TO = 'deletion_notification/reply_to';

    final public const DENY_NOTIFICATION_TEMPLATE = 'deletion_notification/deny_template';

    final public const DENY_NOTIFICATION_SENDER = 'deletion_notification/deny_sender';

    final public const DENY_NOTIFICATION_REPLY_TO = 'deletion_notification/deny_reply_to';

    final public const ALLOWED = 'customer_access_control/';

    final public const DOWNLOAD = 'download';

    final public const ANONYMIZE = 'anonymize';

    final public const DELETE = 'delete';

    final public const CONSENT_OPTING = 'consent_opting';

    final public const PERSONAL_DATA_DELETION = 'personal_data/automatic_personal_data_deletion/personal_data_deletion';

    final public const PERSONAL_DATA_DELETION_DAYS = 'personal_data/automatic_personal_data_deletion/personal_data_deletion_days';

    final public const PERSONAL_DATA_STORED = 'personal_data/anonymization_data/personal_data_stored';

    final public const PERSONAL_DATA_STORED_DAYS = 'personal_data/anonymization_data/personal_data_stored_days';

    final public const ANONYMIZATION_NOTIFICATION_SENDER = 'anonymization_notification/sender';

    final public const ANONYMIZATION_NOTIFICATION_REPLY_TO = 'anonymization_notification/reply_to';

    final public const ANONYMIZATION_NOTIFICATION_TEMPLATE = 'anonymization_notification/template';

    final public const PATH_PREFIX = 'amasty_gdpr/';

    final public const SCOPE_ID = 0;

    final public const  MODULE_ENABLE_VALUE = 1;

    final public const  LOG_GUEST_VALUE = 1;

    final public const  AUTO_CLEANING_VALUE = 1;

    final public const  AUTO_CLEANING_DAYS_VALUE = 180;

    final public const  DOWNLOAD_VALUE = 1;

    final public const  ANONYMIZE_VALUE = 1;

    final public const  DELETE_VALUE = 1;

    final public const  CONSENT_OPTING_VALUE = 1;

    final public const  PERSONAL_DATA_DELETION_VALUE = 1;

    final public const  PERSONAL_DATA_DELETION_DAYS_VALUE = 3650;

    final public const  PERSONAL_DATA_STORED_VALUE = 1;

    final public const  PERSONAL_DATA_STORED_DAYS_VALUE = 180;

    final public const  AVOID_ANONYMIZATION_VALUE = 1;

    final public const  ANONYMIZATION_NOTIFICATION_SENDER_VALUE = 'general';

    final public const  ANONYMIZATION_NOTIFICATION_REPLY_TO_VALUE = 'hello@example.com';

    final public const  ANONYMIZATION_NOTIFICATION_TEMPLATE_VALUE = 'amasty_gdpr_email_notification_anonymization_notification_template';

    final public const  NOTIFICATE_ADMIN_VALUE = 1;

    final public const  NOTIFICATE_ADMIN_SENDER_VALUE = 'general';

    final public const  NOTIFICATE_ADMIN_RECIEVER_VALUE = 'hello@example.com';

    final public const  NOTIFICATE_ADMIN_TEMPLATE_VALUE = 'amasty_gdpr_email_notification_deletion_notification_manager_notification_admin_template';

    final public const  EMAIL_NOTIFICATION_SENDER_VALUE = 'general';

    final public const  EMAIL_NOTIFICATION_REPLY_TO_VALUE = 'hello@example.com';

    final public const  EMAIL_NOTIFICATION_TEMPLATE_VALUE = 'amasty_gdpr_email_notification_deletion_notification_approve_notification_template';

    final public const  DENY_NOTIFICATION_SENDER_VALUE = 'general';

    final public const  DENY_NOTIFICATION_REPLY_TO_VALUE = 'hello@example.com';

    final public const  DENY_NOTIFICATION_TEMPLATE_VALUE = 'amasty_gdpr_email_notification_deletion_notification_deny_notification_deny_template';

    /**#@-*/

    private array $speciedOrderStatus = ['paypal_canceled_reversal','paypal_reversed','pending','pending_payment','pending_paypal','processing'];

    private array $configData = [
        self::PATH_PREFIX . self::MODULE_ENABLED => self::MODULE_ENABLE_VALUE,
        self::PATH_PREFIX . self::LOG_GUEST => self::LOG_GUEST_VALUE,
        self::PATH_PREFIX . self::AUTO_CLEANING => self::AUTO_CLEANING_VALUE,
        self::PATH_PREFIX . self::AUTO_CLEANING_DAYS => self::AUTO_CLEANING_DAYS_VALUE,
        self::PATH_PREFIX . self::ALLOWED . self::DOWNLOAD => self::DOWNLOAD_VALUE,
        self::PATH_PREFIX . self::ALLOWED . self::ANONYMIZE => self::ANONYMIZE_VALUE,
        self::PATH_PREFIX . self::ALLOWED . self::DELETE => self::DELETE_VALUE,
        self::PATH_PREFIX . self::ALLOWED . self::CONSENT_OPTING => self::CONSENT_OPTING_VALUE,
        self::PATH_PREFIX . self::PERSONAL_DATA_DELETION => self::PERSONAL_DATA_DELETION_VALUE,
        self::PATH_PREFIX . self::PERSONAL_DATA_DELETION_DAYS => self::PERSONAL_DATA_DELETION_DAYS_VALUE,
        self::PATH_PREFIX . self::PERSONAL_DATA_STORED => self::PERSONAL_DATA_STORED_VALUE,
        self::PATH_PREFIX . self::PERSONAL_DATA_STORED_DAYS => self::PERSONAL_DATA_STORED_DAYS_VALUE,
        self::PATH_PREFIX . self::AVOID_ANONYMIZATION => self::AVOID_ANONYMIZATION_VALUE,
        self::PATH_PREFIX . self::ANONYMIZATION_NOTIFICATION_SENDER => self::ANONYMIZATION_NOTIFICATION_SENDER_VALUE,
        self::PATH_PREFIX . self::ANONYMIZATION_NOTIFICATION_REPLY_TO => self::ANONYMIZATION_NOTIFICATION_REPLY_TO_VALUE,
        self::PATH_PREFIX . self::ANONYMIZATION_NOTIFICATION_TEMPLATE => self::ANONYMIZATION_NOTIFICATION_TEMPLATE_VALUE,
        self::PATH_PREFIX . self::NOTIFICATE_ADMIN => self::NOTIFICATE_ADMIN_VALUE,
        self::PATH_PREFIX . self::NOTIFICATE_ADMIN_SENDER => self::NOTIFICATE_ADMIN_SENDER_VALUE,
        self::PATH_PREFIX . self::NOTIFICATE_ADMIN_RECIEVER => self::NOTIFICATE_ADMIN_RECIEVER_VALUE,
        self::PATH_PREFIX . self::NOTIFICATE_ADMIN_TEMPLATE => self::NOTIFICATE_ADMIN_TEMPLATE_VALUE,
        self::PATH_PREFIX . self::EMAIL_NOTIFICATION_SENDER => self::EMAIL_NOTIFICATION_SENDER_VALUE,
        self::PATH_PREFIX . self::EMAIL_NOTIFICATION_REPLY_TO => self::EMAIL_NOTIFICATION_REPLY_TO_VALUE,
        self::PATH_PREFIX . self::EMAIL_NOTIFICATION_TEMPLATE => self::EMAIL_NOTIFICATION_TEMPLATE_VALUE,
        self::PATH_PREFIX . self::DENY_NOTIFICATION_SENDER => self::DENY_NOTIFICATION_SENDER_VALUE,
        self::PATH_PREFIX . self::DENY_NOTIFICATION_REPLY_TO => self::DENY_NOTIFICATION_REPLY_TO_VALUE,
        self::PATH_PREFIX . self::DENY_NOTIFICATION_TEMPLATE => self::DENY_NOTIFICATION_TEMPLATE_VALUE
    ];

    /**
     * ConfigData constructor.
     */
    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ModuleDataSetupInterface $moduleDataSetup
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
        $this->setOrderStatuses();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Order Statuses
     */
    public function setOrderStatuses() {
        $pathOrderStatuses = self::PATH_PREFIX . self::ORDER_STATUSES;
        $valueOrderStatuses = implode(',',$this->speciedOrderStatus);
        $this->configWriter->save($pathOrderStatuses, $valueOrderStatuses, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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