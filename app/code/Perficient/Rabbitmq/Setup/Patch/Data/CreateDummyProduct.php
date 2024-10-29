<?php
/**
 * Script to create a dummy product which will be used to create order where the product doesn't exists in Magento.
 *
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq Product Create
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\State;

/**
 * Class CreateDummyProduct
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class CreateDummyProduct implements DataPatchInterface
{
    /**
     * @var Product
     */
    private $addProduct;

    /**
     * CreateDummyProduct constructor.
     *
     * @param Product $addProduct
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Config $config
     * @param State $state
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        Product $addProduct,
        private readonly Config $config,
        private readonly State $state
    ) {
        $this->product      = $addProduct;
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Exception
     */
    public function apply()
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
            $entityType = $this->config->getEntityType('catalog_product');
            $this->product->setSku('MISC');
            $this->product->setName('MISC Product');
            $this->product->setAttributeSetId($entityType->getDefaultAttributeSetId());
            $this->product->setStatus(Status::STATUS_ENABLED);
            $this->product->setWeight(1);
            $this->product->setVisibility(Visibility::VISIBILITY_IN_CATALOG);
            $this->product->setTaxClassId(0);
            $this->product->setTypeId(Type::TYPE_SIMPLE);
            $this->product->setPrice(1);
            $this->product->setStockData([
                'use_config_manage_stock' => 0,
                'manage_stock'            => 0,
                'is_in_stock'             => 1,
                'qty'                     => 99999
            ]);
            $this->product->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
