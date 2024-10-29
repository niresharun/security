<?php
/**
 * This module is used to create custom artwork catalogs.
 * This file contains the code to create PDF.
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
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Block\Pdf;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

/**
 * Class Pdftest
 * @package Perficient\MyCatalog\Controller\Index
 */
class Pdftest extends AbstractAction
{
    /**
     * Index constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param RedirectFactory $redirectFactory
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param RequestInterface $request
     * @param Data $helper
     * @param Pdf $pdfBlock
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly RedirectFactory $redirectFactory,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly RequestInterface $request,
        private readonly Data $helper,
        private readonly Pdf $pdfBlock

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
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \PdfcrowdException
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        // First validate the customer.
        parent::validateCustomer();

        $catalogId = $this->request->getParam('catalog_id');
        $download  = $this->request->getParam('download', 0);
        $pharTest  = $this->request->getParam('phar', 0);

        if (!$this->helper->isCatalogOwner($catalogId) && !$this->helper->isSharedCatalog($catalogId)) {
            $resultRedirect = $this->redirectFactory->create();
            $resultRedirect->setPath('mycatalog/');
            return $resultRedirect;
        }

        $this->pdfBlock->setData('catalogId', $catalogId);

        $html = $this->pdfBlock->toHtml();
        $myCatalog = $this->myCatalogRepository->getById($catalogId);
        $this->helper->createPdf($html, $myCatalog->getCatalogTitle(), $download, false, $pharTest);
    }
}
