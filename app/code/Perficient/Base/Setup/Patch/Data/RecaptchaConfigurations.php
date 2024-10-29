<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\Config\Backend\Encrypted;

/**
 * Patch script for recaptcha config data
 */
class RecaptchaConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const RECAPTCHA_PUBLIC_KEY = 'recaptcha_frontend/type_recaptcha_v3/public_key';

    final const RECAPTCHA_PRIVATE_KEY = 'recaptcha_frontend/type_recaptcha_v3/private_key';

    final const RECAPTCHA_CUSTOMER_FORGOT_PASSWORD = 'recaptcha_frontend/type_for/customer_forgot_password';

    final const RECAPTCHA_COMPANY_CREATE = 'recaptcha_frontend/type_for/company_create';

    final const CUSTOMER_CAPTCHA_ENABLE = 'customer/captcha/enable';

    final const VALUE_RECAPTCHA_V3 = 'recaptcha_v3';

    final const SCOPE_ID = 0;
    /**#@-*/

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Encrypted $encrypted
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Encrypted $encrypted,
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setPublicKey();
        $this->setPrivateKey();
        $this->setTypeForCustomerForgotPassword();
        $this->setTypeForCompanyCreate();
        $this->setCustomerCaptchaEnable();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Public Key
     */
    public function setPublicKey(): void
    {
        $valuePublicKey = $this->scopeConfig->getValue('recaptcha/type_v3/public_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            self::SCOPE_ID
        );
        $this->encrypted->setPath(self::RECAPTCHA_PUBLIC_KEY);
        $this->encrypted->setScopeId(0);
        $this->encrypted->setScope('default');
        $this->encrypted->setScopeCode('');
        $this->encrypted->setValue($valuePublicKey);
        $this->encrypted->save();
        $this->configWriter->save(self::RECAPTCHA_PUBLIC_KEY, $this->encrypted->getValue(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    /**
     * Private Key
     */
    public function setPrivateKey(): void
    {
        $valuePrivateKey = $this->scopeConfig->getValue('recaptcha/type_v3/private_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            self::SCOPE_ID
        );
        $this->encrypted->setPath(self::RECAPTCHA_PRIVATE_KEY);
        $this->encrypted->setScopeId(0);
        $this->encrypted->setScope('default');
        $this->encrypted->setScopeCode('');
        $this->encrypted->setValue($valuePrivateKey);
        $this->encrypted->save();
        $this->configWriter->save(self::RECAPTCHA_PRIVATE_KEY, $this->encrypted->getValue(), ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    /**
     * type_for_customer_forgot_password
     */
    public function setTypeForCustomerForgotPassword(): void
    {
        $this->configWriter->save(self::RECAPTCHA_CUSTOMER_FORGOT_PASSWORD, self::VALUE_RECAPTCHA_V3, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * type_for_company_create
     */
    public function setTypeForCompanyCreate(): void
    {
        $this->configWriter->save(self::RECAPTCHA_COMPANY_CREATE, self::VALUE_RECAPTCHA_V3, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * customer_captcha_enable
     */
    public function setCustomerCaptchaEnable(): void
    {
        $this->configWriter->save(self::CUSTOMER_CAPTCHA_ENABLE, 0, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
