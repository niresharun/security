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

namespace Perficient\Company\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\ResourceConnection;
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
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $attributesInfo = [
            'user_actual_parent_id' => [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'User Actual Parent ID',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'system' => false,
                'group' => 'General',
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
