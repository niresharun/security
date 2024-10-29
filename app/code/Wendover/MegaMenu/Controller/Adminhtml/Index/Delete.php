<?php

namespace Wendover\MegaMenu\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action\Context;
use Wendover\MegaMenu\Model\Menu;

class Delete extends Action
{
    Protected $model;

    public function __construct(
        Context $context,
        Menu $model
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
        $id = $this->getRequest()->getParam('menu_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You Deleted the Menu'));
                return $resultRedirect->setPath('*/*/index');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['menu_id' => $id]);
            }
        }
        $this->messageManager->addError(__(' does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
