<?php
/**
 * Basic class to have complete operations on the task.
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Abstract controller for tasks
 * @version 1.0.0
 */
abstract class Task extends Action
{

    /**
     * @var string
     */
    protected $aclResource = "";

    /**
     * Task constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        protected PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Is the action allowed?
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Perficient_CronScheduler::'.$this->aclResource);
    }

    /**
     * Execute the action
     */
    abstract public function execute();
}
