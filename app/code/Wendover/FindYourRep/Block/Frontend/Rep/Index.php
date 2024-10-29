<?php

namespace Wendover\FindYourRep\Block\Frontend\Rep;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @param Context $context
     * @param StoreManagerInterface $_storeManager
     * @param UrlInterface $_urlInterface
     */
    public function __construct(
        Context                                $context,
        StoreManagerInterface                  $_storeManager,
        private readonly UrlInterface          $_urlInterface,
        array                                  $data = []
    )
    {
        parent::__construct($context, $data);
    }

    public function getRepURL(): string
    {
        return $this->_urlInterface->getUrl('representative/rep/repdata');
    }
}
