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

namespace Perficient\Catalog\Block;

use Magento\Backend\Block\Template;
use \Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Image;
use \Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\Io\File;
use Perficient\Catalog\Model\CustomProductImage;
use Magento\MediaStorage\Service\ImageResize;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList as AppDirectoryList;
use Magento\Framework\Filesystem\Glob;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Backend\Model\Url;

/**
 * Class GetData
 * @package Perficient\Catalog\Block
 */
class GetData extends Template
{
    /**
     * Constants for default configuration / specification
     */
    const DEFAULT_CONFIG_PATH = '/catalog/product/default_configuration';
    const SPECIFICATIONS_PATH = '/catalog/product/specifications';
    const IMPORT_IMAGE_PATH = '/import/wendoverimages/';
    const VIDEOS_PATH   = '/catalog/product/videos';

    /**
     * Constants for image media type.
     */
    const IMAGE_BASE = 'base';
    const IMAGE_CROPPED = 'cropped';
    const IMAGE_SWATCHES = 'swatches';
    const IMAGE_SPECIFICATION = 'specification';
    const IMAGE_SINGLE_CORNER = 'single_corner';
    const IMAGE_SPEC_TYPE = 'spec_details';
    const IMAGE_SWATCHES_TYPE = 'swatch_image';
    const IMAGE_SMALL = 'small_images';

    /**
     * Constant for custom image version file.
     */
    const CUSTOM_IMAGE_VERSION_FILE = 'custom_image_version.txt';

    /**
     * Constant for maximum filename.
     */
    const MAX_FILENAME_LENGTH = 50;

    private array $imageOptions = [
        'image',
        'small_image',
        'thumbnail',
    ];

    private array $supportedImageTypes = [
        'png',
        'jpg',
        'jpeg'
    ];

    private array $supportedVideoTypes = [
        'mp4',
        'webm',
        'mov',
        'mpeg'
    ];

    /**
     * GetData constructor.
     * @param Context $context
     * @param Filesystem\DirectoryList $dir
     * @param File $file
     * @param ResourceConnection $resourceConnection
     * @param Image $imageModel
     * @param ImageResize $imageResize
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context                                                      $context,
        \Magento\Framework\Data\Form\FormKey                         $formKey,
        protected \Magento\Store\Api\StoreRepositoryInterface        $repository,
        \Magento\Framework\Translate\Inline\StateInterface           $inlineTranslation,
        protected \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder,
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        protected \Magento\Framework\Filesystem\DirectoryList        $dir,
        protected \Magento\Catalog\Api\ProductRepositoryInterface    $productRepository,
        protected \Magento\Store\Model\StoreManagerInterface         $storeManager,
        protected \Psr\Log\LoggerInterface                           $logger,
        private readonly File                                        $file,
        private readonly ResourceConnection                          $resourceConnection,
        private readonly Image                                       $imageModel,
        CustomProductImage                                           $customProductImage,
        private readonly ImageResize                                 $imageResize,
        private readonly Filesystem                                  $filesystem,
        private readonly Url                                         $backendUrlManager,
        private readonly DriverInterface                             $driver,
        private \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        private \Magento\ProductVideo\Model\ResourceModel\Video $videoResourceModel
    )
    {
        parent::__construct($context);
        $this->customProductImage = $customProductImage;
        $this->videoResourceModel = $videoResourceModel;
        $this->attributeRepository = $attributeRepository;
    }
    // end __construct()

    /**
     * Get store identifier
     *
     * @return  int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Load Product
     *
     * @param $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadMyProduct($sku)
    {
        return $this->productRepository->get($sku, true, 0);
    }

    /**
     * Return the Store Name and Id.
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->repository->getList();
    }
    // end getStoreName()

    /**
     * Get form key
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
    // end getFormKey()

    /**
     * Get file path
     *
     * @return array
     */
    public function allFolder()
    {
        return [
            'art/base',
            'frames/renderer_corner',
            'frames/single_corner',
            'frames/renderer_length',
            'frames/specification',
            'mats/base',
            'swatches',
            'frames/double_corner',
            'art/double_corner',
            'art/cropped',
            'mats/small_images',
            'mirrors/base',
            'videos'
        ];
    }
    // end allFolder

    /**
     * Get file path
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function coreLogic()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/bulkimport.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('----------------- Bulk Image Started ---------------------');

        $failureSku = [];
        $mainArray = [];
        foreach ($this->allFolder() as $folder => $image_type) {
            $files = Glob::glob($this->dir->getPath('media') . self::IMPORT_IMAGE_PATH . $image_type . '/*.*');
            for ($i = 0; $i < (is_countable($files) ? count($files) : 0); $i++) {
                $imagePath = $files[$i];
                $sku = '';
                $pathParts = $this->file->getPathInfo($imagePath);
                $ext = strtolower($pathParts['extension']);
                $getImgTypetemp = explode('/', (string)$image_type);
                $getImgType = end($getImgTypetemp);
                $basename = $pathParts['basename'];

                if (!in_array($ext, $this->supportedImageTypes) && $image_type !== 'videos') {
                    continue;
                }

                if (strpos($basename, '_') !== false) {
                    [$sku] = explode('_', (string) $basename);
                } else {
                    [$sku] = explode('.', (string) $basename);
                }

                $allFiles[$sku][$i] = $imagePath;

                // Rename all images with unique name.
                $imagePath = $this->renameImageName($imagePath, $sku);

                //if (isset($mainArray[$sku][$getImgType])) {
                  //  $mainArray[$sku]['gallary'][] = $imagePath;
                //} else {
                    $mainArray[$sku][$getImgType][] = $imagePath;
                //}
            }
        }

        $defaultConfPath = $this->dir->getPath('media') . self::DEFAULT_CONFIG_PATH;
        // Generate the feed file on server.

        if (!$this->driver->isDirectory($defaultConfPath)) {
            $this->file->mkdir($defaultConfPath, 0775, true);
        }
        $specificationPath = $this->dir->getPath('media') . self::SPECIFICATIONS_PATH;
        if (!$this->driver->isDirectory($specificationPath)) {
            $this->file->mkdir($specificationPath, 0775, true);
        }
        $customImage = [];
        if (count($mainArray) > 0) {
            $logger->info($mainArray);
            foreach ($mainArray as $sku => $images) {
                $sku = (string)$sku;

                // If there is no image then skip this product.
                if (empty($images)) {
                    continue;
                }

                try {
                    // Load the product.
                    $product = $this->loadMyProduct($sku);

                    // If there is product exists, then process it.
                    if ($product) {
                        // Update the customer image version in a file.
                        $this->updateCustomImageVersion();

		                // Unset video in the image update
                        $videos = (isset($images['videos']))?$images['videos']:[];
                        if($videos) {
                            unset($images['videos']);
                        }

		                // Loop on all the images and videos existed in the folder...
                        // Remove existing images for te image types for a product
		                foreach ($images as $getImgType => $imagePaths) {

                        	$folderFilesCount = count($imagePaths);
                        	$flag = 1;

                            // Loop the image base types
                            foreach ($imagePaths as $imagePath) {
                                // If no image for current image-type then skip it.
                                if (empty($imagePath) || $getImgType == 'gallary') {
                                    continue;
                                }

                                $mediaGalleryIds = [];

                                // Get the existing media gallery.
                                if($flag==1){
    		                        $existingMediaGalleryEntries = $product->getMediaGalleryEntries();

    		                        if (is_array($existingMediaGalleryEntries) && count($existingMediaGalleryEntries) > 0) {
    		                            foreach ($existingMediaGalleryEntries as $key => $entry) {
    		                                $mediaEntities = $entry->getTypes();

    		                                if ((is_countable($mediaEntities) ? count($mediaEntities) : 0) > 0) {
    		                                    // Remove/unset the existing image if we received currently.
    		                                    if (in_array($getImgType, $mediaEntities) || $getImgType == $entry->getLabel()) {
    		                                        $mediaGalleryIds[] = $entry->getId();
    		                                        unset($existingMediaGalleryEntries[$key]);
    		                                    } elseif (self::IMAGE_BASE == $getImgType && in_array('image', $mediaEntities)) {
    		                                        $mediaGalleryIds[] = $entry->getId();
    		                                        unset($existingMediaGalleryEntries[$key]);
    		                                    } elseif (self::IMAGE_SWATCHES == $getImgType
    		                                        && in_array(self::IMAGE_SWATCHES_TYPE, $mediaEntities)) {
    		                                        $mediaGalleryIds[] = $entry->getId();
    		                                        unset($existingMediaGalleryEntries[$key]);
    		                                    } elseif (self::IMAGE_SPECIFICATION == $getImgType
    		                                        && in_array(self::IMAGE_SPEC_TYPE, $mediaEntities)) {
    		                                        $mediaGalleryIds[] = $entry->getId();
    		                                        unset($existingMediaGalleryEntries[$key]);
    		                                    }
    		                                } elseif ($getImgType == $entry->getLabel() || $entry->getLabel() == 'image') {
    		                                    // Even, if the label matched, then delete this.
    		                                    $mediaGalleryIds[] = $entry->getId();
    		                                }

    		                            }
    		                            // Set the media gallery.
    		                            $product->setMediaGalleryEntries($existingMediaGalleryEntries);
    		                        }
    		                    }
    		                    $flag++;

                                // Add/Set the new image
                                if ($getImgType == self::IMAGE_BASE) {
                                    $path_parts = $this->file->getPathInfo($imagePath);
                                    $fileName = strtolower((string) $path_parts['filename']);
                                    $logger->info(json_encode([
                                        'Line' => __LINE__,
                                        'Sku' => $sku,
                                        'Filename' => $fileName,
                                        '$getImgType' => $getImgType,
                                        '$imagePath' => $imagePath
                                    ]));
                                    $product->addImageToMediaGallery($imagePath, $this->imageOptions, false, false);

                                } else {
                                    if ($getImgType == self::IMAGE_CROPPED) {
                                        $logger->info(json_encode([
                                            'Line' => __LINE__,
                                            '$getImgType' => $getImgType,
                                            '$imagePath' => $imagePath
                                        ]));
                                        $product->addImageToMediaGallery($imagePath, [$getImgType], false, true);
                                    } else {
                                        $fileInfo = $this->file->getPathInfo($imagePath);
                                        $basename = $fileInfo['basename'];
                                        if ($getImgType == self::IMAGE_SPECIFICATION) {
                                            $getImgType = self::IMAGE_SPEC_TYPE;
                                            $this->copyImageToMedia($imagePath, self::SPECIFICATIONS_PATH);
                                            $customImage[] = [
                                                'sku' => $sku,
                                                'image' => $basename,
                                                'type' => 'specifications'
                                            ];
                                        } elseif ($getImgType == self::IMAGE_SINGLE_CORNER) {
                                            $this->copyImageToMedia($imagePath, self::DEFAULT_CONFIG_PATH);
                                            $customImage[] = [
                                                'sku' => $sku,
                                                'image' => $basename,
                                                'type' => 'default_configuration'
                                            ];
                                        } elseif ($getImgType == self::IMAGE_SWATCHES) {
                                            $getImgType = self::IMAGE_SWATCHES_TYPE;
                                        } elseif ($getImgType == self::IMAGE_SMALL) {
                                            $this->copyImageToMedia($imagePath, self::DEFAULT_CONFIG_PATH);
                                            $customImage[] = [
                                                'sku' => $sku,
                                                'image' => $basename,
                                                'type' => 'default_configuration'
                                            ];
                                        }

                                        if ($getImgType != self::IMAGE_SMALL) {
                                            if (!is_array($imagePath)) {
                                                if ($getImgType == self::IMAGE_SINGLE_CORNER) {
                                                    // In case of single_corner, set this image as Base, Thumbnail as well.
                                                    $imgTypes = $this->imageOptions;
                                                    array_push($imgTypes, $getImgType);
                                                    $product->addImageToMediaGallery($imagePath, $imgTypes, false, false);
                                                } else {
                                                    $product->addImageToMediaGallery($imagePath, [$getImgType], false, false);
                                                }
                                            } else {
                                                foreach ($imagePath as $image) {
                                                    if (!empty($image)) {
                                                        $product->addImageToMediaGallery($image, null, false, false);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                // Delete existing media images.
                                if (!empty($mediaGalleryIds)) {
                                    $this->deleteOldMediaGalleryImages($mediaGalleryIds);
                                }
                            }
                            // Loop the image base types
                        }
                        // Loop all te images end


			            // Finally, save the product.
			            $product->save();

                		// import videos for product
                        if ($videos) {
                            $this->importVideoForProduct($product,$videos,$failureSku);
                        }
                        // Image regeneration
                        $time_start = microtime(true);
                        $logger->info('Product SKU: ' . $sku);
                        $logger->info('Image Resizing Start Time: ' . $time_start);

                        $galleryImages = $product->getMediaGalleryImages();
                        if ($galleryImages) {
                            $imageItems = $galleryImages->getItems();
                            if (is_countable($imageItems) ? count($imageItems) : 0) {
                                foreach ($imageItems as $image) {
                                    try {
                                        $this->imageResize->resizeFromImageName($image->getFile());
                                    } catch (\Exception $e) {
                                        $logger->info('Resize Error: ' . $e->getMessage());
                                    }
                                }
                            }
                        }

                        $time_end = microtime(true);
                        $logger->info('Start: ' . $time_start . ' :: End: ' . $time_end);
                        $time = $time_end - $time_start;
                        $logger->info('Image Resizing End Time: ' . $time_end);
                        $logger->info('Time Required: ' . $time);

                        // Delete all the copied image from server.
                        foreach ($images as $getImgType => $imagePath) {
                            if (is_array($imagePath)) {
                                foreach ($imagePath as $image) {
                                    $this->file->rm($image);
                                }
                            } else {
                                $this->file->rm($imagePath);
                            }
                        }
                    } else {
                        $failureSku[] = "Products with SKU(s) " . $sku . " does not exists";;
                    }
                } catch (\Exception $e) {
                    $this->logger->debug('Error in Media Gallery Image Assignment: ' . $e->getMessage());
                    $message = $e->getMessage();
                    $failureSku[] = "Product SKU - ". $sku . " Error in Media Gallery Image Assignment";
                    //$this->sendNotification($message);
                }
            }

            // send admin notification for te failure SKU
            if($failureSku) {
                $message = implode(",", $failureSku);
                $this->sendNotification($message);
            }

            // Save custom image in database
            $this->customProductImage->saveAll($customImage);

            // Clear the image cache.
            //$this->imageModel->clearCache();
        }
        $logger->info('----------------- Bulk Image Processed ---------------------');

        // Return to the calling function.
        return true;
    }
    // end coreLogic


    private function importVideoForProduct($product,$videos,&$failureSku)
    {
        $videoPath = $videos[0];
        $pathParts = $this->file->getPathInfo($videoPath);

        // File size validation
        $fileSizeInBytes = filesize($videoPath);
        $fileSizeInMB = round($fileSizeInBytes / (1024 * 1024), 2);
        $extension = strtolower($pathParts['extension']);
        if (!in_array($extension, $this->supportedVideoTypes) || ($fileSizeInMB > 2)) {
            $failureSku[] = 'SKU - ' . $product->getSku() . 'File size exceeds 2MB';
            return;
        }

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $om->get('Magento\Store\Model\StoreManager');

        $resourceConnection = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
        $connection = $resourceConnection->getConnection();
        $galleryTable = $resourceConnection->getTableName('catalog_product_entity_media_gallery');
        $galleryValueEntityTable = $resourceConnection->getTableName('catalog_product_entity_media_gallery_value_to_entity');
        $videoEntityTable = $resourceConnection->getTableName('catalog_product_entity_media_gallery_value_video');

        // Delete existing videos handled in the image
        $query = "SELECT `cepm`.value_id, `cpv`.url FROM " . $galleryTable . " AS `cepm` LEFT JOIN " . $galleryValueEntityTable . " AS `cpemg` ON `cpemg`.value_id = `cepm`.value_id LEFT JOIN " . $videoEntityTable . " AS `cpv` ON  `cpv`.value_id = `cpemg`.value_id Where `cepm`.media_type = 'external-video' AND `cpemg`.row_id = " . $product->getId();
        $result = $connection->fetchRow($query);

        if ($result) {
            $deleteId = $result['value_id'];
            $videoName = $result['url'];
            $this->deleteOldMediaGalleryImages([$deleteId]);

            // remove old video in the catalo video directory
            if ($videoName) {
                $videoName = explode('/', $videoName);
                $videoName = end($videoName);
                $videoName = $this->dir->getPath('media') . self::VIDEOS_PATH . '/' . $videoName;
                $this->file->rm($videoName);
            }
        }

        try {
            //Set video data for product
            $fileName = (string)$pathParts['filename'] . "." . $pathParts['extension'];

            $videoUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/videos/' . $fileName;
            $this->copyImageToMedia($videoPath, self::VIDEOS_PATH);

            $sql = "INSERT INTO " . $galleryTable . "(attribute_id, value, media_type, disabled) VALUES (88, 'video-icon.png','external-video',0)";
            $connection->query($sql);
            $valueId = $connection->lastInsertId($galleryTable);

            $sql = "INSERT INTO " . $galleryValueEntityTable . "(value_id, row_id) VALUES (" . $valueId . "," . $product->getId() . ")";
            $connection->query($sql);

            $galleryValueTable = $resourceConnection->getTableName('catalog_product_entity_media_gallery_value');
            $sql = "INSERT INTO " . $galleryValueTable . "(value_id, store_id, label, disabled, row_id) VALUES (" . $valueId . ", 0, '" . $product->getName() . ' Video' . "',0," . $product->getId() . ")";
            $connection->query($sql);

            if ($valueId) {
                // sample video data
                $videoData = [
                    'value_id' => $valueId,
                    'title' => $product->getName() . " Video", //set your video title
                    'description' => "description", //set your video description
                    'url' => $videoUrl,  //set your video thumbnail path.
                    'provider' => "upload",
                    'store_id' => 0,
                ];
                $this->videoResourceModel->insertOnDuplicate($videoData);
            }

            // remove video in the bulk import directory
            $this->file->rm($videoPath);
            return true;
        } catch (\Exception $e) {
            $this->logger->debug('Error in videos import' . $e->getMessage());
        }
    }

    /**
     * @param $imagePath
     * @param $folderPath
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function copyImageToMedia($imagePath, $folderPath)
    {
        $imgCopy = $this->dir->getPath('media') . $folderPath;
        $getImageName = explode('/', (string)$imagePath);
        $this->driver->copy($imagePath, $imgCopy . '/' . end($getImageName));
    }

    private function deleteVideoEntry($valueId)
    {
    	$connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('catalog_product_entity_media_gallery_value_video');
        $sql = 'DELETE FROM ' . $table
            . ' WHERE value_id IN (' . implode(',', $imageIds) . ')';
        $connection->query($sql);
    }

    /**
     * Method used to delete old media gallery images.
     *
     * @param $imageIds
     */
    private function deleteOldMediaGalleryImages($imageIds)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $table = $connection->getTableName('catalog_product_entity_media_gallery');
            $sql = 'DELETE FROM ' . $table
                . ' WHERE value_id IN (' . implode(',', $imageIds) . ')';
            $connection->query($sql);
        } catch (\Exception $e) {
            $this->logger->debug('Error in deleting Old Media Gallery Image: ' . $e->getMessage());
        }
    }

    /**
     * Method used to update customer image version in file.
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function updateCustomImageVersion()
    {
        $media = $this->filesystem->getDirectoryWrite(AppDirectoryList::MEDIA);
        $media->writeFile(self::CUSTOM_IMAGE_VERSION_FILE, time());
    }

    /**
     * Method used to rename the image name.
     *
     * @param $imagePath
     * @param $getSku
     * @return string
     */
    private function renameImageName($imagePath, $getSku)
    {
        $imgInfo = $this->file->getPathInfo((string)$imagePath);
        if (strlen($imgInfo['filename'] . '.' . $imgInfo['extension']) > self::MAX_FILENAME_LENGTH) {
            $imgInfo['filename'] = $getSku;
        }
        $renameFile = $imgInfo['dirname'] .'/'. $imgInfo['filename'] . '_' . uniqid() . '.' . $imgInfo['extension'];
        rename($imagePath, $renameFile);
        return $renameFile;
    }

    /**
     * Send Email Notification
     *
     * @param $content
     * @return bool ->inlineTranslation->resume();
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendNotification($content)
    {

        $notifyEnable = $this->scopeConfig->getValue('perficient_bulk_upload/cron_setup/cron_notification_enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$notifyEnable) {
            return false;
        }
        $this->inlineTranslation->suspend();
        $sentToEmail = $this->scopeConfig->getValue('perficient_bulk_upload/cron_setup/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sentToName = $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender = [
            'name' => $sentToName,
            'email' => $sentToEmail
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('importemail_email_failture_notification')
            ->setTemplateOptions(
                [
                    'area' => 'frontend',
                    'store' => $this->storeManager->getStore()->getId()
                ]
            )
            ->setTemplateVars([
                'content' => $content
            ])
            ->setFromByScope($sender)
            ->addTo($sentToEmail, $sentToName)
            //->addTo('owner@example.com','owner')
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }
    // end sendNotification

    public function getActionUrl() {

        return $this->backendUrlManager->getUrl('productbulkupload/index/imgupload');
    }

    public function getConfUrl() {

        return $this->backendUrlManager->getUrl('admin/system_config/edit/section/perficient_bulk_upload');
    }

    public function getActionUrlimgdisassociate() {

        return $this->backendUrlManager->getUrl('productbulkupload/index/imgdisassociate');
    }
}
// end class
