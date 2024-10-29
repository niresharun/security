<?php
/**
 * This module is used to create custom artwork catalogs,
 * This file contains the logic to add new catalog
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Controller\Index;

use Perficient\MyCatalog\Controller\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogInterfaceFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Add
 * @package Perficient\MyCatalog\Controller\Index
 */
class Add extends AbstractAction
{
    /**
     * Add constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param RedirectFactory $redirectFactory
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     * @param MyCatalogInterfaceFactory $myCatalogFactory
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly RedirectFactory $redirectFactory,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
        private readonly ManagerInterface $messageManager,
        private readonly MyCatalogInterfaceFactory $myCatalogFactory,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository
    ) {
        parent::__construct(
            $resultPageFactory,
            $customerSession,
            $url
        );
    }

    /**
     * Execute action based on request and return result
     *
     * @throws \Exception
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        // First validate the customer.
        parent::validateCustomer();

        $wishlistId = (int)$this->request->getParam('wishlist_id');
        if (!$wishlistId) {
            $this->messageManager->addErrorMessage(__('Invalid catalog details'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->redirectFactory->create();
            return $resultRedirect->setUrl($this->url->getUrl('mycatalog/'));
        }

        $params = $this->request->getParams();
        try {
            // Check, if continue button is pressed.
            if (isset($params['continue'])) {
                // Model to save the data in database.
                $model = $this->myCatalogFactory->create();

                // Set the customer and created-at date.
                $params['customer_id'] = $this->customerSession->getCustomerId();
                $model->setData($params);
                if ($this->myCatalogRepository->save($model)) {
                    $params = ['catalog_id' => $model->getId()];
                    $resultRedirect = $this->redirectFactory->create();
                    $resultRedirect->setPath('mycatalog/index/pages', $params);
                    return $resultRedirect;
                } else {
                    $this->messageManager->addErrorMessage(__('Unable to save catalog details.'));
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this->resultPageFactory->create();
    }
}
