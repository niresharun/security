<?php
/**
 * Installer to create new order statuses.
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq Order Status
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Setup\Patch\Data;

use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CreateNewOrderStatuses
 * @package Perficient\Rabbitmq\Setup\Patch\Data
 */
class CreateNewOrderStatuses implements DataPatchInterface
{
    private array $statuses = [
        'order_entry'                       => 'Order Entry / Processing',
        'awaiting_deposit_or_prepayment'    => 'Awaiting Deposit or Prepayment',
        'awaiting_design_approval'          => 'Awaiting Design Approval',
        'in_production'                     => 'In Production',
        'awaiting_final_payment_or_release' => 'Awaiting Final Payment or Release',
        'ready_to_ship'                     => 'Ready to Ship',
        'partially_shipped'                 => 'Partially Shipped',
        'shipped_complete'                  => 'Shipped / Complete',
    ];

    /**
     * CreateNewOrderStatuses constructor.
     *
     * @param StatusFactory $statusFactory
     */
    public function __construct(
        private readonly StatusFactory $statusFactory,
        private readonly StatusResourceFactory $statusResourceFactory
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
        $this->addCustomOrderStatus();
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    private function addCustomOrderStatus()
    {
        foreach ($this->statuses as $statusCode => $statusLabel) {
            try {
                $statusResource = $this->statusResourceFactory->create();
                $status = $this->statusFactory->create();
                $status->setData([
                    'status' => $statusCode,
                    'label'  => $statusLabel,
                ]);

                $statusResource->save($status);

                $status->assignState($statusCode, true, true);
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}
