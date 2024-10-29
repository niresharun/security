<?php
/**
 * Rabbitmq product inventory update
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\Rabbitmq\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryLinkManagementInterface;

class ProductInventoryUpdate extends AbstractModel
{
    /**
     * @var MessageArray
     */
    private $messageArray;

    const ART_ATTRIBUTE_SET = 'Art';
    /**
     * ProductInventoryUpdate constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $jsonSerializer
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     * @param ResourceConnection $resourceCon
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly Json $jsonSerializer,
        private readonly Data $rabbitMqHelper,
        private readonly ProductRepositoryInterface $productRepository,
        \Magento\Framework\Serialize\Serializer\Json $json,
        private readonly ResourceConnection $resourceCon,
        AttributeSetRepositoryInterface $attributeSetRepository,
        private CategoryFactory $category,
        private CategoryLinkManagementInterface $categoryLinkManagement,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->attributeSetRepository = $attributeSetRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $message
     */
    public function updateProductInventory($message)
    {
        try {
            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);

            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->rabbitMqHelper->logRabbitMqMessage($message);
            }
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::CATALOG_INVENTORY_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData = rtrim((string) $jsonData, '}') . ', "Message" :"' . $this->messageArray . '"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_INVENTORY_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Inventory update'),
                $message->getBody()
            );
        }
        try {
            $stocks = $this->messageArray['stock'];
            foreach ($stocks as $stock) {
                $sku = trim((string) $stock['sku']);
                $quantityAvailable = $stock['qty'];
                try {
                    $product = $this->productRepository->get($sku, true, 0);
                    $exeQuickShip = $product->getData('is_quick_ship');
                    if ($product->getId()) {
                        $inStock = 1;
                        $isQuickShip = 0;
                        if ((int)$quantityAvailable > 0) {
                            //$inStock = 1;
                            $isQuickShip = 1;
                        }
                        $stockData = [
                            'qty' => (int)$quantityAvailable,
                            'is_in_stock' => $inStock
                        ];
                        $product->setStockData($stockData);
                        $product->setQuantityAndStockStatus($stockData);
                        $attributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());
                        $framesAndMats = ['Frame', 'Mat'];
                        if (!in_array($attributeSet->getAttributeSetName(), $framesAndMats)) {
                            $product->setCustomAttribute('is_quick_ship', $isQuickShip);
                        }
                        $product->save();
                        if ($attributeSet->getAttributeSetName() === self::ART_ATTRIBUTE_SET) {
                            if (($exeQuickShip === 0 && $isQuickShip === 1) ||
                                (($isQuickShip == 1) && (!in_array($this->quickShipCategoryId(), $product->getCategoryIds())))) {

                                // Adding the quick ship category for the specific product
                                $this->modifyProductToQuickShipCategory($product, 1);
                            } else if (($exeQuickShip === 1 && $isQuickShip === 0) ||
                                (!$exeQuickShip && !$isQuickShip)) {

                                // Removing the quick ship category for the specific product
                                $this->modifyProductToQuickShipCategory($product, 0);
                            }
                        }
                    }

                } catch (NoSuchEntityException $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::CATALOG_INVENTORY_UPDATE_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];

                    /** Requeue message if deadlock or lock wait timeout receive */
                    if (str_contains($e->getMessage(), 'Lock wait timeout exceeded') || str_contains($e->getMessage(), 'Deadlock found when trying to get lock')) {
                        $jsonData = $this->jsonSerializer->unserialize($message->getBody());
                        $this->rabbitMqHelper->publishErrMessage(Data::TOPIC_PRODUCT_INVENTORY_UPDATE, $jsonData);

                        if ($this->rabbitMqHelper->isLoggingEnabled()) {
                            $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                                Data::CATALOG_INVENTORY_UPDATE_REQUEUE_LOG_FILE
                            );
                            $logger->debug($e->getMessage() . "::" . $message->getBody());
                        }
                    } else {
                        if (str_contains($e->getMessage(), 'The product that was requested')) {
                            $publishData = ['error' => 'We received a bulk message to process the Product Inventory Update and from this bulk nested Inventory Message we already processed SKUs which exist in the magento system but here we found SKU ***--' . $sku . '--*** which currently does not exist in the Magento system hence we skipping this SKU to be getting processed.'];
                            $jsonData = $this->jsonSerializer->serialize($publishData);
                        } else {
                            $jsonData = $this->jsonSerializer->serialize($publishData);
                            $jsonData = rtrim((string) $jsonData, '}') . ', "Message" :"' . $this->jsonSerializer->unserialize($message->getBody()) . '"}';
                        }
                        $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_INVENTORY_UPDATE, $jsonData);
                    }


                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Product Create Update'),
                        $message->getBody()
                    );
                }
            }
            /*update out of stock product*/
            $connection = $this->resourceCon->getConnection();
            $magentoCatalogInventoryStockItemTable = $connection->getTableName('cataloginventory_stock_item');
            $connection->update(
                $magentoCatalogInventoryStockItemTable,
                ['is_in_stock' => 1],
                'is_in_stock = 0'
            );
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::CATALOG_INVENTORY_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];

            /** Requeue message if deadlock or lock wait timeout receive */
            if (str_contains($e->getMessage(), 'Lock wait timeout exceeded') || str_contains($e->getMessage(), 'Deadlock found when trying to get lock')) {
                $jsonData = $this->jsonSerializer->unserialize($message->getBody());
                $this->rabbitMqHelper->publishErrMessage(Data::TOPIC_PRODUCT_INVENTORY_UPDATE, $jsonData);

                if ($this->rabbitMqHelper->isLoggingEnabled()) {
                    $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                        Data::CATALOG_INVENTORY_UPDATE_REQUEUE_LOG_FILE
                    );
                    $logger->debug($e->getMessage() . "::" . $message->getBody());
                }
            }
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData = rtrim((string) $jsonData, '}') . ', "Message" :"' . $this->jsonSerializer->unserialize($message->getBody()) . '"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_INVENTORY_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Product Create Update'),
                $message->getBody()
            );
        }
    }

    /**
     * This method is used to get the quick-ship-category-Id
     *
     * @return mixed
     */

    public function quickShipCategoryId()
    {
        $categoryTitle = 'Quick Ship';
        $collection = $this->category->create()->getCollection()
            ->addAttributeToFilter('name',$categoryTitle)->setPageSize(1);
        return  $collection->getFirstItem()->getEntityId();
    }

    /**
     * This function performs weather the quick ship
    category  can be added of rmoved for the specific product
     *
     * @param $product
     * @param $flag
     * @return void
     */

    protected function modifyProductToQuickShipCategory($product, $flag)
    {
        try {
            $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                Data::CATALOG_INVENTORY_UPDATE_ERROR_LOG_FILE
            );
            $logger->debug(" Product SKU: ".$product->getSku() );
            $status = ($flag === 1) ? "Quick category added" : "Quick category removed";
            $quickShipId = $this->quickShipCategoryId();
            $existingCategoryIds = $product->getCategoryIds();
            if ($flag === 1) {
                $existingCategoryIds[] = $quickShipId;
            } else {
                $key = array_search($quickShipId, $existingCategoryIds);
                if ($key !== false) {
                    unset($existingCategoryIds[$key]);
                }
            }
            $this->categoryLinkManagement->assignProductToCategories($product->getSku(), $existingCategoryIds);
            $logger->debug("Quickship category modifitication: " . $status);
        } catch (\Exception $e) {
            $logger->debug("Quick Ship update fails " . "---->" . $e);
        }
    }
}
