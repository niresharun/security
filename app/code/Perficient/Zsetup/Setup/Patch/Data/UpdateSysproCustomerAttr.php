<?php
/**
 * Syspro customer id update.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */

declare(strict_types=1);

namespace Perficient\Zsetup\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;


class UpdateSysproCustomerAttr implements DataPatchInterface, PatchVersionInterface
{
    const SYSPRO_CUSTOMER_ID = 'syspro_customer_id';

    /**
     * CustomCustomerAddressAttributes constructor.
     * @param ResourceConnection $installer
     * @param CustomerSetupFactory $customerSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly ResourceConnection $installer,
        private readonly CustomerSetupFactory $customerSetupFactory,
        private readonly \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /*updating the sales rep and cam attributes */
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', self::SYSPRO_CUSTOMER_ID);
        if($attribute) {
            $attribute->setData('is_used_in_grid', true);
            $attribute->setData('is_visible_in_grid', true);
            $attribute->setData('is_filterable_in_grid', true);
            $attribute->setData('is_searchable_in_grid', true);
            $attribute->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
