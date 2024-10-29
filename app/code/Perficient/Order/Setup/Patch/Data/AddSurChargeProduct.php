<?php
/**
 * Added to handle product surcharge if minimum order amount not met by customer
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */
declare(strict_types=1);

namespace Perficient\Order\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\App\State;


/**
 * Class AddSurChargeProduct
 * @package Perficient\Order\Setup\Patch\Data
 */
class AddSurChargeProduct implements DataPatchInterface, PatchVersionInterface
{
    /**
     * AddSurChargeProduct constructor.
     * @param ResourceConnection $installer
     * @param Product $addProduct
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Config $config
     * @param State $state
     */
    public function __construct(
        private readonly ResourceConnection $installer,
        private readonly Product $addProduct,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Config $config,
        private readonly State $state
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * @return DataPatchInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $entityType = $this->config->getEntityType('catalog_product');
        $product = $this->addProduct;
        $product->setSku('surcharge-sku');
        $product->setName('Surcharge Product');
        $product->setAttributeSetId($entityType->getDefaultAttributeSetId());
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setWeight(1);
        $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
        $product->setTaxClassId(0);
        $product->setShortDescription('Add %current_currency%%amount_left_to_avoid_surcharge% worth more products to avoid order Surcharge');
        $product->setTypeId(Type::TYPE_SIMPLE);
        $product->setPrice(50);
        $product->setStockData(
            ['use_config_manage_stock' => 0, 'manage_stock' => 0, 'is_in_stock' => 1, 'qty' => 99999]
        );
        $product->save();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
