<?php
declare(strict_types=1);

namespace Wendover\Catalog\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class WendoverViewModel implements ArgumentInterface
{
    const DOWNLOAD_LINK_IS_ENABLED = 'perficient_mycatalog/pdfcrowd/enable_download_link';
    const URL_OVERWRITE = 'bloomreach/general/url_overwrite';
    const XML_PATH_RESTRICT_CART_CHECKOUT = 'restrictcustomer/cartcheckout/is_enabled';
    const QUICK_SHIP_FIELD = 'is_quick_ship';
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly ProductRepository $productRepository,
        private readonly UrlInterface $urlInterface,
        private readonly RequestInterface $request,
        private readonly Session $customerSession,
        private readonly CategoryRepositoryInterface $categoryRepository
    ){
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->urlInterface->getCurrentUrl();
    }

    public function getDownloadLinkConfig()
    {
        return $this->scopeConfig->getValue(
            self::DOWNLOAD_LINK_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $sku
     * @return Collection
     */
    public function getProductCollection($sku)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('sku', ['in' => [$sku]]);
        return $collection;
    }

    /**
     * @param $id
     * @return ProductInterface|mixed|null
     * @throws NoSuchEntityException
     */
    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }

    /**
     * @return bool
     */
    public function getIsLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getUrlOverwriteConfig()
    {
        return $this->scopeConfig->getValue(
            self::URL_OVERWRITE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration if is restricted add to cart, cart, checkout
     */
    public function isRestrictCartAndCheckout(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_RESTRICT_CART_CHECKOUT,
            ScopeInterface::SCOPE_STORE
        );
    }

        /**
     * @return int
     */
    public function isFromQuickShip()
    {
        $fromQuickShip = 0;
        $categoryId = (int)$this->request->getParam('id', false);
        if (!empty($categoryId)) {
            $catData = $this->categoryRepository->get($categoryId);
            if (!empty($catData) && $catData->getName() === "Quick Ship") {
                $fromQuickShip = 1;
            }
        }

        return $fromQuickShip;
    }

    public function getSearchURL()
    {
        return $this->urlInterface->getUrl('catalogsearch/result/');
    }

    public function getPLPProductCollection($searckWord, $ids)
    {
        $params = $this->request->getParams();
        $pagePerItems = isset($params['product_list_limit']) ? $params['product_list_limit'] : 12;
        $page = isset($params['p']) ? $params['p'] : 1;
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('sku', array('like' => '%'.$searckWord.'%'));
        $collection->addCategoriesFilter(['in' => $ids]);
        $collection->setPageSize($pagePerItems);
        $collection->setCurPage($page);

        return $collection;
    }

    public function getFrameAttribute($product)
    {
        try {
            if ($product) {
                $width = $product->getData('frame_width');
                $height = $product->getData('frame_depth');
                $depth = $product->getData('frame_rabbet_depth');
                $dimension = '';
                $dimension = (!empty($width))? $width.'"w':'';
                $dimension.= (!empty($height))? (!empty($dimension)? ' x ':'').$height . '"h':'';
                $dimension.= (!empty($depth))? (!empty($dimension)? ' x ':'').$depth . '"d':'';
                return $dimension;
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * Method used to configuration value of the given key.
     *
     * @param string $path
     * @return string
     */
    public function getConfigValue($path = ''): ?string
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }
}
