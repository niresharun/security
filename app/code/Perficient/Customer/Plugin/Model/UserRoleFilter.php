<?php
/**
 * User Role filter in customer grid
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Customer\Plugin\Model;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Customer\Model\ResourceModel\Grid\Collection as CustomerGridCollection;

/**
 * Class UserRoleFilter
 * @package Perficient\Customer\Plugin\Model
 */
class UserRoleFilter
{
    /**
     * UserRoleFilter constructor.
     */
    public function __construct(
        private readonly MessageManager         $messageManager,
        private readonly CustomerGridCollection $collection
    )
    {

    }

    /**
     * @param $requestName
     * @return CustomerGridCollection|mixed
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure                                                                   $proceed,
                                                                                   $requestName
    )
    {
        $result = $proceed($requestName);
        if ($requestName == 'customer_listing_data_source') {
            if ($result instanceof $this->collection
            ) {
                $select = $this->collection->getSelect();
                $select->joinLeft(
                    ['cur' => 'company_user_roles'],
                    "cur.user_id = main_table.entity_id"
                )
                    ->joinLeft(
                        ['company_roles' => 'company_roles'],
                        "company_roles.role_id = cur.role_id",
                        ['role_name']
                    );
                    
                return $this->collection;
            }
        }

        return $result;
    }
}

