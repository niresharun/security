<?php
/**
 * Installer to set the option for syspro_customer_id, so that it can be used to sort on customer grid.
 *
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
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateSysproCustomerIdOption
 * @package Perficient\Rabbitmq\Setup\Patch\Data
 */
class UpdateSysproCustomerIdOption implements DataPatchInterface
{
    /**
     * Constant for syspro_customer_id
     */
    const ATTR_SYSPRO_CUSTOMER_ID = 'syspro_customer_id';

    /**
     * UpdateSysproCustomerIdOption constructor.
     * @param Config $eavConfig
     */
    public function __construct(
        private readonly Config $eavConfig
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
        try {
            $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, self::ATTR_SYSPRO_CUSTOMER_ID);
            $attribute->setData('is_used_in_grid', 1)->save();
        } catch (\Exception $e) {
            throw $e;
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
