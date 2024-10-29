<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Productimize
 * @copyright  Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Productimize\Block;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;


/**
 * Class Productcustomizer
 * @package DCKAP\Productimize\Block
 */
class Productimizehome extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{

    /**
     * Productimize cache tag
     */
    const CACHE_TAG = 'productimize_item';
    /**
     * @var
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $helperData;

    protected $productRepository;

    protected $perficientHelper;

    protected $productimizeHelper;

    protected $catalogOutput;

    protected $catalogImage;

    protected $checkoutCartHelper;

    /**
     * Productcustomizer constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \DCKAP\Productimize\Model\ResourceModel\Productcustomizer\CollectionFactory $productcustomizerCollectionFactory
     * @param \DCKAP\Productimize\Model\Productcustomizer $productcustomizerFactory
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        ProductRepository $productRepository,
        \Perficient\Catalog\Helper\Data $perficientHelper,
        \DCKAP\Productimize\Helper\Data $productimizeHelper,
        \Magento\Catalog\Helper\Output $catalogOutput,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Magento\Checkout\Helper\Cart $checkoutCartHelper,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_productloader = $_productloader;
        $this->productRepository = $productRepository;
        $this->storeManager = $context->getStoreManager();
        $this->perficientHelper = $perficientHelper;
        $this->productimizeHelper = $productimizeHelper;
        $this->catalogOutput = $catalogOutput;
        $this->catalogImage = $catalogImage;
        $this->checkoutCartHelper = $checkoutCartHelper;
    }

    public function getPerficientHelper()
    {
        return $this->perficientHelper;
    }

    public function getProductimizeHelper()
    {
        return $this->productimizeHelper;
    }

    public function getCatalogOutput()
    {
        return $this->catalogOutput;
    }

    public function getCatalogImageHelper()
    {
        return $this->catalogImage;
    }

    public function getCheckoutCartHelper()
    {
        return $this->checkoutCartHelper;
    }

    /**
     * @return mixed
     */
    public function getStoreBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, true);
    }

    public function getProductImageUrl($product)
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
    }

    public function getCustomImageTypeUrl($product, $productImgType) {
        $images = array();
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' ;
        //$images['croppedImage'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getResource()->getAttribute('cropped')->getFrontend()->getValue($product);

        foreach ($productImgType as $key => $value) {
            if ($key == "base") {
                $customImg = $mediaUrl . $product->getImage();
            } else if ($key == "thumbnail") {
                $image = $product->getImage();
                $customImg = $this->catalogImage->init($product, 'category_page_grid')
                    ->setImageFile($image)
                    ->getUrl();
            }
            else {
                $customImg = $mediaUrl . $product->getResource()->getAttribute($key)->getFrontend()->getValue($product);
            }
            if ($customImg) {
                $images[$value] = $customImg;
            }
        }

        return $images;
    }

    public function getProductById($productId){
        return $this->productRepository->getById($productId);
    }

    /**
     * @return mixed
     */
    public function getProductimizeDetail()
    {
        return $this->_coreRegistry->registry('product_detail');
    }

    /**
     * @return mixed
     */
    public function getAttributeDetail()
    {
        return $this->_coreRegistry->registry('attribute_detail');
    }

    /**
     * @return mixed
     */
    public function getGlobalSettingDetail()
    {
        return $this->_coreRegistry->registry('global_text_detail');
    }

    /**
     * @param $productid
     * @return $this|string
     */
    public function getProductDetails($productid)
    {
        $ggg = $this->_productcustomizerFactory->getProductDetails($productid);
        return $ggg;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . 'list'];
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getEditProductQryString()
    {
        $editQryString = $this->getRequest()->getParams();
        //if (count)
        return $editQryString;

    }

    public function getProductCustomizationAjaxUrl()
    {
        return $this->getUrl() . 'productimize/index/index';
    }
    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}