<?php
/**
 * Add New field in catalog product For DCKAP
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde<trupti.bobde@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Class CreateCustomerAttribute
 */
class CreateCustomerAttribute implements DataPatchInterface
{
    /**
     * credit terms descr attribute
     */
    const CREDIT_TERMS_DESCR_ATTRIBUTE = 'credit_terms_descr';

    /**
     * aocial media address attribute
     */
    const SOCIAL_MEDIA_ADDRESS_ATTRIBUTE = 'social_media_address';

    /**
     * credit terms code attribute
     */
    const CREDIT_TERMS_CODE_ATTRIBUTE = 'credit_terms_code';

    /**
     * CreateAttributes constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        protected ModuleDataSetupInterface $setup,
        protected Config                   $eavConfig,
        protected AttributeSetFactory      $attributeSetFactory,
        protected CustomerSetupFactory     $customerSetupFactory
    )
    {
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
            self::CREDIT_TERMS_DESCR_ATTRIBUTE => [
                'type' => 'varchar',
                'label' => 'Credit Terms Description',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 100,
                'position' => 100,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'backend' => ''
            ],
            self::SOCIAL_MEDIA_ADDRESS_ATTRIBUTE => [
                'type' => 'varchar',
                'label' => 'Social Media Address',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 101,
                'position' => 101,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'backend' => ''
            ],
            self::CREDIT_TERMS_CODE_ATTRIBUTE => [
                'type' => 'varchar',
                'label' => 'Credit Terms Code',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 102,
                'position' => 102,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'backend' => ''
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
