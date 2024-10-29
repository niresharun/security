<?php

namespace Wendover\ReportImages\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

/**
 * Class Result
 *
 * @package Wendover\ReportImages\Controller\Adminhtml\Index
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Backend\App\Action $context
     */
    public function __construct(
        private readonly \Magento\Framework\View\Result\PageFactory $pageFactory,
        Action\Context                                              $context
    ) {
        parent::__construct($context);
    }

    /**
     * Execute method.
     *
     * @return null
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend((__('Product Images Missing')));
        return $page;
    }
}
