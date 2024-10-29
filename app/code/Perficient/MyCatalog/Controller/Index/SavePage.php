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
use Perficient\MyCatalog\Api\PageRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;

use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Api\Data\PageInterfaceFactory;
use Magento\Framework\Stdlib\DateTime;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;


/**
 * Class SavePage
 * @package Perficient\MyCatalog\Controller\Index
 */
class SavePage extends AbstractAction
{
    private array $nonSavedPages = [
        'front',
        'back'
    ];

    /**
     * SavePage constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param RedirectFactory $redirectFactory
     * @param RequestInterface $request
     * @param JsonFactory $jsonResultFactory
     * @param Json $json
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     * @param PageInterfaceFactory $pageModelFactory
     * @param PageRepositoryInterface $pageRepository
     * @param Data $helper
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param DateTime\DateTime $date
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly RedirectFactory $redirectFactory,
        private readonly RequestInterface $request,
        private readonly JsonFactory $jsonResultFactory,
        private readonly Json $json,
        private readonly ManagerInterface $messageManager,
        private readonly LoggerInterface $logger,
        private readonly PageInterfaceFactory $pageModelFactory,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly Data $helper,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly DateTime\DateTime $date
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

        $params = $this->request->getParams();
        $data = [];
        $catalogId = $params['catalog_id'];

        if ($this->helper->isCatalogOwner($catalogId)) {
            if (!in_array($params['pages']['page_position'], $this->nonSavedPages)) {
                try {
                    $dropSpotConfig = '{}';
                    if (isset($params['pages']['dropspot_config']) && !empty($params['pages']['dropspot_config'])) {
                        $dropSpotConfig = $this->json->serialize($params['pages']['dropspot_config']);
                    }

                    $pageId = $this->pageRepository->getCatalogPageID($catalogId, $params['pages']['page_position']);

                    if ($pageId) {
                        $pageFactory = $this->pageRepository->getById($pageId);
                    } else {
                        $pageFactory = $this->pageModelFactory->create();
                    }
                    // Set page data.
                    $pageFactory->setCatalogId($catalogId);
                    $pageFactory->setPageTemplateId($params['pages']['page_template_id']);
                    $pageFactory->setDropSpotConfig($dropSpotConfig);
                    $pageFactory->setPagePosition($params['pages']['page_position']);

                    $pageFactory = $this->pageRepository->save($pageFactory);
                    $data = $pageFactory->getData('page_position');
                    //update updated_at so data will sync with CRM
                    $catalog = $this->myCatalogRepository->getById($catalogId);
                    $catalog->setUpdatedAt($this->date->gmtDate())->save();
                    $this->messageManager->addSuccessMessage(__('Your changes have been save successfully.'));
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        } else {
            $data = 'myCatalogAuthError';
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }
}
