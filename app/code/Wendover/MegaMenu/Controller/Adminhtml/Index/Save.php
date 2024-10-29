<?php

declare(strict_types=1);

namespace Wendover\MegaMenu\Controller\Adminhtml\Index;

use Wendover\MegaMenu\Model\MenuFactory;
use Wendover\MegaMenu\Model\ResourceModel\Menu;
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
        protected Menu $resource,
        protected MenuFactory $menuFactory,
        protected SerializerInterface $serializer
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getPost('main_menu');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->menuFactory->create();
            if (empty($data['menu_id'])) {
                $data['menu_id'] = null;
            }

            $model->setData($data);

            try {
                $this->resource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved it.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getMenuId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
