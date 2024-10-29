<?php
/**
 * Created by PhpStorm.
 * User: akash.kulkarni
 * Date: 22-10-2021
 * Time: 11:45 AM
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ProductRepository;

class UpdateSurchargeQty
{
    const XML_PATH_CRON_EXPRESSION = 'rabbitmq/surcharge_qty_cron_settings/enabled';
    const SURCHARGE_PRODUCT_SKU = 'surcharge-sku';

    /**
     * @var \Perficient\Catalog\Block\GetData
     */
    protected $getData;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepository $productRepository
     * @param LoggerInterface $logger
     */
    public  function __construct(
        protected ScopeConfigInterface $scopeConfig,
        private readonly ProductRepository $productRepository,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Execute Method
     * @return  void
     */
    public function execute()
    {
        try {
            $storeScope = ScopeInterface::SCOPE_STORE;
            $checkEnable = $this->scopeConfig->getValue(self::XML_PATH_CRON_EXPRESSION, $storeScope);
            if (!$checkEnable) {
                return false;
            }

            $qty = '9999999';
            $product = $this->productRepository->get(self::SURCHARGE_PRODUCT_SKU);
            $product->setStockData(['qty' => $qty, 'is_in_stock' => 1]);
            $this->productRepository->save($product);
            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
