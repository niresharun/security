<?php

namespace Wendover\FindYourRep\Controller\Rep;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ActionInterface;
use Wendover\FindYourRep\ViewModel\Rep;

class Index implements ActionInterface
{
    /**
     * @param PageFactory $_pageFactory
     */
    public function __construct(
        protected PageFactory $_pageFactory,
        protected Rep $repViewModel,
        protected RedirectFactory $resultRedirectFactory
    ) {
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if ($this->repViewModel->isCustomerLoggedIn()) {
            return  $this->resultRedirectFactory->create()
                ->setPath('customer/account');
        }
        return $this->_pageFactory->create();
    }
}
