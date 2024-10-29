<?php
/**
 * Delete entry from cron_schedule table
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Task;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Perficient\CronScheduler\Helper\Url;
use Perficient\CronScheduler\Model\ResourceModel\Task\CollectionFactory;

/**
 * Class MassDelete
 * @package Perficient\CronScheduler\Controller\Adminhtml\Task
 */
class MassDelete extends Action
{
    /**
     * @var string
     */
    protected $aclResource = "task_massdelete";

    /**
     * @var string
     */
    protected $redirectUrl = Url::TASK_LISTING;

    /**
     * Class constructor
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        protected Filter $filter,
        protected CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Execute the action
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Execute the mass delete action
     * @param $collection
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function massAction($collection)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->redirectUrl);

        if (!$this->_authorization->isAllowed('Perficient_CronScheduler::' . $this->aclResource)) {
            $this->messageManager->addErrorMessage(__("You are not allowed to delete tasks"));
        } else {
            $counter = 0;
            foreach ($collection as $item) {
                $item->delete();
                $counter ++;
            }
            if ($counter >= 2) {
                $this->messageManager->addSuccessMessage(__("%1 tasks have been deleted", $counter));
            } else {
                $this->messageManager->addSuccessMessage(__("%1 task has been deleted", $counter));
            }
        }

        return $resultRedirect;
    }

    /**
     * Is the action allowed?
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
}
