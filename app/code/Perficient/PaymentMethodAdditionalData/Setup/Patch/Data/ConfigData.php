<?php
/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */

namespace Perficient\PaymentMethodAdditionalData\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Patch script for Ccpa config data
 */
class ConfigData implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const PAYMENT_AUTHNETCIM_ACTIVE = 'payment/authnetcim/active';

    const PAYMENT_AUTHNETCIM_ACH_ACTIVE = 'payment/authnetcim_ach/active';

    const PAYMENT_FREE_ACTIVE = 'payment/free/active';

    const PAYMENT_CHECKMO_ACTIVE = 'payment/checkmo/active';

    const PAYMENT_AUTHNETCIM_TITLE = 'payment/authnetcim/title';

    const PAYMENT_CHECKMO_TITLE = 'payment/checkmo/title';

    const PAYMENT_AUTHNETCIM_CCTYPES = 'payment/authnetcim/cctypes';

    const PAYMENT_AUTHNETCIM_ORDER_STATUS = 'payment/authnetcim/order_status';

    const PAYMENT_AUTHNETCIM_ACH_ORDER_STATUS = 'payment/authnetcim_ach/order_status';

    const SCOPE_ID = 0;

    const PAYMENT_AUTHNETCIM_ALLOWSPECIFIC = 'payment/authnetcim/allowspecific';

    const PAYMENT_AUTHNETCIM_ACH_ALLOWSPECIFIC =  'payment/authnetcim_ach/allowspecific';

    const PAYMENT_CHECKMO_ALLOWSPECIFIC =  'payment/checkmo/allowspecific';

    const PAYMENT_FREE_ALLOWSPECIFIC =  'payment/free/allowspecific';

    const PAYMENT_AUTHNETCIM_SPECIFICCOUNTRY =  'payment/authnetcim/specificcountry';

    const PAYMENT_AUTHNETCIM_ACH_SPECIFICCOUNTRY =  'payment/authnetcim_ach/specificcountry';

    const PAYMENT_CHECKMO_SPECIFICCOUNTRY =  'payment/checkmo/specificcountry';

    const PAYMENT_FREE_SPECIFICCOUNTRY =  'payment/free/specificcountry';

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

        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ACTIVE, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ACH_ACTIVE, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_FREE_ACTIVE, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_CHECKMO_ACTIVE, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_TITLE, 'Pay Now with Credit Card', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_CCTYPES, 'AE,VI,MC,DI', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ORDER_STATUS, 'processing', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ACH_ORDER_STATUS, 'processing', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_CHECKMO_TITLE, 'Pay on Terms', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ALLOWSPECIFIC, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ACH_ALLOWSPECIFIC, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_CHECKMO_ALLOWSPECIFIC, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_FREE_ALLOWSPECIFIC, true, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_SPECIFICCOUNTRY, 'CA,US', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_AUTHNETCIM_ACH_SPECIFICCOUNTRY, 'CA,US', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_CHECKMO_SPECIFICCOUNTRY, 'CA,US', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->configWriter->save(self::PAYMENT_FREE_SPECIFICCOUNTRY, 'CA,US', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);

        $this->moduleDataSetup->getConnection()->endSetup();
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
