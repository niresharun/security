<?php

namespace Wendover\Checkout\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\Helper\Data;

class ShippingAmount implements ArgumentInterface
{
    protected $priceHelper;

    public function __construct(Data $priceHelper)
    {
        $this->priceHelper = $priceHelper;
    }
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
