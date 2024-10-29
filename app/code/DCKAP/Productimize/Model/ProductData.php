<?php

namespace DCKAP\Productimize\Model;

//use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as EavCollectionFactory;
//use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
//use Magento\Eav\Model\Config;
//use Magento\Framework\Filesystem\DirectoryList;
use Perficient\Productimize\Model\ProductConfiguredPrice;
use DCKAP\Productimize\Helper\Data AS ProductimizeHelper;
use Psr\Log\LoggerInterface;
//use Magento\Eav\Api\AttributeSetRepositoryInterface as AttributeSetRepositoryInterface;

class ProductData
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    private $productFactory;
    private $productCollectionFactory;
    private $eavCollectionFactory;
    private $eavConfig;
    private $directory;
    private $dckapProductimizeHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $perficientConfiguredprice;

    private $attributeSetRepository;


    public function __construct(
        //ResourceConnection $resourceConnection,
        ProductFactory $productFactory,
        //CollectionFactory $productCollectionFactory,
        //EavCollectionFactory $eavCollectionFactory,
        //Config $eavConfig,
        //DirectoryList $directory,
        ProductConfiguredPrice $perficientConfiguredprice,
        ProductimizeHelper $dckapProductimizeHelper,
        LoggerInterface $logger
        //AttributeSetRepositoryInterface $attributeSetRepository
    )
    {
       // $this->resourceConnection = $resourceConnection;
        $this->productFactory = $productFactory;
       // $this->productCollectionFactory = $productCollectionFactory;
        //$this->eavCollectionFactory = $eavCollectionFactory;
        //$this->eavConfig = $eavConfig;
        //$this->directory = $directory;
        $this->perficientConfiguredprice = $perficientConfiguredprice;
        $this->dckapProductimizeHelper = $dckapProductimizeHelper;
        $this->logger = $logger;
       // $this->attributeSetRepository = $attributeSetRepository;
    }

    public function getProductById($productId)
    {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            return $product = $this->productFactory->create()->load($productId);
        } catch (\Exception $e) {
            $this->logger->error(__('Unable to load product #%1.', $productId));
            $this->logger->error($e->getMessage());
            return null;
        }
    }
    public function getProductBySku($productSku)
    {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            return $product = $this->productFactory->create()->loadByAttribute('sku', $productSku);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getProductImages($sku,$productImgType)  {
        $productimages = array();
        //$product = $this->getProductById($currProduct->getId());
        $productSku = $this->getProductBySku($sku);
        $product = $this->getProductById($productSku->getId());

        $productimages = $product->getMediaGalleryImages();


        $finalProduct = array();
        if ($productimages) {


            foreach ($productImgType as $key => $value) {
               $cornerImg = $productimages->getItemByColumnValue('label', $key);
                if ($cornerImg) {
                    $finalProduct[$value] = $cornerImg->getUrl();
                }
            }


        }
        return $finalProduct;
    }


}
