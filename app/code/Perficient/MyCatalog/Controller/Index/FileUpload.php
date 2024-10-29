<?php
/**
 * This module is used to create custom artwork catalogs,
 * This file contains the logic to upload new catalog logo
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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Perficient\MyCatalog\Helper\Data;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FileUpload
 * @package Perficient\MyCatalog\Controller\Index
 */
class FileUpload extends AbstractAction
{
    private array $validExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'bmp'
    ];

    /**
     * fileUpload constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $adapterFactory
     * @param Filesystem $filesystem
     * @param Http $http
     * @param JsonFactory $jsonResultFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly UploaderFactory $uploaderFactory,
        private readonly AdapterFactory $adapterFactory,
        private readonly Filesystem $filesystem,
        private readonly Http $http,
        private readonly JsonFactory $jsonResultFactory,
        private readonly LoggerInterface $logger
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
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        // First validate the customer.
        parent::validateCustomer();

        $data  = [];
        $error = '';
        $uploaderFactories = $this->uploaderFactory->create(['fileId' => 'logo']);

        $fileObj = $this->http->getFiles();
        $file    = $fileObj->get('logo');


        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/checkoutIssue.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('================= Upload START ======================');

        if (isset($file['name']) && $file['name'] != '') {
            $logger->info('================= if inside ======================');
            try {
                $error = $this->validateFile($file);
                if (!$error) {
                    $logger->info('================= validation ======================');
                    $file['name'] = uniqid() . $file['name'];
                    $logger->info('================= name ======================'.$file['name']);
                    $uploaderFactories->setAllowedExtensions($this->validExtensions);
                    $imageAdapter = $this->adapterFactory->create();
                    $uploaderFactories->addValidateCallback('custom_image_upload', $imageAdapter, 'validateUploadFile');
                    $uploaderFactories->setAllowRenameFiles(true);
                    $uploaderFactories->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath(Data::CATALOG_LOGO_PATH);
                    // $logger->info('================= destinationPath ======================'.$destinationPath);
                    $result = $uploaderFactories->save($destinationPath);

                     $logger->info('================= result ======================'.json_encode($result));
                    if (!$result) {
                        $logger->info('================= Not Upload ======================'.$result);
                        throw new \Exception(
                            __('File cannot be saved to path: %s', $destinationPath)
                        );
                    }
                    $imagePath = $destinationPath . $result['file'];
                    $data['logo'] = $imagePath;
                    $data['path'] = $destinationPath;
                    $data['file'] = $result;
                    $data['name'] = $result['file'];
                    $pathArray = explode("/",$result['file']);
                    // give to permission for logo path
                        // chmod($destinationPath.'/'.$pathArray[1], 0777);
                        // chmod($destinationPath.'/'.$pathArray[1].'/'.$pathArray[2], 0777);
                        // chmod($imagePath, 0777);
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $logger->info('=================  Upload catch ======================'.$e->getMessage());
                $this->logger->error($e->getMessage());
            }
        } else {
            $logger->info('================= else Upload inside ======================');
            $error = 'File not found!';
        }
        $logger->info('=================Upload END ======================');
        $data['error'] = $error;
        $data['size']  = $uploaderFactories->getFileSize();

        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }

    /**
     * Method used to validate uploaded logo file.
     */
    private function validateFile($file): \Magento\Framework\Phrase|string
    {
        $error = '';
        if (empty($file['tmp_name'])) {
            $error = __('Please select image file.');
        }

        /*If larger than 2MB and appropriate extension*/
        $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
        if ($file['size'] > $maxFileSize) {
            $error = __('The uploaded file exceeds the upload max filesize of 2MB.');
        }

        $extension = $this->getFileExtension($file['name']);
        if (!in_array($extension, $this->validExtensions)) {
            $error = __('Please select one of following file types: %1.', implode(', ', $this->validExtensions));
        }

        if (!$error) {
            switch ($file['error']) {
                case '1':
                    $error = __('The uploaded file exceeds the upload max filesize of 2MB.');
                    break;
                case '2':
                    $error = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
                    break;
                case '3':
                    $error = __('The uploaded file was only partially uploaded.');
                    break;
                case '4':
                    $error = __('No file was uploaded.');
                    break;
                case '6':
                    $error = __('Missing a temporary folder.');
                    break;
                case '7':
                    $error = __('Failed to write file to disk.');
                    break;
                case '8':
                    $error = __('File upload stopped by extension.');
                    break;
            }
        }

        return $error;
    }

    /**
     * Returns the extension of given filename.
     *
     * @param string
     * @return string
     */
    private function getFileExtension($filename)
    {
        $parts = explode('.', (string) $filename);
        return strtolower(end($parts));
    }
}
