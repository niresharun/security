<?php
/**
 * This module is used to create custom artwork catalogs.
 * This file contains the logic to delete the page.
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
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Perficient\MyCatalog\Api\Data\PageInterfaceFactory;
use Perficient\MyCatalog\Api\PageRepositoryInterface;
use Perficient\MyCatalog\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTime;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;

/**
 * Class DeletePage
 * @package Perficient\MyCatalog\Controller\Index
 */
class DeletePage extends AbstractAction
{
    /**
     * DeletePage constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param JsonFactory $jsonResultFactory
     * @param RedirectFactory $redirectFactory
     * @param PageInterfaceFactory $pageModelFactory
     * @param PageRepositoryInterface $pageRepository
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param Data $helper
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param DateTime $_date
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly JsonFactory $jsonResultFactory,
        private readonly RedirectFactory $redirectFactory,
        private readonly PageInterfaceFactory $pageModelFactory,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly LoggerInterface $logger,
        private readonly RequestInterface $request,
        private readonly ManagerInterface $messageManager,
        private readonly Data $helper,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly DateTime $_date
    ) {
        parent::__construct(
            $resultPageFactory,
            $customerSession,
            $url
        );
    }

    /**
     * Execute action based on request and return result
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        // First validate the customer.
        parent::validateCustomer();

        $catalogId  = (int)$this->request->getParam('catalog_id');
        $pageNumber = (int)$this->request->getParam('page_num');
        $response  = ['success' => false];

        try {
            if ($this->helper->isCatalogOwner($catalogId)) {
                $pageId = $this->pageRepository->getCatalogPageID($catalogId, $pageNumber);

                if ($pageId) {
                    $this->pageRepository->deleteById($pageId);

                    /* Recalculate other pages */
                    $pageFactory = $this->pageModelFactory->create();
                    $pagesData = $pageFactory->getCollection()
                        ->addFieldTofilter('catalog_id', $catalogId)
                        ->addFieldTofilter('page_position', ['gt' => $pageNumber]);
                    if ($pagesData) {
                        foreach ($pagesData as $item) {
                            $position = $item->getPagePosition();
                            $item->setPagePosition($position - 1);
                            $item->save();
                        }
                    }
                    //update updated_at so data will sync with CRM
                    $catalog = $this->myCatalogRepository->getById($catalogId);
                    $catalog->setUpdatedAt($this->_date->gmtDate())->save();

                    $response['success'] = true;
                    $this->messageManager->addSuccessMessage(__('Catalog page deleted successfully.'));
                } else {
                    $response['error'] = __('Error, page doesn\'t exists.');
                }
            } else {
                $response['error'] = 'myCatalogAuthError';
            }
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($response);
        return $result;
    }
}
