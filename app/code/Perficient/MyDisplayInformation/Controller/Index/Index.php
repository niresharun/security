<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Perficient\MyDisplayInformation\Controller\AbstractAction;
use Perficient\MyDisplayInformation\Helper\Data;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class Index
 * @package Perficient\MyDisplayInformation\Controller\Index
 */
class Index extends AbstractAction
{
    const CUSTOMER_CUSTOMER = "Customer's Customer";

    /**
     * Index constructor.
     */
    public function __construct(
        protected PageFactory $resultPageFactory,
        protected Session $customerSession,
        protected UrlInterface $url,
        private readonly Data $myDisplayInformationHelper,
        private readonly Escaper $escaper,
        private readonly StoreManagerInterface $storemanager,
        private readonly Redirect $resultRedirect
    ) {
        parent::__construct($resultPageFactory, $customerSession, $url);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->validateCustomer();
        $currentUserRole = $this->myDisplayInformationHelper->getCurrentUserRole();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if(isset($currentUserRole[0])){
            $currentUserRoleText = html_entity_decode((string) $currentUserRole[0], ENT_QUOTES);
        }
        if (isset($currentUserRoleText) && $currentUserRoleText == self::CUSTOMER_CUSTOMER){
            $redirectUrl= $this->storemanager->getStore()->getBaseUrl();
            return $this->resultRedirect->setPath($redirectUrl);
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Custom Front View'));
        return $resultPage;
    }
}
