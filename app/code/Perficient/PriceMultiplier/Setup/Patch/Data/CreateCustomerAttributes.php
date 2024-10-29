<?php

/**
 * Installer to create customer attributes for price multiplier
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: price multiplier custom customer attribute
 */
declare(strict_types=1);
namespace Perficient\PriceMultiplier\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Api\CustomerMetadataInterface;


class CreateCustomerAttributes implements DataPatchInterface
{
    /**
     * price multiplier attribute
     */
    final const PRICE_MULTIPLIER_ATTRIBUTE = 'price_multiplier';

    /**
     * Discount Type attribute
     */
    final const DISCOUNT_TYPE_ATTRIBUTE = 'discount_type';

    /**
     * discount available attribute
     */
    final const DISCOUNT_AVAILABLE_ATTRIBUTE = 'discount_available';

    /**
     * CreateAttributes constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        protected readonly ModuleDataSetupInterface $setup,
        protected readonly Config $eavConfig,
        protected readonly AttributeSetFactory $attributeSetFactory,
        protected readonly CustomerSetupFactory $customerSetupFactory
    ) {
    }

    /**
     * @return DataPatchInterface|void
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerAttributes = [
            self::PRICE_MULTIPLIER_ATTRIBUTE => [
                'type'           => 'varchar',
                'label'          => 'Price Multiplier',
                'input'          => 'select',
                'source'         => \Perficient\PriceMultiplier\Model\Config\Source\MultiplierOptions::class,
                'required'       => false,
                'visible'        => true,
                'system'         => false,
                'user_defined'   => true,
                'sort_order'     => 70,
                'position'       => 70,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ],
            self::DISCOUNT_TYPE_ATTRIBUTE => [
                'type'           => 'varchar',
                'label'          => 'Discount Type',
                'input'          => 'select',
                'source'         => \Perficient\PriceMultiplier\Model\Config\Source\DiscountOptions::class,
                'required'       => false,
                'visible'        => true,
                'system'         => false,
                'user_defined'   => true,
                'sort_order'     => 71,
                'position'       => 71,
            ],
            self::DISCOUNT_AVAILABLE_ATTRIBUTE => [
                'type'           => 'int',
                'label'          => 'Discount Available',
                'input'          => 'boolean',
                'required'       => false,
                'visible'        => true,
                'system'         => false,
                'user_defined'   => true,
                'default'        => 0,
                'sort_order'     => 72,
                'position'       => 72,
                'source'         => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            ]
        ];

        foreach ($customerAttributes as $attributeCode => $attributeDetails) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                $attributeCode,
                $attributeDetails
            );

            $customerSetup->addAttributeToSet(
                CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
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
     * The default magento OOB method used to get aliases.
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * The default magento OOB method used to get dependencies.
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '2.0.0';
    }

}
