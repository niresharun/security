<?php
/**
 * Rabbitmq product create update
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Exception;
use LogicException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryLinkRepository;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductCategoryList;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute as ConfigurableAttrModel;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory as ConfigurableAttrModelFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute as ConfigurableAttrResourceModel;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\OptionManagement;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavAttribute;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\Store\Model\ScopeInterface;
use Perficient\Catalog\Helper\Data as ConfigOptions;
use Perficient\Rabbitmq\Helper\Data;

class ProductCreateUpdate extends AbstractModel
{
    private $framesAndMats = ['Frame', 'Mat'];

    /** @var int $mirrorAttributeSetId  */
    private $mirrorAttributeSetId = null;

    /**
     * @var MessageArray
     */
    private $messageArray;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;
    /* These are not actual magento attributes*/
    private array $imageDetailsAttributes = [
//        'frame_color_default',
//        'liner_color_default',
//        'top_mat_color_default',
//        'bottom_mat_color_default',
        'default_frame_depth',
        'default_liner_depth',
        'default_frame_color',
        'default_liner_color',
        'default_top_mat_color',
        'default_bottom_mat_color'
    ];

    /**
     * 'attribute on payload' => 'attribute on magento'
     *
     * @var string[]
     */
    private array $configurableOptionArray = [
        'frame_default_sku' => ['attribute_code' => 'frame_default_sku_configurable', 'can_create' => false],
        'size_string' => ['attribute_code' => 'size_string', 'can_create' => true],
        'glass_type' => ['attribute_code' => 'glass_type', 'can_create' => false]
    ];

    private $attrVisibility;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Json $jsonSerializer
     * @param Data $rabbitMqHelper
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Product $product
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
     * @param CategoryLinkRepository $categoryLink
     * @param ProductCategoryList $productCategory
     * @param ResourceConnection $resourceCon
     * @param SourceItemsSaveInterface $sourceItemsSave
     * @param SourceItemInterfaceFactory $sourceItemFactory
     * @param UrlRewriteSysproSyncProduct $regenerateProductRewrites
     * @param File $fileManager
     * @param LinkManagementInterface $linkManagement
     * @param ConfigurableAttrModelFactory $configurableAttrModelFactory
     * @param ConfigurableAttrResourceModel $configurableAttrResourceModel
     * @param EavAttribute $eavAttribute
     * @param OptionManagement $optionManagement
     * @param AttributeOptionInterfaceFactory $attributeOptionInterfaceFactory
     */
    public function __construct(
        Context                                            $context,
        Registry                                           $registry,
        private readonly Json                              $jsonSerializer,
        private readonly Data                              $rabbitMqHelper,
        private readonly ProductInterfaceFactory           $productFactory,
        private readonly ProductRepositoryInterface        $productRepository,
        private readonly Product                           $product,
        Filesystem                                         $filesystem,
        private readonly ScopeConfigInterface              $scopeConfig,
        private readonly CategoryLinkRepository            $categoryLink,
        private readonly ProductCategoryList               $productCategory,
        private readonly ResourceConnection                $resourceCon,
        private readonly SourceItemsSaveInterface          $sourceItemsSave,
        private readonly SourceItemInterfaceFactory        $sourceItemFactory,
        protected UrlRewriteSysproSyncProduct              $regenerateProductRewrites,
        private readonly File                              $fileManager,
        protected readonly LinkManagementInterface         $linkManagement,
        protected readonly ConfigurableAttrModelFactory    $configurableAttrModelFactory,
        protected readonly ConfigurableAttrResourceModel   $configurableAttrResourceModel,
        protected readonly EavAttribute                    $eavAttribute,
        protected readonly OptionManagement                $optionManagement,
        protected readonly AttributeOptionInterfaceFactory $attributeOptionInterfaceFactory,
        protected readonly MetadataPool $metadataPool,
        protected readonly EavConfig $eavConfig,
        protected readonly ConfigOptions $configOption
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        parent::__construct($context, $registry);
    }

    /**
     * Update product inventory
     *
     * @param EnvelopeInterface $message
     * @throws LocalizedException
     */
    public function createUpdateProduct(EnvelopeInterface $message)
    {
        $this->logSuccess("SUCCESS ::" . $message->getBody());
        $this->logError("SUCCESS ::" . $message->getBody());

        try {
            $this->rabbitMqHelper->logRabbitMqMessage($message);
            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
            $this->logSuccess("SUCCESS-1 ::" . $message->getBody());
        } catch (Exception $e) {
            $this->logError($e->getMessage() . "::" . $message->getBody());
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData = rtrim((string)$jsonData, '}') . ', "Message" :"' . $this->messageArray . '"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Product Create Update'),
                $message->getBody()
            );
        }

        if (!isset($this->messageArray['product']) || empty($this->messageArray['product'])) {
            $this->logError("Payload is invalid");
            $this->rabbitMqHelper->sendErrorEmail(
                "Payload is invalid",
                __('Product Create Update'),
                $message->getBody()
            );
            return;
        }

        $productArr = $this->messageArray['product'];
        switch ($productArr['type_id']) {
            case ProductType::TYPE_SIMPLE:
                $this->createSimpleProduct($productArr, $message);
                break;
            case Configurable::TYPE_CODE:
                $this->createConfigurableOptionValue($productArr);
                $this->createConfigurableProduct($productArr, $message);
                break;
            default:
                $this->logError(sprintf("Product type is invalid %s", $productArr['type_id']));
        }
    }

    /**
     * @param array $productArr
     * @param EnvelopeInterface $message
     * @return array|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function createSimpleProduct(array $productArr, EnvelopeInterface &$message): ?array
    {
        $tableCategoryProductRelation = "catalog_category_product";
        $categoryIds = [];
        $oldExistingCategory = [];
        $productVisibilityAttributeId = $this->getProductVisibilityAttributeId();

        try {
            $this->logSuccess("SUCCESS-2 ::" . json_encode($productArr, 1));
            if (!array_key_exists("sku", $productArr) || !array_key_exists("attribute_set", $productArr) || !array_key_exists("status", $productArr)) {
                $this->errorLoggerForProductCreateUpdate(
                    $message->getBody(),
                    __('Required Attributes is Missing')
                );
                return null;
            }
            $sku = trim((string)$productArr['sku']);
            if ($sku == "" || $productArr['attribute_set'] == "" || $productArr['visibility'] == "") {
                $this->errorLoggerForProductCreateUpdate(
                    $message->getBody(),
                    __('Required Attributes is empty')
                );
                return null;
            }
            if (count($productArr) && !empty($productArr['sku'])) {
                $attributeSetId = $this->product->getAttributeSetId($productArr['attribute_set']);
                if ($attributeSetId == -1) {
                    $this->logError(sprintf("%s attribute-set is not found in magento", $productArr['attribute_set']));
                    $this->rabbitMqHelper->sendErrorEmail(
                        sprintf("%s attribute-set is not found in magento", $productArr['attribute_set']),
                        __('Product Create Update'),
                        $message->getBody()
                    );
                    return null;
                }
                $visibility = $this->product->getVisibilityIdByName($productArr['visibility']);
                $status = Status::STATUS_DISABLED;
                if ($productArr['status'] == 1) {
                    $status = Status::STATUS_ENABLED;
                }
                $sku = trim((string)$productArr['sku']);
                try {
                    $product = $this->productRepository->get($sku, true, 0);
                    $select = $this->resourceCon->getConnection()->select();
                    $select->from(
                        ['cpe' => $this->resourceCon->getConnection()->getTableName($tableCategoryProductRelation)],
                        ['GROUP_CONCAT(category_id) AS categories']
                    )->where('product_id = ?', $product->getId());
                    $result = $this->resourceCon->getConnection()->fetchCol($select);
                    if (isset($result) && isset($result[0]) && !empty($result[0])) {
                        $oldExistingCategory = explode(',', (string)$result[0]);
                    }
                } catch (NoSuchEntityException $e) {
                    /** @var ProductInterface $product */
                    $product = $this->productFactory->create();
                    $product->setSku($sku);
                    $product->setStoreId(0);
                    $type = ProductType::TYPE_SIMPLE;
                    if (isset($productArr['type_id']) && $productArr['type_id'] != '') {
                        $type = $productArr['type_id'];
                    }
                    $product->setTypeId($type);
                    $prdImagesPath = $this->scopeConfig->getValue(
                        Data::XML_PATH_PRODUCT_IMAGES_PATH,
                        ScopeInterface::SCOPE_STORE
                    );
                    $files = $this->mediaDirectory->search($sku . '*', $prdImagesPath);
                    if ((is_countable($files) ? count($files) : 0) > 0) {
                        foreach ($files as $file) {
                            $fileInfo = $this->fileManager->getPathInfo($file);
                            $fileName = $fileInfo['basename'];
                            $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $fileName);
                            $filepath = $this->mediaDirectory->getAbsolutePath() . $file;
                            if (strtolower($sku) == strtolower($fileNameNoExtension)) {
                                $product->addImageToMediaGallery($filepath, ['image', 'thumbnail', 'small_image', 'swatch_image'], false, false);
                            } else {
                                $product->addImageToMediaGallery($filepath, null, false, false);
                            }
                        }
                    }
                }
                try {
                    $product->setName($productArr['name']);
                    $product->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE);
                    if ($visibility) {
                        $product->setVisibility($visibility);
                    }
                    $product->setPrice($productArr['price']);
                    $product->setAttributeSetId($attributeSetId);

                    $product->setStatus($status);
                    /*$key = $productArr['name']."-".$sku;
                    $url = preg_replace('#[^0-9a-z]+#i', '-', $key);
                    $urlKey = strtolower($url);
                    $product->setUrlKey($urlKey);*/
                    $previousUrlKey = $product->getUrlKey();
                    if (!isset($productArr['url_key']) || empty($productArr['url_key']) || ($status != 1)) {
                        //handles 3 cases
                        //a) if url_key is having blank value (i.e. empty string) in msg OR
                        //b) if url_key is missing in msg
                        //c) if url_key is null value
                        $product->setUrlKey('');
                    }
                    if (isset($productArr['url_key']) && !empty($productArr['url_key']) && ($status == 1)) {
                        //if url_key node exists and is not blank (empty)
                        $product->setUrlKey($productArr['url_key']);
                    }
                    if (isset($productArr['weight']) && !empty($productArr['weight']) && is_numeric($productArr['weight'])) {
                        $product->setWeight($productArr['weight']);
                    }

                    /*Start fix for WENDOVER-553: Out of Stock Frame Features*/
                    if (isset($productArr['days_to_in_stock'])) {
                        $product->setDaysToInStock($productArr['days_to_in_stock']);
                    }
                    /*End fix for WENDOVER-553: Out of Stock Frame Features*/

                    $defaultConfigurationAttributes = array_flip(ConfigOptions::$defaultConfLabel);
                    if ($this->mirrorAttributeSetId === null) {
                        $this->mirrorAttributeSetId = $this->configOption->getMirrorAttributeSetId();
                    }
                    if ($this->mirrorAttributeSetId === $attributeSetId) {
                        $defaultConfigurationAttributes = array_flip(ConfigOptions::$defaultConfMirrorProductLabel);
                    }

                    // If desired, you can set a tax class like so:
                    list($defaultConfigurationValues, $tempRelatedItems) = $this->processCustomAttribute(
                        $productArr['custom_attributes'],
                        $defaultConfigurationAttributes,
                        $product
                    );
                    /*Syspro will send details for color in message against some node which will be set to default json string attribute.
                    Note these will not be actual attributes. We are not sure which node they will share it with us.
                    So lets assume we will get it in color_details node in "::" separated format which we can set in json string as*/
                    if (isset($productArr['color_details']) &&
                        !empty($productArr['color_details']) &&
                        is_array($productArr['color_details'])) {
                        foreach ($productArr['color_details'] as $imgDetail) {
                            if (in_array($imgDetail['color_code'], $this->imageDetailsAttributes)) {
                                $defaultConfigurationValues[$imgDetail['color_code']] = $imgDetail['value'] . ":--" . ltrim(ucwords(str_replace(['_', 'default'], [' ', ''], (string)$imgDetail['color_code'])), ' ');
                            }
                        }
                    }
                    if (!isset($productArr['color_details'])) {
                        $existingColorDetails = $product->getData('default_configurations');
                        if (!empty($existingColorDetails)) {
                            $existingColorDetailsArray = $this->jsonSerializer->unserialize($existingColorDetails);
                            if (!empty($existingColorDetails)) {
                                foreach ($this->imageDetailsAttributes as $fetchFromDb) {
                                    if (array_key_exists($fetchFromDb, $existingColorDetailsArray)) {
                                        $fetchValue = explode(':', (string)$existingColorDetailsArray[$fetchFromDb]);
                                        $defaultConfigurationValues[$fetchFromDb] = $fetchValue[0] . ":--" . ltrim(ucwords(str_replace(['_', 'default'], [' ', ''], (string)$fetchFromDb)), ' ');
                                    }

                                }
                            }
                        }
                    }
                    /*added logic if $defaultConfigurationAttributes  not present in current request then we will check
                    if we have their value present in magento DB so that they can retained in 'default_configurations' attribute  */
                    if (isset($defaultConfigurationValues) && !empty($defaultConfigurationValues)) {
                        $existingColorDetails = $product->getData('default_configurations');
                        if (!empty($existingColorDetails)) {
                            $existingColorDetailsArray = $this->jsonSerializer->unserialize($existingColorDetails);
                            foreach ($this->imageDetailsAttributes as $fetchFromDb) {
                                if (!array_key_exists($fetchFromDb, $defaultConfigurationValues)) {
                                    if (array_key_exists($fetchFromDb, $existingColorDetailsArray)) {
                                        $fetchValue = explode(':', (string)$existingColorDetailsArray[$fetchFromDb]);
                                        $defaultConfigurationValues[$fetchFromDb] = $fetchValue[0] . ":--" . str_replace('_', ' ', ucwords((string)$fetchFromDb));
                                    }
                                }
                            }
                        }
                        $cleanedJsonString = str_replace(':--', ':', (string)$this->jsonSerializer->serialize($defaultConfigurationValues));
                        $product->setCustomAttribute('default_configurations', $cleanedJsonString);
                    }

                    /** @var bool $deleteCategory */
                    /** @var int[] $categoryIds */
                    /** @var int[] $removableCategoryIds */
                    list(
                        $deleteCategory,
                        $categoryIds,
                        $removableCategoryIds
                        ) = $this->processExtensionAttribute($productArr, $categoryIds, (int)$product->getId());

                    if (!empty($categoryIds)) {
                        $product->setCategoryIds($categoryIds);
                    }

                    if ($product->getId()) {
                        $product->save();
                        $this->logSuccess("SUCCESS-3 ::" . $product->getId());
                        if ($deleteCategory && $removableCategoryIds) {
                            $this->removableCategoryIds($removableCategoryIds, $product->getSku(), $oldExistingCategory);
                        }
                        if (isset($product) && isset($categoryIds)) {
                            $this->categoryProductAssociationCheck($categoryIds, $product, $tableCategoryProductRelation);
                        }
                        /*update out of stock product*/
                        $connection = $this->setProductsToInStock();

                        // update urlrewrite if url key present in the message
                        if ($previousUrlKey != $product->getUrlKey()) {
                            $removeExisting = $this->removeExistingRewrites($product->getId());
                            if ($status == 1) {
                                $this->regenerateProductRewrites->regenerateSpecificSysproProductUrlRewrites($product->getId(), 0);
                            }
                        }
                    } else {
                        if (!in_array($productArr['attribute_set'], $this->framesAndMats)) {
                            $product->setCustomAttribute('is_quick_ship', 0);
                        }
                        $product = $this->productRepository->save($product);
                        $this->logSuccess("SUCCESS-4 ::" . $product->getId());
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode('default');
                        $sourceItem->setSku($product->getSku());
                        $sourceItem->setQuantity(0);
                        $sourceItem->setStatus(1);

                        $this->sourceItemsSave->execute([$sourceItem]);
                        /*update out of stock product*/
                        $connection = $this->setProductsToInStock();

                        try {
                            $newProduct = $this->productRepository->get(trim((string)$productArr['sku']), true, 0);
                            if (isset($newProduct) && isset($categoryIds)) {
                                $this->categoryProductAssociationCheck($categoryIds, $newProduct, $tableCategoryProductRelation);
                            }
                            if (isset($newProduct)) {
                                $attrVisibility = $this->getProductCatalogVisibility($productArr);
                                $magentoCatalogProductEntityIntTable = $connection->getTableName('catalog_product_entity_int');
                                $connection->update(
                                    $magentoCatalogProductEntityIntTable,
                                    ['value' => $attrVisibility],
                                    'attribute_id =' . $productVisibilityAttributeId . ' AND row_id =' . $newProduct->getId()
                                );
                                if (!empty($tempRelatedItems)) {
                                    if(is_array($tempRelatedItems)) {
                                        $tempRelatedItems = implode(',', $tempRelatedItems);
                                    }
                                    $this->setRelatedProducts((int)$newProduct->getId(), $tempRelatedItems);
                                }
                                // update urlrewrite if url key present in the message
                                //$removeExisting = $this->removeExistingRewrites($newProduct->getId());
                                if ($status == 1) {
                                    $this->regenerateProductRewrites->regenerateSpecificSysproProductUrlRewrites($newProduct->getId(), 0);
                                }
                            }
                        } catch (LogicException|Exception $e) {
                            $this->logError($e->getMessage() . "::" . $message->getBody());
                            $publishData = ['error' => $e->getMessage()];

                            /** Requeue message if deadlock or lock wait timeout receive */
                            if (str_contains($e->getMessage(), 'Lock wait timeout exceeded') || str_contains($e->getMessage(), 'Deadlock found when trying to get lock')) {
                                $jsonData = $this->jsonSerializer->unserialize($message->getBody());
                                $this->rabbitMqHelper->publishErrMessage(Data::TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
                                $this->logRequeue($e->getMessage() . "::" . $message->getBody());
                            } else {
                                $jsonData = $this->jsonSerializer->serialize($publishData);
                                $jsonData = rtrim((string)$jsonData, '}') . ', "Message" :"' . $this->jsonSerializer->unserialize($message->getBody()) . '"}';
                                $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
                            }

                            $this->rabbitMqHelper->sendErrorEmail(
                                $e->getMessage(),
                                __('Product Create Update'),
                                $message->getBody()
                            );
                        }
                    }
                    $upInsProdID = '';
                    $upInsProdID = $product->getId();
                    $attrVisibility = $this->getProductCatalogVisibility($productArr);
                    if (isset($upInsProdID) && !empty($upInsProdID)) {
                        $magentoCatalogProductEntityIntTable = $connection->getTableName('catalog_product_entity_int');
                        $connection->update(
                            $magentoCatalogProductEntityIntTable,
                            ['value' => $attrVisibility],
                            'attribute_id =' . $productVisibilityAttributeId . ' AND row_id =' . $product->getId()
                        );
                    }
                    $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
                    return [(int)$product->getData($linkField), $product->getSku()];
                } catch (LogicException|Exception $e) {
                    $this->logError($e->getMessage() . "::" . $message->getBody());
                    $publishData = ['error' => $e->getMessage()];

                    /** Requeue message if deadlock or lock wait timeout receive */
                    if (str_contains($e->getMessage(), 'Lock wait timeout exceeded') || str_contains($e->getMessage(), 'Deadlock found when trying to get lock')) {
                        $jsonData = $this->jsonSerializer->unserialize($message->getBody());
                        $this->rabbitMqHelper->publishErrMessage(Data::TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
                        $this->logRequeue($e->getMessage() . "::" . $message->getBody());
                    } else {
                        $jsonData = $this->jsonSerializer->serialize($publishData);
                        $jsonData = rtrim((string)$jsonData, '}') . ', "Message" :"' . $this->jsonSerializer->unserialize($message->getBody()) . '"}';
                        $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
                    }
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Product Create Update'),
                        $message->getBody()
                    );
                }
                return null;
            }

        } catch (Exception $e) {
            $this->logError($e->getMessage() . "::" . $message->getBody());
            $publishData = ['error' => $e->getMessage()];

            /** Requeue message if deadlock or lock wait timeout receive */
            if (str_contains($e->getMessage(), 'Lock wait timeout exceeded') || str_contains($e->getMessage(), 'Deadlock found when trying to get lock')) {
                $jsonData = $this->jsonSerializer->unserialize($message->getBody());
                $this->rabbitMqHelper->publishErrMessage(Data::TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);

                $this->logRequeue($e->getMessage() . "::" . $message->getBody());
            } else {
                $jsonData = $this->jsonSerializer->serialize($publishData);
                $jsonData = rtrim((string)$jsonData, '}') . ', "Message" :"' . $this->jsonSerializer->unserialize($message->getBody()) . '"}';
                $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
            }

            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Product Create Update'),
                $message->getBody()
            );
        }
        return null;
    }

    protected function createConfigurableProduct(array $productArr, EnvelopeInterface &$message)
    {
        if (!isset($productArr['simple_products'])) {
            $errMessage = 'Payload missing simple product(s)';
            $this->logError($errMessage);
            $this->rabbitMqHelper->sendErrorEmail(
                $errMessage,
                __('Product Create Update'),
                $message->getBody()
            );
            return;
        }
        $simpleProductIds = [];
        foreach ($productArr['simple_products'] as $simple_product) {
            list($id, $sku) = $this->createSimpleProduct($simple_product, $message);
            if (empty($id) || empty($sku)) {
                $this->logError(sprintf("Error product-id missing for simple product. Payload : %s", json_encode($productArr)));
                continue;
            }
            $simpleProductIds[] = ['id' => $id, 'sku' => $sku];
        }

        // logging
        $this->logSuccess("Simple product-ids : " . json_encode($simpleProductIds));

        // Process the productArr for configurable
        unset($productArr['simple_products']);

        // create configurable product
        list($configurableId, $configurableSKU) = $this->createSimpleProduct($productArr, $message);

        // if configurable not created/exist
        if (empty($configurableId)) {
            // notify client about unable to create configurable parent
            $errMessage = sprintf(
                "Configurable product : %s is not created",
                $productArr['sku'],
            );
            $this->logError($errMessage);
            $this->rabbitMqHelper->sendErrorEmail(
                $errMessage,
                __('Product Create Update'),
                $message->getBody()
            );
            return;
        }

        // configurable option attribute creation
        $this->createConfigurableOptionAttribute($configurableId, $productArr['configurable_product_options']);

        // finalize the child sku to be linked
        $childSKUs = $this->getValidateChildSKUToLink($configurableSKU, array_column($simpleProductIds, 'sku'));

        if (count($childSKUs) < count($simpleProductIds)) {
            // notify client about the not linked sku
            $childNotLinked = array_diff(array_column($simpleProductIds, 'sku'), $childSKUs);
            $errMessage = sprintf(
                "Child product(s) skus [%s] are not linked to configurable product: %s",
                implode(', ', $childNotLinked),
                $configurableSKU
            );
            $this->logError($errMessage);
            $this->rabbitMqHelper->sendErrorEmail(
                $errMessage,
                __('Product Create Update'),
                $message->getBody()
            );
        }

        if (!empty($childSKUs)) {
            // finally linking
            $this->linkProducts($configurableSKU, $childSKUs);
        }

        // logging
        $this->logSuccess("Configurable product-id : " . json_encode($configurableId));

        /*update out of stock product*/
        $this->setProductsToInStock();
    }

    /**
     * get all the category id
     *
     * @return array
     */
    public function getCategoryIds(int $productId)
    {
        $categoryIds = $this->productCategory->getCategoryIds($productId);
        $category = [];
        if ($categoryIds) {
            $category = array_unique($categoryIds);
        }
        return $category;
    }

    /**
     * @param $removableCategoryIds
     * @param $sku
     * @param $oldExistingCategory
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws StateException
     */
    private function removableCategoryIds($removableCategoryIds, $sku, $oldExistingCategory)
    {
        foreach ($removableCategoryIds as $removedId) {
            if (!empty($oldExistingCategory) && in_array($removedId, $oldExistingCategory)) {
                $this->categoryLink->deleteByIds($removedId, $sku);
            }
        }
    }

    /**
     * @param string $message
     * @return void
     */
    private function logError(string $message): void
    {
        if ($this->rabbitMqHelper->isLoggingEnabled()) {
            $errorLogger = $this->rabbitMqHelper->getRabbiMqLogger(
                Data::CATALOG_PRODUCT_CREATE_UPDATE_ERROR_LOG_FILE
            );
            $errorLogger->debug($message);
        }
    }

    private function logSuccess(string $message): void
    {
        if ($this->rabbitMqHelper->isCustomLogEnabled()) {
            $successLogger = $this->rabbitMqHelper->getRabbiMqLogger(
                Data::CATALOG_PRODUCT_CREATE_UPDATE_SUCCESS_LOG_FILE
            );
            $successLogger->debug($message);
        }
    }

    private function logRequeue(string $message): void
    {
        if ($this->rabbitMqHelper->isLoggingEnabled()) {
            $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                Data::CATALOG_PRODUCT_REQUEUE_LOG_FILE
            );
            $logger->debug($message);
        }
    }

    /**
     * @return ?int
     */
    public function getProductVisibilityAttributeId(): ?int
    {
        $visibilityAttributeCodeIDSelect = $this->resourceCon->getConnection()->select();
        $visibilityAttributeCodeIDSelect->from(
            ['cpe' => $this->resourceCon->getConnection()->getTableName('eav_attribute')],
            ['attribute_id']
        )->where('attribute_code = "visibility"');
        $resultVisibilityAttributeCodeIDSelect = $this->resourceCon->getConnection()->fetchCol($visibilityAttributeCodeIDSelect);
        if (isset($resultVisibilityAttributeCodeIDSelect)
            && isset($resultVisibilityAttributeCodeIDSelect[0])
            && !empty($resultVisibilityAttributeCodeIDSelect[0])) {
            return (int)current($resultVisibilityAttributeCodeIDSelect);
        }
        return null;
    }

    /**
     * @param $custom_attributes
     * @param array $defaultConfigurationAttributes
     * @param ProductInterface $product
     * @return array
     */
    private function processCustomAttribute($custom_attributes, array $defaultConfigurationAttributes, ProductInterface $product): array
    {
        $defaultConfigurationValues = [];
        $tempRelatedItems = [];
        $description = ['description', 'short_description'];
        foreach ($custom_attributes as $custom_attribute) {
            if (in_array($custom_attribute['attribute_code'], $description)) {
                continue;
            }
            if (in_array($custom_attribute['attribute_code'], $defaultConfigurationAttributes)) {
                $defaultConfigurationValues[$custom_attribute['attribute_code']] = $custom_attribute['value'] . ":--" . array_search($custom_attribute['attribute_code'], $defaultConfigurationAttributes);
            }
            if (isset($custom_attribute['attribute_code']) && $custom_attribute['attribute_code'] == 'tax_class' && !empty($custom_attribute['value'])) {
                $taxClassId = $this->product->getTaxClassId($custom_attribute['value']);
                $product->setCustomAttribute('tax_class_id', $taxClassId);
                continue;
            }
            $attributeOption = $this->product->getOptionIdByLabel($custom_attribute['attribute_code'], $custom_attribute['value']);
            $product->setCustomAttribute($custom_attribute['attribute_code'], $attributeOption);
            if ($custom_attribute['attribute_code'] == 'frame_default_sku') {
                $product->setCustomAttribute('frame_default_sku_configurable', $attributeOption);
            }
            if ($custom_attribute['attribute_code'] != 'related_items') {
                continue;
            }
            $nProdId = (int)$product->getId();
            if (empty($nProdId)) {
                continue;
            }
            if (!empty($custom_attribute['value'])) {
                $tempRelatedItems = $custom_attribute['value'];
                $this->setRelatedProducts($nProdId, $custom_attribute['value']);

            } else {
                $this->deleteRelatedProducts($nProdId);
            }
        }
        return [$defaultConfigurationValues, $tempRelatedItems];
    }

    /**
     * @param array $productArr
     * @param array $categoryIds
     * @param int $productId
     * @return array
     */
    private function processExtensionAttribute(array $productArr, array $categoryIds, int $productId): array
    {
        $deleteCategory = false;
        $removableCategoryIds = [];
        if (!empty($productArr['extension_attributes'])) {
            foreach ($productArr['extension_attributes'] as $key => $extAttrData) {
                if ($key == 'category_links' && (is_countable($extAttrData) ? count($extAttrData) : 0) > 0) {
                    foreach ($extAttrData as $data) {
                        if (!empty($data['category'])) {
                            $categoryIds[] = $this->product->getCategoryIdFromName($data['category']);
                        }
                    }
                    $existingProductCategoriesIds = $this->getCategoryIds($productId);
                    $deleteCategory = false;
                    if (!empty($existingProductCategoriesIds)) {
                        $deltaCategoryIds = $categoryIds;
                        $removableCategoryIds = array_diff($existingProductCategoriesIds, $deltaCategoryIds);
                        // Remove unwanted old categories
                        if (!empty($removableCategoryIds)) {
                            $deleteCategory = true;
                        }
                    }
                }
            }
        }
        return [$deleteCategory, $categoryIds, $removableCategoryIds];
    }

    /**
     * @param $categoryIds
     * @param $product
     * @param $tableCategoryProductRelation
     * We added as Fix as we opened ticket(#383572) with magento
     * Issue : $product->setCategoryIds($categoryIds) is not saving data in 'catalog_category_product' table but save in *'category_ids' attribute under 'catalog_product_entity_varchar' table
     */
    private function categoryProductAssociationCheck($categoryIds, $product, $tableCategoryProductRelation)
    {
        foreach ($categoryIds as $categoryIdToInsert) {
            if ($categoryIdToInsert != null && $categoryIdToInsert > 0) {
                $data = ["category_id" => $categoryIdToInsert, "product_id" => $product->getId(), "position" => false];
                $connection = $this->resourceCon->getConnection();
                $connection->insertOnDuplicate($tableCategoryProductRelation, $data);
            }
        }
    }

    /**
     * Method used to log message .
     *
     * @param $data
     * @param $message
     */
    private function errorLoggerForProductCreateUpdate($data, $message): void
    {
        try {
            $publishData = ['error' => $message];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData = rtrim((string)$jsonData, '}') . ', "Message" :"' . $data . '"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_PRODUCT_CREATE_UPDATE, $jsonData);
        } catch (Exception $e) {
            $this->logError($e->getMessage());
        }
    }

    /**
     * Set related products
     *
     * @param int $parentId
     * @param string $commaSeparatedSkus
     */
    private function setRelatedProducts(int $parentId, string $commaSeparatedSkus)
    {
        try {
            $connection = $this->resourceCon->getConnection();
            $select = $connection->select();
            $select->reset();
            $select->from(
                ['cpe' => 'catalog_product_entity'],
                ['row_id']
            )->where('entity_id = ' . $parentId);
            $ProdRowId = $connection->fetchOne($select);

            if (!empty($ProdRowId)) {
                $connection->delete(
                    'catalog_product_link',
                    [
                        'product_id = ' . $ProdRowId,
                        'link_type_id = 1'
                    ]
                );

                $relatedSkus = '"' . str_replace(',', '","', $commaSeparatedSkus) . '"';
                $select->reset();
                $select->from(
                    ['cpe' => 'catalog_product_entity'],
                    ['entity_id']
                )->where('sku IN (' . $relatedSkus . ')');
                $relatedEntityIds = $connection->fetchCol($select);

                if (is_array($relatedEntityIds) && !empty($relatedEntityIds)) {
                    foreach ($relatedEntityIds as $relatedEntityId) {
                        $connection->insertOnDuplicate(
                            'catalog_product_link',
                            [
                                'product_id' => $ProdRowId,
                                'linked_product_id' => $relatedEntityId,
                                'link_type_id' => 1
                            ],
                            ['linked_product_id']
                        );
                    }
                }
            }
        } catch (Exception) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLoggerForProductCreateUpdate(
                    'Related Skus = ' . $commaSeparatedSkus,
                    __('Failed to set related products')
                );
            }
        }
    }

    /**
     * delete related products
     *
     * @param int $parentId
     */
    private function deleteRelatedProducts($parentId)
    {
        try {
            $connection = $this->resourceCon->getConnection();
            $select = $connection->select();
            $select->reset();
            $select->from(
                ['cpe' => 'catalog_product_entity'],
                ['row_id']
            )->where('entity_id = ' . $parentId);
            $ProdRowId = $connection->fetchOne($select);

            if (!empty($ProdRowId)) {
                $connection->delete(
                    'catalog_product_link',
                    [
                        'product_id = ' . $ProdRowId,
                        'link_type_id = 1'
                    ]
                );
            }
        } catch (Exception) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLoggerForProductCreateUpdate(
                    'Parent product entity id = ' . $parentId,
                    __('Failed to delete related products')
                );
            }
        }
    }

    /**
     * @param $productId
     * @param string $entityType
     */
    private function removeExistingRewrites($productId, $entityType = 'product')
    {
        try {
            $connection = $this->resourceCon->getConnection();
            $select = $connection->select();
            $select->reset();
            $select->from(
                ['urw' => 'url_rewrite'],
                ['entity_id', 'entity_type']
            )->where('entity_id = ?', $productId)->where('entity_type = ?', $entityType);
            $oldRewriteRecords = $connection->fetchAll($select);
            // delete the existing old url rewrite records before creating new one
            foreach ($oldRewriteRecords as $oldRow) {
                $connection->delete(
                    'url_rewrite',
                    [
                        'entity_id = ?' => $oldRow['entity_id'],
                        'entity_type = ?' => $entityType
                    ]
                );
            }

        } catch (Exception) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLoggerForProductCreateUpdate(
                    'product entity id = ' . $productId,
                    __('Failed to delete url rewrite records')
                );
            }
        }
    }

    /**
     * Prepare the validate child product-sku(s) that can be linked to parent product
     *
     * @param string $parentSKU
     * @param array $childSKUs
     * @return array
     */
    private function getValidateChildSKUToLink(string $parentSKU, array $childSKUs): array
    {
        $validateChildSKUs = [];
        $attributeOptions = [];
        $parentProduct = $this->productRepository->get($parentSKU);
        $productEntityTypeId = $this->eavConfig->getEntityType(ProductModel::ENTITY)->getEntityTypeId();
        foreach ($parentProduct->getTypeInstance()->getConfigurableAttributes($parentProduct) as $attribute) {
            $productAttribute = $attribute->getData('product_attribute');
            $attributeCode = $productAttribute->getAttributeCode();
            $optionValues = $this->optionManagement->getItems($productEntityTypeId, $attributeCode);
            foreach ($optionValues as $optionValue) {
                if (!empty($optionValue->getValue())) {
                    $attributeOptions[$attributeCode][] = $optionValue->getValue();
                }
            }
        }
        foreach ($childSKUs as $childSKU) {
            $childProduct = $this->productRepository->get($childSKU);
            $isValid = true;
            foreach ($attributeOptions as $attributeCode => $attributeOption) {
                $isValid &= (!empty($childProduct->getData($attributeCode)) &&
                    in_array($childProduct->getData($attributeCode), $attributeOption));

                if (!$isValid) {
                    break;
                }
            }
            if ($isValid) {
                $validateChildSKUs[] = $childSKU;
            }
        }

        return $validateChildSKUs;
    }

    /**
     * @param string $parentSKU
     * @param string[] $childSKUs
     * @return void
     */
    private function linkProducts(string $parentSKU, array $childSKUs): void
    {
        foreach ($childSKUs as $childSKU) {
            try {
                $this->linkManagement->addChild($parentSKU, (string)$childSKU);
            } catch (StateException $e) {
                $this->logError($e->getMessage());
                if ($e->getMessage() != 'The product is already attached.') {
                    throw $e;
                }
            }
        }
    }

    /**
     * @param int $parentId
     * @param array $configurableProductOptions
     * @return void
     * @throws Exception
     */
    private function createConfigurableOptionAttribute(int $parentId, array $configurableProductOptions): void
    {
        $position = 0;
        foreach ($configurableProductOptions as $attribute) {
            if ($attribute == 'frame_default_sku') {
                // changing the `frame_default_sku` to `frame_default_sku_configurable`
                $attribute .= '_configurable';
            }
            /** @var ConfigurableAttrModel $attr */
            $attributeModel = $this->configurableAttrModelFactory->create();
            $attributeId = $this->eavAttribute
                ->getIdByCode(ProductModel::ENTITY, $attribute);
            $data = [
                'attribute_id' => $attributeId,
                'product_id' => $parentId,
                'position' => $position++
            ];

            try {
                $attributeModel->setData($data);
                $this->configurableAttrResourceModel->save($attributeModel);
            } catch (AlreadyExistsException $e) {
                $this->logError($e->getMessage());
            }
        }
    }

    private function createConfigurableOptionValue(array $productArr): void
    {
        $productEntityID = $this->eavConfig->getEntityType(ProductModel::ENTITY)->getEntityTypeId();
        $magentoAttributes = $this->prepareConfigurableOptionData($productArr);

        foreach ($magentoAttributes as $attributeCode => $newOptions) {
            foreach ($newOptions as $value) {
                try {
                    $option = $this->attributeOptionInterfaceFactory->create();
                    $option->setLabel($value);
                    $option->setValue($value);
                    $this->optionManagement->add($productEntityID, $attributeCode, $option);
                } catch (InputException $e) {
                    $this->logError($e->getMessage());
                    if (!str_contains($e->getMessage(), 'is already exists.')) {
                        throw $e;
                    }
                }
            }
        }
    }

    private function prepareConfigurableOptionData(array $productArr): array
    {
        $data = [];

        if (isset($productArr['simple_products'])) {
            foreach ($productArr['simple_products'] as $simpleProduct) {
                /** @var array $customAttributes */
                $customAttributes = $simpleProduct['custom_attributes'];
                if (empty($customAttributes)) {
                    continue;
                }
                foreach ($customAttributes as $customAttribute) {
                    if (in_array($customAttribute['attribute_code'], array_keys($this->configurableOptionArray))) {
                        $attribute = $this->configurableOptionArray[$customAttribute['attribute_code']];
                        if (!empty($attribute['can_create']) && $attribute['can_create'] === true) {
                            $data[$attribute['attribute_code']][] = $customAttribute['value'];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return AdapterInterface
     */
    public function setProductsToInStock(): AdapterInterface
    {
        $connection = $this->resourceCon->getConnection();
        $magentoCatalogInventoryStockItemTable = $connection->getTableName('cataloginventory_stock_item');
        $connection->update(
            $magentoCatalogInventoryStockItemTable,
            ['is_in_stock' => 1],
            'is_in_stock = 0'
        );
        return $connection;
    }

    /**
     * @param array $productArr
     * @return int
     */
    public function getProductCatalogVisibility(array $productArr): int
    {
        if (in_array($productArr['attribute_set'], $this->framesAndMats)) {
            return Visibility::VISIBILITY_IN_CATALOG;
        }
        if (empty($productArr['visibility'])) {
            return Visibility::VISIBILITY_NOT_VISIBLE;
        }
        return $this->product->getVisibilityIdByName($productArr['visibility']);
    }
}
