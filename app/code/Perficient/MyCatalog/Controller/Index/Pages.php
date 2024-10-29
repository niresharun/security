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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Perficient\MyCatalog\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

/**
 * Class Pages
 * @package Perficient\MyCatalog\Controller\Index
 */
class Pages extends AbstractAction
{
    /**
     * Index constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param RedirectFactory $redirectFactory
     * @param RequestInterface $request
     * @param Data $helper
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly RedirectFactory $redirectFactory,
        private readonly RequestInterface $request,
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

        $catalogId = $this->request->getParam('catalog_id');
        $downloadPdf = $this->request->getParam('download_pdf');
        if(isset($downloadPdf) && $downloadPdf == true){
            $resultRedirect = $this->redirectFactory->create();
            $downloadPdfUrl = 'mycatalog/index/pdf/catalog_id/'.$catalogId;
            $resultRedirect->setPath($downloadPdfUrl);
            return $resultRedirect;
        }



        if (!$this->helper->isCatalogOwner($catalogId)) {
            $resultRedirect = $this->redirectFactory->create();
            $resultRedirect->setPath('mycatalog/');
            return $resultRedirect;
        }

        return $this->resultPageFactory->create();
    }
}
