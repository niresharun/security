<?php
/**
 * Product Image Mapping
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Divya Sree<divya.sree@Perficient.com>
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
 * Patch script for CRM Admin Mapping
 */
class CrmAdminMappingPhase2 implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const CUSTOMER_PATH = 'perficient_crmconnector/customer/mapping';
    const CUSTOMER_VALUE = '{"_1403608965781_781":{"regexp":"group_id","value":"customerGroup","child":""},"_1403608977556_556":{"regexp":"price_multiplier","value":"priceMultiplier","child":""},"_1403608984372_372":{"regexp":"created_at","value":"created","child":""},"_1403608994084_84":{"regexp":"updated_at","value":"updated","child":""},"_1403609003460_460":{"regexp":"customer_activated","value":"customerActivated","child":""},"_1403609012580_580":{"regexp":"prefix","value":"prefix","child":""},"_1403609018636_636":{"regexp":"firstname","value":"firstName","child":""},"_1403609030947_947":{"regexp":"middlename","value":"middleNameOrInitial","child":""},"_1403609038555_555":{"regexp":"lastname","value":"lastName","child":""},"_1403609048468_468":{"regexp":"suffix","value":"suffix","child":""},"_1403609061171_171":{"regexp":"email","value":"email","child":""},"_1403609074414_414":{"regexp":"telephone","value":"telephone","child":""},"_1403609079938_938":{"regexp":"fax","value":"fax","child":""},"_1403609086858_858":{"regexp":"default_billing","value":"defaultBillingAddress","child":""},"_1403609093870_870":{"regexp":"default_shipping","value":"defaultShippingAddress","child":""},"_1403770934746_746":{"regexp":"entity_id","value":"webCustomerNumber","child":""},"_1403771780784_784":{"regexp":"designer_type","value":"type","child":""},"_1403785424492_492":{"regexp":"addresses","value":"addresses","child":""},"_1404219169888_888":{"regexp":"website_id","value":"websiteId","child":""},"_1404219212286_286":{"regexp":"business_info","value":"businessInfo","child":""},"_1404219226878_878":{"regexp":"company","value":"company","child":""},"_1404219235095_95":{"regexp":"taxvat","value":"taxId","child":""},"_1407474718305_305":{"regexp":"uuid","value":"UUID","child":""},"_1407474719289_289":{"regexp":"is_vip","value":"isVIP","child":""},"_1407474719873_873":{"regexp":"is_customer_of","value":"isCustomerOf","child":""},"_1620774562951_951":{"regexp":"no_of_stores","value":"numberOfStores","child":""},"_1620774578648_648":{"regexp":"sq_ft_per_store","value":"sqFeetPerStore","child":""},"_1620774588011_11":{"regexp":"designer_type","value":"clientTypes","child":""},"_1620774606334_334":{"regexp":"no_of_jobs_per_year","value":"jobsPerYear","child":""},"_1620774747063_63":{"regexp":"no_of_designers","value":"numberOfDesigners","child":""},"_1620774768871_871":{"regexp":"percent_of_design","value":"percentageInDesign","child":""},"_1620774809032_32":{"regexp":"mark_pos","value":"marketingPosition","child":""},"_1635776290660_660":{"regexp":"is_b2c_customer","value":"is_b2c_customer","child":""}}';
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
        $this->setCustomerMapping();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function setCustomerMapping()
    {
        $this->configWriter->save(self::CUSTOMER_PATH, self::CUSTOMER_VALUE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
