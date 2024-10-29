<?php
/**
 * update crm reports link setting
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

namespace Perficient\Reports\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Config\Model\Config\Backend\Encrypted;
/**
 * Patch script to change flatrate price
 */
class UpdateCrmReportsLinkConfig implements DataPatchInterface
{

    const PATH_CRM_REPORTS_LINK = 'perficient_crm/settings/reports_url';

    const SCOPE_ID = 0;

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Encrypted $encrypted
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Encrypted $encrypted
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setCrmReportsLink();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Change CRM Reports Link
     */
    private function setCrmReportsLink() {
        $crmLink = 'http://crmtestui.wag.thegrandriver.net/#/wendover/dashboard/dashboard';
        $this->configWriter->save( self::PATH_CRM_REPORTS_LINK, $crmLink, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
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
