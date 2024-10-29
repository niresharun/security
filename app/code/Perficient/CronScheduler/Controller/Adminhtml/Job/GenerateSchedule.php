<?php
/**
 * Generate all the cron jobs
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Job;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Event\Observer;
use Perficient\CronScheduler\Helper\Url;
use Magento\Framework\App\RequestInterface;
use Magento\Cron\Observer\ProcessCronQueueObserver;

/**
 * Class GenerateSchedule
 * @package Perficient\CronScheduler\Controller\Adminhtml\Job
 */
class GenerateSchedule extends Action
{
    /**
     * @var string
     */
    protected $aclResource = "generate_schedule";

    /**
     * Class constructor
     * @param Context $context
     * @param ProcessCronQueueObserver $cron
     * @param Observer $observer
     * @param ForwardFactory $resultForwardFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        public ProcessCronQueueObserver $cron,
        public Observer $observer,
        public ForwardFactory $resultForwardFactory,
        protected RequestInterface $request
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action
     */
    public function execute()
    {

        $this->cron->execute($this->observer);
        $params = $this->request->getParams();
        if (isset($params['redirect'])) {
            return $this->resultRedirectFactory->create()->setPath(str_replace("_", "/", (string) $params['redirect']), []);
        }
        return $this->resultRedirectFactory->create()->setPath(Url::JOB_CONFIG, []);
    }

    /**
     * Is the action allowed?
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Perficient_CronScheduler::'.$this->aclResource);
    }
}
