<?php
/**
 * This module is used to bulk image upload
 *
 * @category:  Magento
 * @package:   Perficient/Catalog
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * @license:   Magento Enterprise Edition (MEE) license
 * @author:    Vijayashanthi M
 * @project:   Wendover
 * @keywords:  Module Perficient_Catalog
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Perficient\Catalog\Controller\Adminhtml\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class Result
 *
 * @package Perficient\Catalog\Controller\Adminhtml\Index
 */
class Imgdisassociate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private $context;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     */
    public function __construct(
        \Magento\Backend\App\Action\Context                       $context,
        protected \Magento\Store\Model\StoreManagerInterface      $storeManager,
        protected \Magento\Framework\File\Csv                     $csvProcessor,
        protected \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Message\ManagerInterface               $messageManager,
        protected RequestInterface                                $request
    )
    {
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
            $resultRedirect->setPath('productbulkupload/index/disassociate/');
            return $resultRedirect;
        }

        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }

        $csvData = $this->csvProcessor->getData($file['tmp_name']);
        $successSku = $failureSku = [];
        foreach ($csvData as $row => $data) {
            if ($row > 0) {
                $sku = '';
                $action = '';
                if (!empty($data[0])) {
                    $sku = trim((string)$data[0]);
                }

                if (!empty($data[1])) {
                    $action = trim((string)$data[1]);
                }

                if (strtolower($action) == "drop") {
                    $product = $this->loadMyProduct($sku);
                    if ($product) {
                        try {
                            $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
                            foreach ($existingMediaGalleryEntries as $key => $entry) {
                                $imageLable = $existingMediaGalleryEntries[$key]->getLabel();
                                if ($imageLable) {
                                    $imageLable = strtolower((string)$imageLable);
                                }
                                $temparr = $existingMediaGalleryEntries[$key]->getTypes();
                                //$getImgType="cropper_image";
                                /*if (in_array($getImgType, $temparr) && (count($temparr) != 1)) {
                                    $arr = array_diff($temparr, array($getImgType));
                                    $existingMediaGalleryEntries[$key]->setTypes($arr);
                                }
                                else*/
                                //if (in_array($getImgType, $temparr) && (count($temparr) == 1)) {
                                if ($imageLable == "cropped" || in_array('cropped', $temparr)) {
                                    //$existingMediaGalleryEntries[$key]->setData([]);
                                    //$existingMediaGalleryEntries[$key]->setFile([]);
                                    unset($existingMediaGalleryEntries[$key]);
                                    $successSku[] = $data[0];
                                }

                            }
                            $product->setMediaGalleryEntries($existingMediaGalleryEntries);
                            $this->productRepository->save($product);
                        } catch (\Exception $e) {
                            throw $e->getMessage();
                        }
                    } else {
                        $failureSku[] = $data[0];
                    }
                }
            }
        }
        if (count($failureSku))
            $this->messageManager->addErrorMessage('Failure Count: ' . count($failureSku));
        if (count($successSku))
            $this->messageManager->addSuccessMessage('Disassociation Done Successfully');
        $this->messageManager->addSuccessMessage('Success Count: ' . count($successSku));
        $resultRedirect->setPath('productbulkupload/index/disassociate/');
        return $resultRedirect;
    }

    public function loadMyProduct($sku)
    {
        return $this->productRepository->get($sku, true, 0);
    }
}
