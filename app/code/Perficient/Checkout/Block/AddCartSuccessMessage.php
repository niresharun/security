<?php

namespace Perficient\Checkout\Block;

use Magento\Checkout\Model\Session;
use Perficient\QuickShip\Helper\Data as QuickShipHelper;

class AddCartSuccessMessage extends \Magento\Framework\View\Element\Template
{

    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        protected Session                                $checkoutSession
    )
    {
        return parent::__construct($context);
    }

    public function isQuickShipCart()
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote->getData(QuickShipHelper::QUICK_SHIP_ATTRIBUTE);
    }
}
