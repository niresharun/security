<?php
/**
 * Checkout Addresses Custom Attribute Installer.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
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
class CustomCustomerAddressAttributes implements DataPatchInterface, PatchVersionInterface
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
        /*$customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributesInfo = [
            'order_shipping_notes' => [
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Order/Shipping Notes',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'global' => true,
                'visible_on_front' => true,
                'position' => 999,
                'sort_order' => 999,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
            'location' => [
                'type' => 'int',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'default' => '0',
                'required' => true,
                'visible' => true,
                'system' => false,
                'position' => 999,
                'sort_order' => 999,
                'filterable' => true,
                'comparable' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'label' => 'Location',
                'option' => ['values' => ['Business', 'Residential']],
                'user_defined' => true,
                'group' => 'General',
                'global' => true,
                'visible_on_front' => true,
            ],
            'delivery_appointment' => [
                'label' => 'Delivery Appointment',
                'type' => 'int',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'value' => 0,
                'default' => '',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 300,
                'position' => 300,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_visible' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
            'loading_dock_available' => [
                'type' => 'int',
                'label' => 'Loading Dock Available',
                'input' => 'boolean',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'value' => 0,
                'default' => '',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 300,
                'position' => 300,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_visible' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
        ];
        foreach ($attributesInfo as $attributeCode => $attributeParams) {

            $customerSetup->addAttribute('customer_address', $attributeCode, $attributeParams);
            $addAttributesToForm = $customerSetup->getEavConfig()->getAttribute('customer_address', $attributeCode);
            $addAttributesToForm->setData(
                'used_in_forms',
                ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
            );

            $addAttributesToForm->save();
        }
		*/
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
