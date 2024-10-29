<?php

namespace DCKAP\Productimize\Plugin\Wishlist\Block;

use Magento\Framework\App\Request\Http;
use DCKAP\Productimize\Helper\Data;
use DCKAP\Productimize\Model\ProductimizeCalculation;

class AbstractBlock
{

    private $request;
    private $productimizeHelperData;
    private $productimizeProductData;

    public function __construct(
        Http $request,
        Data $productimizeHelperData,
        ProductimizeCalculation $productimizeProductData
    )
    {

        $this->request = $request;
        $this->productimizeHelperData = $productimizeHelperData;
        $this->productimizeProductData = $productimizeProductData;
    }

    /**
     * @param \Magento\Wishlist\Block\AbstractBlock $subject
     * @param $result
     * @param $item
     * @param array $additional
     * @return string
     */
    public function afterGetProductUrl(
        \Magento\Wishlist\Block\AbstractBlock $subject,
        $result,
        $item
    )
    {
        $result = preg_replace('/(?:&|(\?))' . '___store' . '=[^&]*(?(1)&|)?/i', "$1", $result);
        if ($item) {
            $buyRequest = $item->getBuyRequest();
            if (is_object($buyRequest)) {
                if ($buyRequest->getEditId()) {
                    $currentActionName = $this->request->getFullActionName();
                    if ($currentActionName == "wishlist_index_send" || $currentActionName == 'wishlist_shared_index') {
                        $productId = $item->getProductId();
                        $currProduct = $this->productimizeProductData->getProductById($productId);
                        $defaultConf = $currProduct->getDefaultConfigurations();
                        $pzCartProperties = json_decode($buyRequest['pz_cart_properties'], true);
                        $productimizeShareUrl = $this->productimizeHelperData->generateShareEditUrl($pzCartProperties, $defaultConf);
                        $result .= '&' . $productimizeShareUrl;
                    } else {
                        $result = $subject->getItemConfigureUrl($item) . 'customizer/' . $item->getProductId();
                    }
                }
            }
        }
        return $result;
    }
}