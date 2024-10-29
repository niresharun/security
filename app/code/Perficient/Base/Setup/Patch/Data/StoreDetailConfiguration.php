<?php
/**
 * Set Session Configuration
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vijayashanthi<v.murugesan@Perficient.com>
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for gdpr config data
 */
class StoreDetailConfiguration implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const STORE_NAME = 'general/store_information/name';
    final const STORE_PHONE = 'general/store_information/phone';
    final const STORE_HOURS = 'general/store_information/hours';
    final const STORE_COUNTRY = 'general/store_information/country_id';
    final const STORE_REGION = 'general/store_information/region_id';
    final const STORE_POSTCODE = 'general/store_information/postcode';
    final const STORE_CITY = 'general/store_information/city';
    final const STORE_STREET_LINE1 = 'general/store_information/street_line1';
    final const STORE_STREET_LINE2 = 'general/store_information/street_line2';
    final const STORE_VAT = 'general/store_information/merchant_vat_number';
    final const STORE_TIMEZONE = 'general/locale/timezone';
    final const STORE_CODE = 'general/locale/code';
    final const STORE_FIRSTDAY = 'general/locale/firstday';
    final const STORE_WEEKEND = 'general/locale/weekend';
    final const STORE_GENERAL_EMAIL = 'trans_email/ident_general/email';
    final const STORE_GENERAL_NAME = 'trans_email/ident_general/name';
    final const STORE_SALES_EMAIL = 'trans_email/ident_sales/email';
    final const STORE_SALES_NAME = 'trans_email/ident_sales/name';
    final const STORE_SUPPORT_EMAIL = 'trans_email/ident_support/email';
    final const STORE_SUPPORT_NAME = 'trans_email/ident_support/name';
    final const STORE_CUSTOM1_EMAIL = 'trans_email/ident_custom1/email';
    final const STORE_CUSTOM1_NAME = 'trans_email/ident_custom1/name';
    final const STORE_CUSTOM2_EMAIL = 'trans_email/ident_custom2/email';
    final const STORE_CUSTOM2_NAME = 'trans_email/ident_custom2/name';

    final const SCOPE_ID = FALSE;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * {@inheritdoc}
     */
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
        $this->setStoreConfigurationValues();
        //$this->setSessionRestrictionValues();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Customer Cookie value increase
     */
    public function setStoreConfigurationValues(): void
    {
        $this->configWriter->save(self::STORE_NAME, "Wendover Art Group", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_PHONE, "888-743-9232", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        //$this->configWriter->save(self::STORE_HOURS, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_COUNTRY, "US", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_REGION, "18", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_POSTCODE, "33773", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_CITY, "Largo", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_STREET_LINE1, "6465 126th Avenue North", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);/*
        $this->configWriter->save(self::STORE_STREET_LINE2, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_VAT, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_TIMEZONE, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_CODE, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_FIRSTDAY, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::STORE_WEEKEND, "", ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);*/
    }


    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
