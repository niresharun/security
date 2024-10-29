<?php
/**
 * Wendover Custom attributes
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Setup\Patch\Data;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Script for adding WendOver attributes that will be updated by Syspro
 */
class AddWendoverAdditionalAttributesSalesCam implements DataPatchInterface, PatchVersionInterface
{
    /**
     * AddWendOverCustomerAttributes constructor.
     * @param ResourceConnection $installer
     * @param CustomerSetupFactory $customerSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly ResourceConnection       $installer,
        private readonly CustomerSetupFactory     $customerSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly AttributeSetFactory      $attributeSetFactory
    )
    {
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
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $attributesInfo = [
            'sales_rep_phone' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Sales Rep Phone Number',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'validate_rules' => '{"max_text_length":255,"min_text_length":1}',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible_on_front' => false,
                'position' => 1004,
                'sort_order' => 1004,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'adminhtml_only' => true,
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
            ],
            'cam_phone' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Customer Account Manager Phone Number',
                'visible' => true,
                'required' => false,
                'validate_rules' => '{"max_text_length":255,"min_text_length":1}',
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible_on_front' => false,
                'position' => 1005,
                'sort_order' => 1005,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'adminhtml_only' => true,
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
            ],
            'sales_rep_email' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Sales Rep Email',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'validate_rules' => '{"input_validation":"email"}',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible_on_front' => false,
                'position' => 1000,
                'sort_order' => 1000,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'adminhtml_only' => true,
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
            ],
            'cam_email' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Customer Account Manager Email',
                'visible' => true,
                'required' => false,
                'validate_rules' => '{"input_validation":"email"}',
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible_on_front' => false,
                'position' => 1001,
                'sort_order' => 1001,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'adminhtml_only' => true,
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
            ]
        ];
        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeParams);
            $customerSetup->addAttributeToSet(
                Customer::ENTITY,
                CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
                null,
                $attributeCode
            );
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode)
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => [
                        'adminhtml_customer'
                    ],
                ]);

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
