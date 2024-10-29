<?php
/**
 * Add new option in credit_terms_group attribute.
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class AddOptionToCreditTermsGroup
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class AddOptionToCreditTermsGroup implements DataPatchInterface
{
    /**
     * Constant for credit_terms_group
     */
    const ATTR_CREDIT_TERMS_GROUP   = 'credit_terms_group';
    const ATTR_NOT_PREPAY_LABEL_OLD = 'Not Prepay';
    const ATTR_NOT_PREPAY_LABEL_NEW = 'Net';

    /**
     * AddOptionToCreditTermsGroup constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param StoreManagerInterface $storeManager
     * @param Config $eavConfig
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly EavSetupFactory $eavSetupFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly Config $eavConfig,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->addOptionToCreditTermsGroup();
    }

    /**
     * Add new option to credit_terms_group attribute and rename an existing option label.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addOptionToCreditTermsGroup()
    {
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, self::ATTR_CREDIT_TERMS_GROUP);
        $allStores = $this->storeManager->getStores();
        $option = [];
        $attribute_arr = ['Pay at Shipping'];
        $option['attribute_id'] = $attribute->getAttributeId();
        foreach ($attribute_arr as $key => $value) {
            $option['value'][$value][0] = $value;
            foreach ($allStores as $store) {
                $option['value'][$value][$store->getId()] = $value;
            }
        }
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);

        if ($attribute->usesSource()) {
            $optionId = $attribute->getSource()->getOptionId(self::ATTR_NOT_PREPAY_LABEL_OLD);

            $connection = $this->resourceConnection->getConnection();
            $magentoEavTable = $connection->getTableName('eav_attribute_option_value');
            $connection->update(
                $magentoEavTable,
                ['value' => self::ATTR_NOT_PREPAY_LABEL_NEW],
                'option_id = ' . $optionId
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}