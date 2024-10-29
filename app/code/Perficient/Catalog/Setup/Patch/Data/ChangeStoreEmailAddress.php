<?php
/**
 * Product Image Mapping
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for mailchimp config data
 */
class ChangeStoreEmailAddress implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const GENERAL_CONTACT = 'trans_email/ident_general/email';
    const GENERAL_EMAIL = 'info@wendoverart.com';
    const SALES_REPRESENTATIVE = 'trans_email/ident_sales/email';
    const SALES_EMAIL = 'info@wendoverart.com';
    const CUSTOMER_SUPPORT = 'trans_email/ident_support/email';
    const CUSTOMER_EMAIL = 'info@wendoverart.com';
    const CUSTOM_EMAIL_1 = 'trans_email/ident_custom1/email';
    const CUSTOM_1_EMAIL = 'info@wendoverart.com';
    const CUSTOM_EMAIL_2 = 'trans_email/ident_custom2/email';
    const CUSTOM_2_EMAIL = 'info@wendoverart.com';
    const CONTACT_US_PATH = 'contact/email/recipient_email';
    const CONTACT_US_EMAIL = 'info@wendoverart.com';
    const GMAIL_SMTP_EMAIL = 'system/gmailsmtpapp/debug/from_email';
    const GMAIL_SMTP_VALUE = 'info@wendoverart.com';
    const SCOPE_ID = false;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected ScopeConfigInterface     $scopeConfig
    )
    {
        $this->configWriter = $configWriter;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setGeneralContact();
        $this->setSalesRepresentative();
        $this->setCustomerSupport();
        $this->setCustomEmail1();
        $this->setCustomEmail2();
        $this->setContactUsEmail();
        $this->setGmailSmtpEmail();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Set General Contact email
     */
    public function setGeneralContact()
    {
        $this->configWriter->save(self::GENERAL_CONTACT, self::GENERAL_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Sales Representative email
     */
    public function setSalesRepresentative()
    {
        $this->configWriter->save(self::SALES_REPRESENTATIVE, self::SALES_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Customer Support Email
     */
    public function setCustomerSupport()
    {
        $this->configWriter->save(self::CUSTOMER_SUPPORT, self::CUSTOMER_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Custom Email 1
     */
    public function setCustomEmail1()
    {
        $this->configWriter->save(self::CUSTOM_EMAIL_1, self::CUSTOM_1_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Custom Email 2
     */
    public function setCustomEmail2()
    {
        $this->configWriter->save(self::CUSTOM_EMAIL_2, self::CUSTOM_2_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Contact Us email
     */
    public function setContactUsEmail()
    {
        $this->configWriter->save(self::CONTACT_US_PATH, self::CONTACT_US_EMAIL, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * Set Gmail Smtp Email
     */
    public function setGmailSmtpEmail()
    {
        $this->configWriter->save(self::GMAIL_SMTP_EMAIL, self::GMAIL_SMTP_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
