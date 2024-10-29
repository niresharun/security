<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace DCKAP\Productimize\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * UpgradeSchema mock class
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {        
        //Create table and check, that Magento can`t delete it
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('custom_image_detail');
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            if($installer->getConnection()->isTableExists($tableName) != true){
                $table = $setup->getConnection()->newTable($tableName)->addColumn(
                    'custom_image_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Custom image id'
                )->addColumn(
                    'custom_image_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1055,
                    [],
                    'Custom image name'
                )->setComment(
                    'Custom Image Detail'
                );
                $setup->getConnection()->createTable($table);
            }
        }

    }
}
