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
use Perficient\MyCatalog\Api\Data\MyCatalogInterfaceFactory;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Perficient\MyCatalog\Helper\Data;
use Psr\Log\LoggerInterface;
use Perficient\MyCatalog\Api\Data\PageInterfaceFactory;
use Perficient\MyCatalog\Api\PageRepositoryInterface;
use Perficient\MyCatalog\Model\ResourceModel\MyCatalog;
use Perficient\MyCatalog\Model\TemplateRepository;

/**
 * Class Edit
 * @package Perficient\MyCatalog\Controller\Index
 */
class Edit extends AbstractAction
{
    /**
     * Edit constructor.
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param RedirectFactory $redirectFactory
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param ManagerInterface $messageManager
     * @param MyCatalogInterfaceFactory $myCatalogFactory
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param Data $helper
     * @param PageInterfaceFactory $pageModelFactory
     * @param PageRepositoryInterface $pageRepository
     * @param MyCatalog $resourceMyCatalog
     * @param TemplateRepository $templateRepository
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
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly Data $helper,
        private readonly PageInterfaceFactory $pageModelFactory,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly MyCatalog $resourceMyCatalog,
        private readonly TemplateRepository $templateRepository
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
        $dropSpotConfigContentArray = [];
        // First validate the customer.
        parent::validateCustomer();

        $params = $this->request->getParams();
        $wishlistId = (int)$this->request->getParam('wishlist_id', 0);
        $catalogId  = (int)$this->request->getParam('catalog_id', 0);

        if (!$wishlistId && !$catalogId) {
            $this->messageManager->addErrorMessage(__('Invalid catalog details'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->redirectFactory->create();
            return $resultRedirect->setUrl($this->url->getUrl('mycatalog/'));
        }

        // Validate catalog
        if ($catalogId && !$this->helper->isCatalogOwner($catalogId)) {
            $this->messageManager->addErrorMessage(__('Invalid catalog details'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->redirectFactory->create();
            return $resultRedirect->setUrl($this->url->getUrl('mycatalog/'));
        }

        try {
            // Check, if continue button is pressed.
            if (isset($params['continue'])) {
                // Model to save the data in database.
                if (!empty($params['catalog_id'])) {
                    try {
                        $model = $this->myCatalogRepository->getById($params['catalog_id']);
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(__('This catalog no longer exists.'));
                        $resultRedirect = $this->redirectFactory->create();
                        $resultRedirect->setPath('mycatalog');
                        return $resultRedirect;
                    }
                } else {
                    $model = $this->myCatalogFactory->create();
                }

                // Set the customer and created-at date.
                $params['customer_id'] = $this->customerSession->getCustomerId();
                //$model->setData($params);
                $model->setWishlistId($params['wishlist_id']);
                $model->setCustomerId($params['customer_id']);
                $model->setLogoImage($params['logo_image'] ?? null);
                $model->setCatalogTitle($params['catalog_title'] ?? null);
                $model->setAdditionalInfo1($params['additional_info_1'] ?? null);
                $model->setAdditionalInfo2($params['additional_info_2'] ?? null);
                $model->setName($params['name'] ?? null);
                $model->setPhoneNumber($params['phone_number'] ?? null);
                $model->setWebsiteUrl($params['website_url'] ?? null);
                $model->setCompanyName($params['company_name'] ?? null);

                try {
                    $model = $this->myCatalogRepository->save($model);
                    $oldParams = $this->request->getParams();
                    $params = ['catalog_id' => $model->getId()];
                    if(isset($oldParams['get_pdf']) && $oldParams['get_pdf'] == 1){
                        $params['download'] = 1;
                    }
                    if(isset($oldParams['page_template_id']) && !empty($oldParams['page_template_id'])){
                       $galleryImagesKeys =  array_keys($this->resourceMyCatalog->getGalleryImages($model->getId()));
                       $getSelectedTemplateSpotCount = $this->templateRepository->getById($oldParams['page_template_id'])
                                                       ->getTemplateDropSpotsCount();
                        $calculateRequiredPagesCount =  count($galleryImagesKeys)/$getSelectedTemplateSpotCount;
                        $getRequiredPagesCount = ceil($calculateRequiredPagesCount);
                       if($getRequiredPagesCount == 1){

                          array_unshift($galleryImagesKeys,"");
                            unset($galleryImagesKeys[0]);
                            foreach($galleryImagesKeys as $key => $value){
                                $dropSpotConfigContentArray [] = '"dropspot_'.$key.'":{"item_id":"'.$value.'"}';
                            }
                            $dropSpotConfigContentString = '{'.implode(',',$dropSpotConfigContentArray).'}';
                           $pageFactory = $this->pageModelFactory->create();
                           $pageFactory->setCatalogId($model->getId());
                           $pageFactory->setPageTemplateId($oldParams['page_template_id']);
                           $pageFactory->setDropSpotConfig($dropSpotConfigContentString);
                           $pageFactory->setPagePosition(true);
                           $pageFactory = $this->pageRepository->save($pageFactory);

                       }else{
                           $pageWiseImagesArray =  array_chunk($galleryImagesKeys, (int) $getSelectedTemplateSpotCount);
                           array_unshift($pageWiseImagesArray,"");
                           unset($pageWiseImagesArray[0]);
                           for($i = 1; $i<=$getRequiredPagesCount; $i++){

                              $dropSpotConfigArray = $pageWiseImagesArray[$i];
                               array_unshift($dropSpotConfigArray,"");
                               unset($dropSpotConfigArray[0]);
                               $dropSpotConfigContentArray = [];
                               foreach($dropSpotConfigArray as $key => $value){
                                   $dropSpotConfigContentArray [] = '"dropspot_'.$key.'":{"item_id":"'.$value.'"}';
                               }
                               $dropSpotConfigContentString = '{'.implode(',',$dropSpotConfigContentArray).'}';
                               $pageFactory = $this->pageModelFactory->create();
                               $pageFactory->setCatalogId($model->getId());
                               $pageFactory->setPageTemplateId($oldParams['page_template_id']);
                               $pageFactory->setDropSpotConfig($dropSpotConfigContentString);
                               $pageFactory->setPagePosition($i);
                               $pageFactory = $this->pageRepository->save($pageFactory);
                               unset($dropSpotConfigContentArray);
                           }

                       }
  }
                    $resultRedirect = $this->redirectFactory->create();
                    if(isset($oldParams['get_pdf']) && $oldParams['get_pdf'] == 1){
                        $resultRedirect->setPath('mycatalog/index/pdf', $params);
                    }else{
                        $resultRedirect->setPath('mycatalog/index/pages', $params);
                    }


                    return $resultRedirect;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this->resultPageFactory->create();
    }

}
