<?php

namespace DCKAP\Productimize\Model\Source;
use Magento\Store\Model\StoreManagerInterface;

class ProductimizeSiteUrl implements \Magento\Framework\Option\ArrayInterface
{
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager){
            $this->storeManager = $storeManager;
        }

    public function toOptionArray()
    {
        $siteUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        return [
            [
                'value' => $siteUrl,
                'label' => __('Staging')
            ]
        ];
    }

}