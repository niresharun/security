<?php
/**
 * Module to customize customer related features
 *
 * @category: PHP
 * @package: Perficient/Customer
 * @copyright:
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suraj Jaiswal <suraj.jaiswal@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Customer
 */
declare(strict_types=1);

namespace Perficient\Customer\Controller\Account;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Index
 * @package Perficient\Customer\Controller\Account
 */
class RequiredDetails implements ActionInterface
{
    /**
     * Required details constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Session $customerSession
     * @param RedirectInterface $redirectInterface
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly PageFactory       $resultPageFactory,
        private readonly RedirectFactory   $resultRedirectFactory,
        private readonly Session           $customerSession,
        private readonly HttpRequest       $httpRequest,
        private readonly RedirectInterface $redirectInterface,
        protected RequestInterface         $request
    )
    {

    }

    public function execute(): \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
    {
        $refererUrl = $this->redirectInterface->getRefererUrl();
        $params = $this->request->getParams();

        if (strpos((string)$refererUrl, '/customer/account/login/') === false
            && isset($params['cid']) && isset($params['flpc'])) {
            return $this->resultRedirectFactory->create()
                ->setPath('customer/account/login');
        }

        if ($this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/');
            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Required Details'));

        return $resultPage;
    }
}
