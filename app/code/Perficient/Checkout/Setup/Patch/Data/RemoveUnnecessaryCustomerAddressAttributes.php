<?php
/**
 * Checkout Addresses Custom Attribute Installer.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Nikhil Atkare <Nikhil.Atkare@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */

declare(strict_types=1);

namespace Perficient\Checkout\Setup\Patch\Data;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class CustomCustomerAddressAttributes
 * @package Perficient\Checkout\Setup\Patch\Data
 */
class RemoveUnnecessaryCustomerAddressAttributes implements DataPatchInterface, PatchVersionInterface
{
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
        /*updating the street multiline_count */
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'street');
        $attribute->setData('multiline_count', 2);
        $attribute->save();

        $attribute ='';
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'location');
        $attribute->setData('frontend_label', 'Address Type');
        $attribute->setData('sort_order', 23);
        $attribute->save();

        $attribute ='';
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'loading_dock_available');
        $attribute->setData('is_required', 0);
        $attribute->save();

        $attribute ='';
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'delivery_appointment');
        $attribute->setData('is_required', 0);
        $attribute->save();

    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}