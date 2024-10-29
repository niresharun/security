<?php
declare(strict_types=1);

namespace Wendover\Theme\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Msrp\Helper\Data as MsrpHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;
use Perficient\Catalog\Helper\Data as CatalogHelper;
use DCKAP\Productimize\Helper\Data as DCKAPHelper;
use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Perficient\QuickShip\Helper\Data as QuickShipHelper;

class WendoverViewModel implements ArgumentInterface
{
    const SURCHARGE_PRODUCT_SKU_CONFIGURATION_PATH = 'order/general/surcharge_sku';

    /**
     * Wendover ViewModel Constructor
     */
    public function __construct(
        private readonly MsrpHelper $msrpHelper,
        private readonly ProductimizeHelper $productimizeHelper,
        private readonly CatalogHelper $catalogHelper,
        private readonly DCKAPHelper $dckapHelper,
        private readonly QuickShipHelper $quickShipHelper,
        private readonly CatalogImageHelper $imageHelper,
        private readonly ScopeConfigInterface $scopeConfig
    ){
    }

    /**
     * Getting isShowBeforeOrderConfirm of the Product
     */
    public function getBeforeOrderConfirm($product)
    {
        return $this->msrpHelper->isShowBeforeOrderConfirm($product);
    }

    /**
     * Getting Is From QuickShip OR not from QuickShipHelper
     */
    public function getIsFromQuickShip()
    {
        return $this->quickShipHelper->isFromQuickShip();
    }

    /**
     * Getting isMinimalPriceLessMsrp of the Product
     */
    public function getMinimalPriceLessMsrp($product)
    {
        return $this->msrpHelper->isMinimalPriceLessMsrp($product);
    }

    /**
     * Get Surcharge Product Sku
     */
    public function getSurchargeProductSku()
    {
        return $this->scopeConfig->getValue(self::SURCHARGE_PRODUCT_SKU_CONFIGURATION_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Customize EditId from \Perficient\Productimize\Helper\Data
     */
    public function getCustomizeEditId($infoBuyRequest)
    {
        return $this->productimizeHelper->getCustomizeEditId($infoBuyRequest);
    }

    /**
     * Get Edit Url from \Perficient\Productimize\Helper\Data
     */
    public function getEditUrl($url, $id)
    {
        return $this->productimizeHelper->getEditUrl($url, $id);
    }

    /**
     * Get PzCart Properties from \Perficient\Catalog\Helper\Data
     */
    public function getPzCartProperties($infoBuyRequest)
    {
        return $this->catalogHelper->getPzCartProperties($infoBuyRequest);
    }

    /**
     * Get Valid Customized Options from \Perficient\Catalog\Helper\Data
     */
    public function getValidCustomizedOptions($productConfiguration, $expectedData = null)
    {
        return $this->catalogHelper->getValidCustomizedOptions($productConfiguration, false, $expectedData);
    }

    public function isMirrorProduct(ProductInterface $product): bool
    {
        return $this->catalogHelper->isMirrorProduct($product);
    }

    public function getMirrorProductUrl(ProductInterface $product): string
    {
        return $this->catalogHelper->getMirrorProductUrl($product->getId());
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getProductUrl(ProductInterface $product): string
    {
        return $this->catalogHelper->getSimpleProductURL($product);
    }

    public function getProductName(ProductInterface $product): string
    {
        return $this->catalogHelper->getSimpleProductName($product);
    }

    /**
     * Get Cart Product Additional Information from \Perficient\Catalog\Helper\Data
     */
    public function getCartProductAdditionalInformation($jsonObj)
    {
        return $this->catalogHelper->getCartProductAdditionalInformation($jsonObj);
    }

    /**
     * Get Image url from buy request data in \DCKAP\Productimize\Helper\Data
     */
    public function getImageurlfrombuyrequestdata($buyRequest)
    {
        return $this->dckapHelper->getImageurlfrombuyrequestdata($buyRequest);
    }

    /**
     * Get Image url from \Magento\Catalog\Helper\Image
     */
    public function getImageURL($product, $imageId)
    {
        return $this->imageHelper->init($product, $imageId)
            ->constrainOnly(TRUE)
            ->keepAspectRatio(TRUE)
            ->keepTransparency(TRUE)
            ->keepFrame(FALSE)
            ->resize(100, 100)->getUrl();
    }
}
