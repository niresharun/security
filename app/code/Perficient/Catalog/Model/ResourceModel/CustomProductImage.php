<?php
/**
 * Custom Product Image
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain <hiral.jain@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class CustomProductImage
 * @package Perficient\CustomProductImage\Model\ResourceModel
 */
class CustomProductImage extends AbstractDb
{
    /**
     * CustomProductImage constructor.
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
                $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('perficient_custom_product_images', 'custom_product_image_id');
    }

    /**
     * Get product custom image identifier by sku and type
     *
     * @param string $sku
     * @return int|false
     */
    public function getIdBySkuAndType($data)
    {
        $connection = $this->getConnection();
        $table = $connection->getTableName('perficient_custom_product_images');

        $select = $connection->select()->from($table, 'custom_product_image_id')->where('sku = :sku and type = :type');

        $bind = [
            ':sku' => (string)$data['sku'],
            ':type' => (string)$data['type']
        ];

        return $connection->fetchOne($select, $bind);
    }
}
