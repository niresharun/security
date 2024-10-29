<?php

namespace Wendover\FindYourRep\Controller\Adminhtml\Rep;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\ResponseInterface;
use Wendover\FindYourRep\Model\RepFactory;
use Wendover\FindYourRep\Model\ResourceModel\Rep\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @param Context $context
     * @param Filter $filter
     * @param RedirectFactory $resultRedirectFactory
     * @param RepFactory $repFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context                            $context,
        private readonly Filter            $filter,
        RedirectFactory                    $resultRedirectFactory,
        private readonly RepFactory        $repFactory,
        private readonly CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!empty($this->getRequest()->getParam('queue'))) {
            $collection = $this->getRequest()->getParam('queue');
            $collectionSize = is_countable($collection) ? count($collection) : 0;
        } else {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
        }
        foreach ($collection as $item) {
            if (!empty($this->getRequest()->getParam('queue'))) {
                $id = $item;
            } else {
                $id = $item->getId();
            }
            $item = $this->repFactory->create()->load($id);
            $item->delete();
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 element(s) have been deleted.', $collectionSize));
        return $resultRedirect->setPath('*/*/');
    }
}
