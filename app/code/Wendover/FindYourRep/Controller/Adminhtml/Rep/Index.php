<?php

namespace Wendover\FindYourRep\Controller\Adminhtml\Rep;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context               $context,
        protected PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Wendover_FindYourRep::representative_grid_list');
        $resultPage->getConfig()->getTitle()->prepend((__('Manage Representative')));
        return $resultPage;
    }

    /**
     * @return true
     */
    protected function _isAllowed()
    {
        return true;
    }
}
