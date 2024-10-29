<?php
/**
 * This module is used to create custom artwork catalogs.
 * This file contains the logic to delete the catalog.
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
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Perficient\MyCatalog\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\Message\ManagerInterface;
use Perficient\MyCatalog\Api\MyCatalogDeleteRepositoryInterface;
use Perficient\MyCatalog\Api\Data\MyCatalogDeleteInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class DeleteCatalog
 * @package Perficient\MyCatalog\Controller\Index
 */
class DeleteCatalog extends AbstractAction
{
    /**
     * DeleteCatalog constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param JsonFactory $jsonResultFactory
     * @param RedirectFactory $redirectFactory
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param Data $helper
     * @param MyCatalogDeleteRepositoryInterface $myCatalogDeleteRepository
     * @param MyCatalogDeleteInterfaceFactory $myCatalogDeleteFactory
     * @param DateTime $timezone
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly JsonFactory $jsonResultFactory,
        private readonly RedirectFactory $redirectFactory,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly RequestInterface $request,
        private readonly ManagerInterface $messageManager,
        private readonly Data $helper,
        private readonly MyCatalogDeleteRepositoryInterface $myCatalogDeleteRepository,
        private readonly MyCatalogDeleteInterfaceFactory $myCatalogDeleteFactory,
        private readonly DateTime $timezone
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

        $catalogId = (int)$this->request->getParam('catalog_id');
        $response  = ['success' => false];

        try {
            if ($this->helper->isCatalogOwner($catalogId)) {
                $catalog = $this->myCatalogRepository->getById($catalogId);
                if ($catalog->getCatalogId()) {
                    // Model to save the data in database.
                    $deleteModel = $this->myCatalogDeleteFactory->create();
                    $deleteModel->setCatalogId($catalog->getCatalogId());
                    $deleteModel->setWishlistId($catalog->getWishlistId());
                    $deleteModel->setUpdatedAt($this->timezone->gmtDate());
                    $deleteModel->setAction('Deleted');
                    $this->myCatalogDeleteRepository->save($deleteModel);
                    $isDeleted = $this->myCatalogRepository->deleteById($catalog->getCatalogId());
                    $response['success'] = $isDeleted;
                }
                //$this->messageManager->addSuccessMessage(__('Catalog deleted successfully.'));
            }
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($response);
        return $result;
    }
}
