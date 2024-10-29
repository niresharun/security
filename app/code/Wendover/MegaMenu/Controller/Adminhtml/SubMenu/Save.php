<?php

declare(strict_types=1);

namespace Wendover\MegaMenu\Controller\Adminhtml\SubMenu;

use Wendover\MegaMenu\Model\SubMenuFactory;
use Wendover\MegaMenu\Model\ResourceModel\SubMenu;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;


class Save extends Action implements HttpPostActionInterface
{

    public function __construct(
        Context $context,
        protected SubMenu $resource,
        protected SubMenuFactory $subMenuFactory,
        protected SerializerInterface $serializer
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getPost('submenu_configuration');
        $dataChild = $this->getRequest()->getPost('childmenu_configuration');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->subMenuFactory->create();
            if (empty($data['menu_id'])) {
                $data['menu_id'] = null;
            }

             if(!empty($dataChild['child_menu'])){
                 $data['child_menu'] = $this->serializer->serialize($dataChild['child_menu']['child_menu']);
             } else {
                 $data['child_menu'] = 'null';
             }

             $model->setData($data);

            try {
                $this->resource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved it.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['submenu_id' => $model->getSubmenuId(), 'menu_id' => $model->getMenuId()]);
                }
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong.'));
            }
        }

        return $resultRedirect->setPath('*/index/edit', ['menu_id' => $model['menu_id']]);
    }
}
