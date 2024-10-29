<?php

namespace Wendover\MegaMenu\Controller\Adminhtml\SubMenu;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action\Context;
use Wendover\MegaMenu\Model\SubMenu;

class Delete extends Action
{
    Protected $model;

    public function __construct(
        Context $context,
        SubMenu $model
    )
    {
        $this->model = $model;
        parent::__construct($context);
    }


    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('submenu_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You Deleted the Sub Menu'));
                return $resultRedirect->setPath('*/index/edit', ['menu_id' => $model['menu_id']]);
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['submenu_id' => $id]);
            }
        }
        $this->messageManager->addError(__(' does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
