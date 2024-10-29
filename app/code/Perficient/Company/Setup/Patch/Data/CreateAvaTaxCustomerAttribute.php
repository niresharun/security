<?php
/**
 * Add New fields in customer which needs to send from magento to Avalara Request.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Archana Lohakare<archana.lohakare@Perficient.com>
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
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

/**
 * Class CreateAvaTaxCustomerAttribute
 */
class CreateAvaTaxCustomerAttribute implements DataPatchInterface
{
    /**
     *  Customer Usage Code attribute (Customer's Tax category)
     */
    const CUSTOMER_USAGE_CODE = 'customer_usage_code';

    /**
     * Country For Tax attribute
     */
    const COUNTRY_FOR_TAX = 'country_for_tax';

    /**
     * Send to AvaTax attribute
     */
    const SEND_TO_AVATAX = 'send_to_avatax';

    /**
     * CreateAvaTaxCustomerAttribute constructor.
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
     * Created customer attribute t0 send in Avatax request.
     * @return $this|void
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerAttributes = [
            self::CUSTOMER_USAGE_CODE => [
                'type' => 'varchar',
                'label' => 'Customer Usage Code(Tax category)',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 200,
                'position' => 200,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'backend' => ''
            ],
            self::COUNTRY_FOR_TAX => [
                'type' => 'varchar',
                'label' => 'Country For Tax',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 201,
                'position' => 201,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'backend' => ''
            ],
            self::SEND_TO_AVATAX => [
                'type' => 'int',
                'label' => 'Send To AvaTax',
                'input' => 'boolean',
                'source_model' => Boolean ::class,
                'source' => Boolean ::class,
                'required' => false,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'sort_order' => 202,
                'position' => 202,
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
