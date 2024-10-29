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

namespace Perficient\Order\CustomerData;

use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Checkout\CustomerData\DefaultItem as ParentDefaultItem;
use Magento\Framework\App\ObjectManager;
use Perficient\Catalog\Helper\Data as PerficientCatalogHelper;
use Perficient\Order\Helper\Data as PerficientOrderHelper;

/**
 * Default cart item
 */
class DefaultItem extends ParentDefaultItem
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * @var \Magento\Msrp\Helper\Data
     */
    protected $msrpHelper;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Catalog\Helper\Product\ConfigurationPool
     */
    protected $configurationPool;
    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;
    /**
     * @var ItemResolverInterface
     */
    private $itemResolver;

    /**
     * DefaultItem constructor.
     * @param \Magento\Framework\Escaper|null $escaper
     * @param ItemResolverInterface|null $itemResolver
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Msrp\Helper\Data $msrpHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        private readonly PerficientOrderHelper $perficientOrderHelper,
        private readonly PerficientCatalogHelper $catalogHelper,
        \Magento\Framework\Escaper $escaper = null,
        ItemResolverInterface $itemResolver = null
    ){
        parent::__construct($imageHelper, $msrpHelper, $urlBuilder, $configurationPool,
                            $checkoutHelper, $escaper, $itemResolver);
        $this->configurationPool = $configurationPool;
        $this->imageHelper = $imageHelper;
        $this->msrpHelper = $msrpHelper;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutHelper = $checkoutHelper;
        $this->escaper = $escaper ?: ObjectManager::getInstance()->get(\Magento\Framework\Escaper::class);
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);
    }

    /**
     * @inheritdoc
     */
    protected function doGetItemData()
    {
        $imageHelper = $this->imageHelper->init($this->getProductForThumbnail(), 'mini_cart_product_thumbnail');
        $productName = $this->escaper->escapeHtml($this->item->getProduct()->getName());
        $surchargeSku = $this->perficientOrderHelper->getSurchargeProductSku();
        $isVisibleInSiteVisibility = $this->item->getProduct()->isVisibleInSiteVisibility();
        $CurrentUserSurchargeStatus = $this->perficientOrderHelper->getCurrentUserSurchargeStatus();
        if ($CurrentUserSurchargeStatus != false && $this->item->getSku() == $surchargeSku) {
            $isVisibleInSiteVisibility = null;
        }
        $buyRequest = $this->item->getBuyRequest();
        $options = [];
        if($buyRequest->getPzCartProperties()) {
            $expectedLabels = null;
            if ($this->catalogHelper->isMirrorProduct($this->item->getProduct())) {
                $expectedLabels = PerficientCatalogHelper::$expectedConfMirrorProductLabel;
            }
            $options = $this->getCustomConfigOptions($buyRequest, $expectedLabels);
        } else {
            $options = $this->getOptionList();
        }
        $editId = 0;
        if($buyRequest->getEditId()) {
           $editId = $buyRequest->getEditId();
        }
        // check quickship product or not
        $quickShip = 0;
        if($buyRequest->getQuickShipProduct()) {
            $quickShip = $buyRequest->getQuickShipProduct();
        }
        return [
            'options' => $options,
            'edit_id' => $editId,
            'is_quick_ship' => $quickShip,
            'qty' => $this->item->getQty() * 1,
            'item_id' => $this->item->getId(),
            'configure_url' => $this->getConfigureUrl(),
            'is_visible_in_site_visibility' => $isVisibleInSiteVisibility,
            'product_id' => $this->item->getProduct()->getId(),
            'product_name' => $productName,
            'product_sku' => $this->item->getProduct()->getSku(),
            'product_url' => $this->getProductUrl(),
            'product_has_url' => $this->hasProductUrl(),
            'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),
            'product_price_value' => $this->item->getCalculationPrice(),
            'product_image' => [
                'src' => $imageHelper->getUrl(),
                'alt' => $imageHelper->getLabel(),
                'width' => $imageHelper->getWidth(),
                'height' => $imageHelper->getHeight(),
            ],
            'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())
                && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),
            'message' => $this->item->getMessage(),
        ];
    }

    /**
     * @param $buyRequest
     * @param null|array $expectedLabels
     * @return array
     */
    public function getCustomConfigOptions($buyRequest, $expectedLabels = null)
    {
        $customOptions = [];
        if($buyRequest) {
            $customConfigProperties = $this->catalogHelper
                ->getValidCustomizedOptions($buyRequest->getPzCartProperties(), false, $expectedLabels);
            $tempOptions = [];
            if(!empty($customConfigProperties['dataArray'])) {
                $i = 0;
                foreach ($customConfigProperties['dataArray'] as $dataLabel=>$dataValue){
                    $tempOptions[$i]['label'] = $dataLabel;
                    $tempOptions[$i]['value'] = $dataValue;
                    $i = $i+1;
                }
                $customOptions = $tempOptions;
            }
        }
        return $customOptions;
    }
}
