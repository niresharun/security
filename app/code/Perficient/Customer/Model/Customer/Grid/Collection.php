<?php
/**
 * Resolve ambiguous when filter customer grid
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */

namespace Perficient\Customer\Model\Customer\Grid;

use Magento\Customer\Model\ResourceModel\Grid\Collection as ParentCollection;

class Collection extends ParentCollection
{
    /**
     * @inheritdoc
     */
    protected $_map = ['fields' => ['entity_id' => 'main_table.entity_id', 'syspro_customer_id' => 'main_table.syspro_customer_id', 'role_name' => 'company_roles.role_name']];
    
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'billing_region') {
            $conditionSql = $this->_getConditionSql(
                $this->getRegionNameExpression(),
                $condition
            );
            $this->getSelect()->where($conditionSql);
            return $this;
        }

        if ($field === 'created_at') {
            if (is_array($condition)) {
                foreach ($condition as $key => $value) {
                    $condition[$key] = $this->timeZone->convertConfigTimeToUtc($value);
                }
            }
        }

        if ($field ===  'role_name') {
        	$field = 'company_roles.' . $field;
        
        } else if( is_string($field) && count(explode('.', $field)) === 1) { 
            $field = 'main_table.' . $field;
        }

        return parent::addFieldToFilter($field, $condition);
    }
    
     /**
     * Get SQL Expression to define Region Name field by locale
     *
     * @return \Zend_Db_Expr
     */
    private function getRegionNameExpression(): \Zend_Db_Expr
    {
        $connection = $this->getConnection();
        $defaultNameExpr = $connection->getIfNullSql(
            $connection->quoteIdentifier('rct.default_name'),
            $connection->quoteIdentifier('main_table.billing_region')
        );

        return $connection->getIfNullSql(
            $connection->quoteIdentifier('rnt.name'),
            $defaultNameExpr
        );
    }

}
