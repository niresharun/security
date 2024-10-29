<?php

namespace Perficient\Productimize\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perficient\Catalog\Helper\Data;
use DCKAP\Productimize\Helper\Data as DckapHelper;

class ProductimizeData implements ArgumentInterface
{
    public function __construct(
     protected   Data $helper,
    protected   DckapHelper $dckapHelper
    ) {

    }

    public function getValidCustomizedOptions($pzCartProperties, $sizeOnly = false)
    {
        return $this->helper->getValidCustomizedOptions($pzCartProperties, $sizeOnly = false);
    }

    public function getImageurlfrombuyrequestdata($buyRequestdata)
    {
        return $this->dckapHelper->getImageurlfrombuyrequestdata($buyRequestdata);
    }
}
