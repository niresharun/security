<?php
/**
 * Custom Email Template to Notify Admin
 * Ref Ticket : WENDOVER-534
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Setup\Patch\Data;

use Exception;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class EmailConfiguration
 * @package Perficient\Company\Setup\Patch\Data
 */
class EmailConfiguration implements DataPatchInterface
{
    /**
     * @var string
     */
    protected $notifyAdminTemplatePath = 'company/email/company_notify_admin_template';

    /**
     * @var string
     */
    protected $notifyAdminTemplateValue = 'custom_company_email_company_notify_admin_template';

    /**
     * EmailConfiguration constructor.
     * @param WriterInterface $configWriter
     * @param StoreManagerInterface $storeManager
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly WriterInterface          $configWriter,
        private readonly StoreManagerInterface    $storeManager,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    )
    {
    }

    /**
     * Apply the change for website configuration changes
     * {@inheritdoc}
     * @throws Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->configWriter->save(
            $this->notifyAdminTemplatePath,
            $this->notifyAdminTemplateValue,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID
        );
        $this->moduleDataSetup->getConnection()->endSetup();
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
