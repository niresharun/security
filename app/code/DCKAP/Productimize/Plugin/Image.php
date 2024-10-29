<?php

namespace DCKAP\Productimize\Plugin\CheckoutCart;

class Image

{

    public function afterGetImage(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $result)

    {
    $item = $subject->getItem();
    $options = $item->getProductOptions();
    $imageUrl = 0;

    foreach ($options as $_option) {
    	if($_option['label'] == 'designedImage'){
    		$imageUrl = $_option['value'];	
    	}
    }
    if($imageUrl) {
     	$result->setImageUrl($imageUrl);
    }

    return $result;

    }

}