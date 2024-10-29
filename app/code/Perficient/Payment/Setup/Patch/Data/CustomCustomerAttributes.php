<?php
/**
 * Customer Specific Payment Methods
 * @category: Magento
 * @package: Perficient/Payment
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Payment
 */
declare(strict_types=1);

namespace Perficient\Payment\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class CustomCustomerAddressAttributes
 * @package Perficient\Checkout\Setup\Patch\Data
 */
class CustomCustomerAttributes implements DataPatchInterface, PatchVersionInterface
{
    /**
     * CustomCustomerAttributes constructor.
     */
    public function __construct(
        private readonly CustomerSetupFactory $customerSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly AttributeSetFactory $attributeSetFactory
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
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $attributesInfo = [
            'credit_terms_group' => [
                'type' => 'int',
                'input' => 'select',
                'label' => 'Credit Term Group',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'global' => true,
                'visible_on_front' => true,
                'position' => 999,
                'sort_order' => 999,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'adminhtml_only' => true,
                'attribute_group_id' => $attributeGroupId,
                'attribute_set_id' => $attributeSetId,
                'option' =>
                    [
                        'values' =>
                            ['Prepay', 'Not Prepay'],
                    ],
            ]
        ];
        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeParams);
            $addAttributesToForm = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode);
            $addAttributesToForm->setData(
                'used_in_forms',
                ['adminhtml_customer']
            );
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
