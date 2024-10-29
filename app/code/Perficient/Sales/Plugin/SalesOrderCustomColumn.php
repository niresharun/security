<?php
/**
 * New column in sales grid
 * @category: Magento
 * @package: Perficient/Sales
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Sales
 */
namespace Perficient\Sales\Plugin;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;

/**
 * Class SalesOrderCustomColumn
 * @package Perficient\Sales\Plugin
 */
class SalesOrderCustomColumn
{
    /**
     * SalesOrderCustomColumn constructor.
     */
    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly SalesOrderGridCollection $collection
    ) {
    }

    /**
     * @param $requestName
     * @return SalesOrderGridCollection|mixed
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source') {
            if ($result instanceof $this->collection
            ) {
                $select = $this->collection->getSelect();
                $select->joinLeft(
                    ["sorder" => $this->collection->getTable("sales_order")],
                    'main_table.increment_id = sorder.increment_id',
                    ['syspro_order_id']
                );
                return $this->collection;
            }
        }
        return $result;
    }
}
