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

namespace Perficient\Sales\Controller\Order;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;

class Shipped implements ActionInterface, OrderInterface, HttpGetActionInterface
{
    /**
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        protected PageFactory $resultPageFactory,
        private readonly RedirectInterface $redirectInterface,
    ) {
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Shipped Orders'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->redirectInterface->getRedirectUrl());
        }
        return $resultPage;
    }
}
