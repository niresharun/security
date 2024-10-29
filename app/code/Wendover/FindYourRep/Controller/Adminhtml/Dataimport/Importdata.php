<?php

namespace Wendover\FindYourRep\Controller\Adminhtml\Dataimport;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Wendover\FindYourRep\Model\Rep;

class Importdata extends \Magento\Backend\App\Action
{
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Rep $repModel
     */
    public function __construct(
        Context                   $context,
        private readonly Registry $coreRegistry,
        private readonly Rep      $repModel
    )
    {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->coreRegistry->register('row_data', $this->repModel);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Import Rep Data'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wendover_FindYourRep::add_datalocation');
    }
}
