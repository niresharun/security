<?php
/**
 * The class used to get the default options of the product as well as calculate the image, item size.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ResourceConnection;

/**
 *
 * Class ProductDetails
 */
class ProductDetails extends AbstractModel
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public $connection;
    private static $tableDetails = [
        'treatment' => [
            'table' => 'treatment',
            'selection' => 'display_name',
            'where' => 'treatment_sku'
        ],
        'medium' => [
            'table' => 'media',
            'selection' => 'display_name',
            'where' => 'sku'
        ]
    ];

    /**
     * ProductDetails constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->connection = $resource->getConnection();
    }


    /**
     * Get display name from treatment sku
     * SQL queries added as disucssed in tech call to improve performance
     * @param string $treatmentSku
     * @return null|array
     */
    public function getDisplayName($sku, $table)
    {
        $select = $this->connection->select();
        $select->from(
            ['cg' => $this->connection->getTableName(self::$tableDetails[$table]['table'])],
            [
                self::$tableDetails[$table]['selection']
            ]
        );
        if ($table == 'treatment') {
            $select->where('treatment_sku = ?', $sku);
        } else {
            $select->where('sku = ?', $sku);
        }
        $result = $this->connection->fetchRow($select);

        if (isset($result[self::$tableDetails[$table]['selection']])) {
            return $result[self::$tableDetails[$table]['selection']];
        } else {
            return $sku;
        }
    }
}
