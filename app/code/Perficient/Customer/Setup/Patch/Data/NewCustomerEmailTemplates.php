<?php
/**
 * Set Session Configuration
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<sreedevi.selvaraj@perficient.com>
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Customer\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr config data
 */
class NewCustomerEmailTemplates implements DataPatchInterface
{
    /**
     * Constants defined for xpath of system configuration
     */
    final const NEW_CUSTOMER_EMAIL = 'customer/create_account/email_template';
    final const NEW_CUSTOMER_NO_PASSWORD_EMAIL = 'customer/create_account/email_no_password_template';
    final const SCOPE_ID = FALSE;

    /**
     * ConfigData constructor.
     */
    public function __construct(
        protected WriterInterface          $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    )
    {

    }

    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setCustomerEmailTemplates();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * New Customer welcome email template
     */
    public function setCustomerEmailTemplates(): void
    {
        $this->configWriter->save(self::NEW_CUSTOMER_EMAIL, "3", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::NEW_CUSTOMER_NO_PASSWORD_EMAIL, "customer_create_account_email_no_password_template", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    public function getAliases(): array
    {
        return [];
    }
}
