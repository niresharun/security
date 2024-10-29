<?php

namespace Wendover\MegaMenu\Controller\Adminhtml\SubMenu;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Wendover\MegaMenu\Model\SubMenuFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Hexamarvel\FlexibleForm\Model\FieldSetFactory $fieldSetFactory
     */
    public function __construct(
        Context $context,
        protected PageFactory $resultPageFactory,
        protected SubMenuFactory $subMenuFactory
    ) {
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
      $id = $this->getRequest()->getParam('submenu_id');
        $model = $this->subMenuFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Submenu no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $subMenuId = $this->getRequest()->getParam('submenu_id');
                return $resultRedirect->setPath('*/*/edit/'.$subMenuId);
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Sub Menu') : __('New Submenu'),
            $id ? __('Edit Sub Menu') : __('New Submenu')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Sub Menu'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getSubmenuId() ? $model->getSubmenuTitle() : __('New Submenu'));
        return $resultPage;
    }
}
