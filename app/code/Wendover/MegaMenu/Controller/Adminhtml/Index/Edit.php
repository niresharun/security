<?php

namespace Wendover\MegaMenu\Controller\Adminhtml\Index;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Wendover\MegaMenu\Model\MenuFactory
     */
    private $menuFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Wendover\MegaMenu\Model\MenuFactory $menuFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Wendover\MegaMenu\Model\MenuFactory        $menuFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->menuFactory = $menuFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('menu_id');
        $model = $this->menuFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Menu no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Menu') : __('New Menu'),
            $id ? __('Edit Menu') : __('New Menu')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Menus'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getMenuId() ? $model->getMenuTitle() : __('New Menu'));
        return $resultPage;
    }
}

