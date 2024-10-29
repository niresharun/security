<?php
/**
 * Modify Customer Account Sales Order Navigation
 * @category: Magento
 * @package: Perficient/Sales
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Sales
 */
declare(strict_types=1);

namespace Perficient\Sales\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

abstract class View extends \Magento\Sales\Controller\AbstractController\View
{
    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Action\Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        private readonly OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context, $orderLoader, $resultPageFactory);
    }

    /**
     * Order view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $orderId = $this->getRequest()->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
            if ($order->getStatus() === 'complete') {
                $navigationBlock->setActive('sales/order/shipped');
            } else {
                $navigationBlock->setActive('sales/order/history');
            }
        }
        return $resultPage;
    }
}
