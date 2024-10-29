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
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * Class UpdateAttributes
 * @package Perficient\Checkout\Setup\Patch\Data
 */
class UpdateAttributes implements DataPatchInterface, PatchVersionInterface
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
        private readonly \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
		AttributeSetFactory $attributeSetFactory
    ) {
		$this->attributeSetFactory = $attributeSetFactory;
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
		$this->updateAddressAttributes();
    }

    /**
     * Customer Address Attribute changes
     */
	public function updateAddressAttributes()
	{
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributesInfo = [
            'order_shipping_notes' => [
                'label' => 'Order/Shipping Notes',
                'type' => 'text',
                'input' => 'textarea',
                'required' => false,
                'visible' => true,				
                'user_defined' => true,
                'system' => false,
                'visible_on_front' => true,
                'position' => 999,
                'sort_order' => 999,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
				'is_visible' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
            'location' => [
                'label' => 'Address Type',
                'type' => 'int',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
				'option' => ['values' => ['Business', 'Residential']],
                'default' => '',
                'required' => false,
                'visible' => true,
				'user_defined' => true,
                'system' => false,
                'position' => 996,
                'sort_order' => 996,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
				'is_visible' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,                
            ],
            'delivery_appointment' => [
                'label' => 'Delivery Appointment Required',
                'type' => 'int',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
				'option' => ['values' => ['No', 'Yes']],
				'value' => 0,
                'default' => '',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 997,
                'position' => 997,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_visible' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
            ],
            'loading_dock_available' => [
                'label' => 'Loading Dock Available',			
                'type' => 'int',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
				'option' => ['values' => ['No', 'Yes']],
                'value' => 0,
                'default' => '',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 998,
                'position' => 998,
                'system' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_visible' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
			]
        ];
        foreach ($attributesInfo as $attributeCode => $attributeParams) {

            $customerSetup->addAttribute('customer_address', $attributeCode, $attributeParams);
			
			$customerSetup->addAttributeToSet(
                AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
                null,
                $attributeCode
            );			
			
            $addAttributesToForm = $customerSetup->getEavConfig()->getAttribute('customer_address', $attributeCode);
            $addAttributesToForm->setData(
                'used_in_forms',
                ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
            );
			$addAttributesToForm->setData(
                'attribute_set_id',
				AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS
            );
			$addAttributesToForm->setData(
                'attribute_group_id',
				AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS
            );
			$addAttributesToForm->setData(
				'source_identifier',
				'M2'
            );	

			//set default option value
			if ($attributeCode == 'delivery_appointment' || $attributeCode == 'loading_dock_available') {
				$options = $addAttributesToForm->getSource()->getAllOptions();
				$defaultVal = '';
				foreach ($options as $option) {
					if ($option['label'] == 'No') {
						$defaultVal = $option['value'];
					}
				}
                $addAttributesToForm->setData(
					'default_value', $defaultVal
				);
			}
			if ($attributeCode == 'location') {
				$options = $addAttributesToForm->getSource()->getAllOptions();
				$defaultVal = '';
				foreach ($options as $option) {
					if ($option['label'] == 'Residential') {
						$defaultVal = $option['value'];
					}
				}
                $addAttributesToForm->setData(
					'default_value', $defaultVal
				);
            }		
			
            $addAttributesToForm->save();
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