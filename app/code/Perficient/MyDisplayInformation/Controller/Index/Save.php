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
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Perficient\MyDisplayInformation\Controller\AbstractAction;
use Perficient\MyDisplayInformation\Model\MyDisplayInformationFactory;
use Perficient\MyDisplayInformation\Model\ResourceModel\MyDisplayInformation;

/**
 * Class Save
 * @package Perficient\MyDisplayInformation\Controller\Index
 */
class Save extends AbstractAction
{

    /**
     * Save constructor.
     */
    public function __construct(Context $context,
        protected PageFactory $resultPageFactory,
        protected Session $customerSession,
        protected UrlInterface $url,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        protected MyDisplayInformation $myDisplayInformation,
        protected MyDisplayInformationFactory $myDisplayInformationFactory,
        protected ManagerInterface $messageManager
    ) {
        parent::__construct($resultPageFactory, $customerSession, $url);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->request->getParams();
        $params['state'] = $params['text_state'];
        $params['user_id'] = $this->customerSession->getCustomerData()->getId();
        $objModel = $this->myDisplayInformationFactory->create();
        if (empty($params['mydisplayinformation_id'])) {
            unset($params['mydisplayinformation_id']);
        }
        $objModel->setData($params);
        try {
            $saveData =  $this->myDisplayInformation->save($objModel);
            if ($saveData) {
                $this->messageManager->addSuccessMessage(__('My Display Information Saved Successfully'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e);
        }
        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setUrl($this->url->getUrl('mydisplayinformation/'));
    }
}
