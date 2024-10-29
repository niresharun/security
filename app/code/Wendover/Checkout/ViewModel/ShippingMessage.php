<?php
namespace Wendover\Checkout\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ShippingMessage implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Shiiping Method Popup Notice
     */
    const XML_SHIPPING_POPUP_NOTICE = 'carriers/flatrate/popup_notice';
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getShippingPopup()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_SHIPPING_POPUP_NOTICE, $storeScope);
    }
}
