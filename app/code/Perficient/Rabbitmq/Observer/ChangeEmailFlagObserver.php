<?php

declare(strict_types=1);

namespace Perficient\Rabbitmq\Observer;

use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class ChangeEmailFlagObserver implements \Magento\Framework\Event\ObserverInterface
{

    protected $quoteRepository;

    protected $logger;

    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {             
               
        try {
            $order= $observer->getData('order');            
            
            if($order->getSysproOrderId()) {
                $order->setCanSendNewEmailFlag(false);
            }
           
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $this;
    }
}
