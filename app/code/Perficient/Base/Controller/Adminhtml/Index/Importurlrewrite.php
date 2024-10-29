<?php
/**
 * This module is used to bulk image upload
 *
 * @category:  Magento
 * @package:   Perficient/Base
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * @license:   Magento Enterprise Edition (MEE) license
 * @author:    Vijayashanthi M
 * @project:   Wendover
 * @keywords:  Module Perficient_Base
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Perficient\Base\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\File\Csv;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

/**
 * Class Result
 *
 * @package Perficient\Base\Controller\Adminhtml\Index
 */
class Importurlrewrite extends \Magento\Backend\App\Action
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Csv $csvProcessor
     * @param ManagerInterface $messageManager
     * @param UrlRewriteFactory $urlRewriteModelFactory
     * @param Http $request
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        protected StoreManagerInterface $storeManager,
        protected Csv $csvProcessor,
        protected UrlRewriteFactory $urlRewriteModelFactory,
        protected Http $request
    ) {
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }


    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $file = $this->request->getFiles('csv');

         if (empty($file)) {
            $resultRedirect->setPath('customutlrewrite/index/index/');
            return $resultRedirect;
        }
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }

        $csvData = $this->csvProcessor->getData($file['tmp_name']);
        $errorMsg = [];
        foreach ($csvData as $row => $data) {
            if ($row > 0) {
                try{

                    $UrlRewriteCollection=$this->urlRewriteModelFactory->create()->getCollection()->addFieldToFilter('request_path', trim((string) $data[0]," "));
                    $deleteItem = $UrlRewriteCollection->getFirstItem();
                    if ($UrlRewriteCollection->getFirstItem()->getId()) {
                        // target path does exist
                        $deleteItem->delete();
                    }
                    $urlRewriteModel = $this->urlRewriteModelFactory->create();
                    /* set current store id */
                    $urlRewriteModel->setStoreId(1);
                    /* this url is not created by system so set as 0 */
                    $urlRewriteModel->setIsSystem(0);
                    /* unique identifier - set random unique value to id path */
                    $urlRewriteModel->setRedirectType(301);
                    /* set actual url path to target path field */
                    $urlRewriteModel->setTargetPath(trim((string) $data[1]," "));
                    /* set requested path which you want to create */
                    $urlRewriteModel->setRequestPath(trim((string) $data[0]," "));
                    /* set current store id */
                    $urlRewriteModel->save();
                }catch (\Excepttion $e){
                    $errorMsg[]=$e->getMessage();
                }
            }
        }
        if($errorMsg){
            $this->messageManager->addErrorMessage('Error Occured'.implode(',', $errorMsg));
        }
        $this->messageManager->addSuccessMessage('Import Done Successfully');
        $resultRedirect->setPath('customutlrewrite/index/index/');
        return $resultRedirect;
    }
}
