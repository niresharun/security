<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\Catalog\Model\Product\Gallery;

use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;

/**
 * Catalog product Media Gallery attribute processor.
 *
 * @api
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 101.0.0
 */
class Processor extends \Magento\Catalog\Model\Product\Gallery\Processor
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     * @since 101.0.0
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\File\Mime
     */
    private $mime;

    /**
     * @param \Magento\Framework\File\Mime|null $mime
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\MediaStorage\Helper\File\Storage\Database       $fileStorageDb,
        \Magento\Catalog\Model\Product\Media\Config              $mediaConfig,
        \Magento\Framework\Filesystem                            $filesystem,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery     $resourceModel,
        \Magento\Framework\File\Mime                             $mime = null
    )
    {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mime = $mime ?: ObjectManager::getInstance()->get(\Magento\Framework\File\Mime::class);
        parent::__construct($attributeRepository, $fileStorageDb, $mediaConfig, $filesystem, $resourceModel, $mime);
    }


    /**
     * Add image to media gallery and return new filename
     *
     * @param string $file file path of image in file system
     * @param string|string[] $mediaAttribute code of attribute with type 'media_image',
     * leave blank if image should be only in gallery
     * @param boolean $move if true, it will move source file
     * @param boolean $exclude mark image as disabled in product page view
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @since 101.0.0
     */
    public function addImage(
        \Magento\Catalog\Model\Product $product,
                                       $file,
                                       $mediaAttribute = null,
                                       $move = false,
                                       $exclude = true
    )
    {
        $file = $this->mediaDirectory->getRelativePath($file);
        if (!$this->mediaDirectory->isFile($file)) {
            throw new LocalizedException(__("The image doesn't exist."));
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $pathinfo = pathinfo((string)$file);
        $imgExtensions = ['jpg', 'jpeg', 'gif', 'png'];
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
            throw new LocalizedException(
                __('The image type for the file is invalid. Enter the correct image type and try again.')
            );
        }

        $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($pathinfo['basename']);
        $dispersionPath = \Magento\MediaStorage\Model\File\Uploader::getDispersionPath($fileName);
        $fileName = $dispersionPath . '/' . $fileName;

        $fileName = $this->getNotDuplicatedFilename($fileName, $dispersionPath);

        $destinationFile = $this->mediaConfig->getTmpMediaPath($fileName);

        try {
            /** @var $storageHelper \Magento\MediaStorage\Helper\File\Storage\Database */
            $storageHelper = $this->fileStorageDb;
            if ($move) {
                $this->mediaDirectory->renameFile($file, $destinationFile);

                //If this is used, filesystem should be configured properly
                $storageHelper->saveFile($this->mediaConfig->getTmpMediaShortUrl($fileName));
            } else {
                $this->mediaDirectory->copyFile($file, $destinationFile);

                $storageHelper->saveFile($this->mediaConfig->getTmpMediaShortUrl($fileName));
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('The "%1" file couldn\'t be moved.', $e->getMessage()));
        }

        $fileName = str_replace('\\', '/', (string)$fileName);

        $attrCode = $this->getAttribute()->getAttributeCode();
        $mediaGalleryData = $product->getData($attrCode);
        $position = 0;

        $absoluteFilePath = $this->mediaDirectory->getAbsolutePath($destinationFile);
        $imageMimeType = $this->mime->getMimeType($absoluteFilePath);
        $imageContent = $this->mediaDirectory->readFile($absoluteFilePath);
        $imageBase64 = base64_encode((string)$imageContent);
        $imageName = $pathinfo['filename'];

        if (!is_array($mediaGalleryData)) {
            $mediaGalleryData = ['images' => []];
        }

        foreach ($mediaGalleryData['images'] as &$image) {
            if (isset($image['position']) && $image['position'] > $position) {
                $position = $image['position'];
            }
        }

        $position++;
        if (is_array($mediaAttribute)) {
            $label = $mediaAttribute[0] ?? $product->getName();
        } else {
            $label = $mediaAttribute;
        }

        $mediaGalleryData['images'][] = [
            'file' => $fileName,
            'position' => $position,
            'label' => $label,
            'disabled' => (int)$exclude,
            'media_type' => 'image',
            'types' => $mediaAttribute,
            'content' => [
                'data' => [
                    ImageContentInterface::NAME => $imageName,
                    ImageContentInterface::BASE64_ENCODED_DATA => $imageBase64,
                    ImageContentInterface::TYPE => $imageMimeType,
                ]
            ]
        ];

        $product->setData($attrCode, $mediaGalleryData);

        if ($mediaAttribute !== null) {
            $this->setMediaAttribute($product, $mediaAttribute, $fileName);
        }

        return $fileName;
    }


}
