<?php
/**
 * This module is used to create custom artwork catalogs
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
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Api\PageRepositoryInterface;

/**
 * Class LoadPage
 * @package Perficient\MyCatalog\Controller\Index
 */
class LoadPage extends AbstractAction
{
    /**
     * LoadPage constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param JsonFactory $jsonFactory
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param PageRepositoryInterface $pageRepository
     * @param Data $helper
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly JsonFactory $jsonFactory,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly Data $helper

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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        // First validate the customer.
        parent::validateCustomer();

        $params = $this->request->getParams();

        $response  = [];
        $catalogId = $params['catalog_id'];

        if ($this->helper->isCatalogOwner($catalogId)) {
            try {
                $pageId = $this->pageRepository->getCatalogPageID($catalogId, $params['pages']['page_position']);
                $page = $this->pageRepository->getById($pageId);
                $response['page_template_id'] = $page->getPageTemplateId();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $response = ['myCatalogAuthError'];
        }

        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData($response);
    }
}
