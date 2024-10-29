<?php

namespace DCKAP\Productimize\Plugin\Block\Cart\Item;

class RendererPlugin
{
    /**
     * Override cart image, if designer product
     *
     * @param \Magento\Checkout\Block\Cart\Item\Renderer $subject
     * @param \Magento\Catalog\Block\Product\Image $result
     * @see \Magento\Checkout\Block\Cart\Item\Renderer::getImage
     */
    public function afterGetImage(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $result)
    {
        $item = $subject->getItem();
        $itemData = $item->getBuyRequest()->getData();
        if (isset($itemData['pz_cart_properties'])) {
            if ($itemData['pz_cart_properties'] != '') {
                $pzCartproperties = json_decode($itemData['pz_cart_properties'], true);
                if(isset($pzCartproperties['CustomImage'])){
                    $result->setImageUrl($pzCartproperties['CustomImage']);
                }
            }
        }
        return $result;

    }
}