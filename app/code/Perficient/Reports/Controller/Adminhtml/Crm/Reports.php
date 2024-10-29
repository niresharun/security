<?php

/**
 * Redirect crm reports link dashboard
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

namespace Perficient\Reports\Controller\Adminhtml\Crm;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
/**
 * Class Reports
 * @package Perficient\Reports\Controller\Adminhtml\Crm
 */
class Reports extends \Magento\Backend\App\Action
{
    /**
     * CRM Reports url path
     */
    const XML_CONFIG_PATH = "perficient_crm/settings/reports_url";

    /**
     * Reports constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        protected ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
    }

    public function execute(): \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->getReportsUrl());
        return $resultRedirect;
    }

    /**
     * @return string
     */
    public function getReportsUrl()
    {
        $configValue = $this->scopeConfig->getValue(self::XML_CONFIG_PATH,ScopeInterface::SCOPE_STORE);
        return $configValue;
    }
    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Perficient_Reports::perficient_crm_application_report');
    }
}
