<?php
namespace Wendover\Checkout\Observer;
use Magento\Framework\Serialize\SerializerInterface;
class SaveShippingAmountInOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $order->setData('shipping_amount_without_discount', $quote->getShippingAmountWithoutDiscount());
        return $this;
    }
}
