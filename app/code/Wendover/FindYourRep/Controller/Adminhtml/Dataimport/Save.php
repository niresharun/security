<?php

namespace Wendover\FindYourRep\Controller\Adminhtml\Dataimport;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Wendover\FindYourRep\Model\ResourceModel\Rep\CollectionFactory as RepCollection;
use Wendover\FindYourRep\Model\RepFactory;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\File\Csv;

class Save extends Action
{
    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param RepFactory $repFactory
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(
        Context                               $context,
        protected RequestInterface            $request,
        protected Filesystem                  $fileSystem,
        protected UploaderFactory             $uploaderFactory,
        ManagerInterface                      $messageManager,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly RepFactory           $repFactory,
        private readonly RepCollection        $repCollection,
        RedirectFactory                       $resultRedirectFactory,
        protected AdapterFactory              $adapterFactory,
        private DriverInterface               $driverFile,
        protected File                         $csvProcessor
    )
    {
        parent::__construct($context);
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        try {
            if ($_FILES['importdata']['size'] > $maxFileSize) {
                $this->messageManager->addErrorMessage(__("Please upload a .csv / .xls file less than 2MB."));
                return $resultRedirect->setPath('representative/dataimport/importdata');
            }
            if ((isset($_FILES['importdata']['name'])) && ($_FILES['importdata']['name'] != '')) {
                try {
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'importdata']);
                    $uploaderFactory->setAllowedExtensions(['csv', 'xls']);
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('Rep_ImportData');
                    $result = $uploaderFactory->save($destinationPath);

                    if (!$result) {
                        $this->messageManager->addErrorMessage(__('File cannot be saved to path: $1', $destinationPath));
                        return $resultRedirect->setPath('representative/dataimport/importdata');
                    } else {
                        $imagePath = 'Rep_ImportData' . $result['file'];
                        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                        $filePath = $mediaDirectory->getAbsolutePath($imagePath);
                        $f_object = $this->driverFile->fileOpen($filePath, 'r');
                        $column = $this->driverFile->fileGetCsv($f_object, 0, ",");

                        if ($f_object) {
                            if (($column[0] == 'firstname') &&
                                ($column[1] == 'lastname') &&
                                ($column[2] == 'email') &&
                                ($column[3] == 'phone1') &&
                                ($column[4] == 'phone2') &&
                                ($column[5] == 'notes') &&
                                ($column[6] == 'postal_code') &&
                                ($column[7] == 'type')) {
                                $count = 0;
                                $update = 0;
                                while (($columns = $this->driverFile->fileGetCsv($f_object, 0, ",")) !== FALSE) {
                                    if (!empty($columns[0]) &&
                                        !empty($columns[1]) &&
                                        !empty($columns[2]) &&
                                        !empty($columns[3]) &&
                                        !empty($columns[6]) &&
                                        !empty($columns[7])) {
                                        $columns[6] = ltrim((string)$columns[6], '0');
                                        $collection = $this->repCollection->create()->addFieldToSelect('*')
                                            ->addFieldToFilter('email', ['eq' => $columns[2]])
                                            ->addFieldToFilter('type', ['eq' => $columns[7]])
                                            ->addFieldToFilter('postal_code', ['eq' => $columns[6]]);
                                        if ($collection->getSize() > 0) {
                                            foreach ($collection as $item) {
                                                $item->setData('firstname', $columns[0]);
                                                $item->setData('lastname', $columns[1]);
                                                $item->setData('email', $columns[2]);
                                                $item->setData('phone1', $columns[3]);
                                                $item->setData('phone2', $columns[4]);
                                                $item->setData('notes', $columns[5]);
                                                $item->setData('postal_code', $columns[6]);
                                                $item->setData('type', $columns[7]);
                                            }
                                            $collection->save();
                                            $update++;
                                        } else {
                                            $model = clone $this->repFactory->create();
                                            try {
                                                $data = [
                                                    'firstname' => $columns[0],
                                                    'lastname' => $columns[1],
                                                    'email' => $columns[2],
                                                    'phone1' => $columns[3],
                                                    'phone2' => $columns[4],
                                                    'notes' => $columns[5],
                                                    'postal_code' => $columns[6],
                                                    'type' => $columns[7]
                                                ];
                                                $model->setData($data)->save();
                                                $count++;
                                            } catch (\Exception $e) {
                                                $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
                                            }
                                        }
                                    }
                                }
                                if ($update) {
                                    $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been Updated.', $update));
                                }
                                if ($count) {
                                    $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been Added.', $count));
                                }
                                return $resultRedirect->setPath('representative/rep/index');
                            } else {
                                $this->messageManager->addErrorMessage(__("Uploaded file has incorrect column header. Please check and upload a valid file."));
                                return $resultRedirect->setPath('representative/dataimport/importdata');
                            }
                        } else {
                            $this->messageManager->addErrorMessage(__("The uploaded file is empty. Please check and upload a valid file."));
                            return $resultRedirect->setPath('representative/dataimport/importdata');
                        }
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                    return $resultRedirect->setPath('representative/dataimport/importdata');
                }
            } else {
                $this->messageManager->addErrorMessage(__("You have uploaded an invalid file. Please upload .csv or .xls file."));
                return $resultRedirect->setPath('representative/dataimport/importdata');
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('representative/dataimport/importdata');
        }
    }
}
