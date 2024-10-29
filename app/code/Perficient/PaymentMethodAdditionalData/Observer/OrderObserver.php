<?php
/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */
namespace Perficient\PaymentMethodAdditionalData\Observer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ObserverInterface;

class OrderObserver implements ObserverInterface
{
    /**
     * @var AbstractDb
     */
    private $dbCon;

    /**
     * OrderObserver constructor.
     * @param AbstractDb $dbCon
     */
    public function __construct(
        ResourceConnection $dbCon
    ) {
        $this->dbCon = $dbCon;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrder()->getId();
        $connection = $this->dbCon->getConnection();
        $tableName = $connection->getTableName('quote_payment');
        $query = $connection->select()
            ->from($tableName, ['*'])
            ->join(
                ['so' => 'sales_order'],
                'so.quote_id = quote_payment.quote_id',
                ['*']
            )
            ->where('so.entity_id = ?', $orderId);
        $fetchData = $connection->fetchAll($query);
        $data = ["customer_po_number" => $fetchData[0]['customer_po_number']];
        $where = ['parent_id = ?' => (int)$orderId];
        $tableName = $connection->getTableName("sales_order_payment");
        $connection->update($tableName, $data, $where);
    }
}
