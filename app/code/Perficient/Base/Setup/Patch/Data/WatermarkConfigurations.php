<?php
/**
 * This module is used to add base configurations
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\StoreRepositoryInterface;
/**
 * Patch script for ups WatermarkConfigurations data
 */
class WatermarkConfigurations implements DataPatchInterface
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    final const IMAGE_SIZE = 'image_size';

    final const IMAGE_IMAGE = 'image_image';

    final const IMAGE_IMAGEOPACITY = 'image_imageOpacity';

    final const IMAGE_POSITION = 'image_position';

    final const SMALL_IMAGE_SIZE = 'small_image_size';

    final const SMALL_IMAGE_IMAGE = 'small_image_image';

    final const SMALL_IMAGE_IMAGEOPACITY = 'small_image_imageOpacity';

    final const SMALL_IMAGE_POSITION = 'small_image_position';

    final const THUMBNAIL_IMAGE_SIZE = 'thumbnail_size';

    final const  THUMBNAIL_IMAGE_IMAGE = 'thumbnail_image';

    final const  THUMBNAIL_IMAGE_IMAGEOPACITY = 'thumbnail_imageOpacity';

    final const  THUMBNAIL_IMAGE_POSITION = 'thumbnail_position';

    final const SCOPE = 'stores';

    final const PATH_PREFIX = 'design/watermark/';

    final const  THUMBNAIL_IMAGE_SIZE_VALUE = '50x50';

    final const  SMALL_IMAGE_SIZE_VALUE = '50x50';

    final const  IMAGE_SIZE_VALUE = '100x100';

    final const  IMAGE_OPACITY_VALUE = 100;

    final const  IMAGE_POSITION_VALUE = 'center';

    final const  IMAGE_VALUE = 'wendover_watermark_image.png';

    final const  SMALL_IMAGE_VALUE = 'wendover_watermark_small.png';

    final const  THUMBNAIL_IMAGE_VALUE = 'wendover_watermark_thumbnail.png';

    /**
     * Authorization level
     */
    final const ADMIN_RESOURCE = 'Magento_Theme::theme';
    /**#@-*/

    /**
     * ConfigData constructor.
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected StoreRepositoryInterface $storeRepository
    ) {
    }

    /**
     * Run code inside patch script
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->setImageSize();
        $this->setImage();
        $this->setImageOpacity();
        $this->setImagePosition();
        $this->setSmallImageSize();
        $this->setSmallImage();
        $this->setSmallImageOpacity();
        $this->setSmallImagePosition();
        $this->setThumbnailImageSize();
        $this->setThumbnailImage();
        $this->setThumbnailImageOpacity();
        $this->setThumbnailImagePosition();
        $this->moduleDataSetup->getConnection()->endSetup();
    }


    /**
     * Get Default Store Id
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreId(): int|string
    {
        $storeId = '';
        $store = $this->storeRepository->get('default');
        if ($store) {
            $storeId = $store->getId();
        }
        return $storeId;
    }

    /**
     * Image Size
     */
    public function setImageSize(): void
    {
        $pathImageSize = self::PATH_PREFIX . self::IMAGE_SIZE;
        $this->configWriter->save($pathImageSize, self::IMAGE_SIZE_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Image
     */
    public function setImage(): void
    {
        $pathImage = self::PATH_PREFIX . self::IMAGE_IMAGE;
        $imageValue = self::SCOPE.'/'.$this->getStoreId().'/'.self::IMAGE_VALUE;
        $this->configWriter->save($pathImage, $imageValue, self::SCOPE, $this->getStoreId());
    }

    /**
     * Image Opacity
     */
    public function setImageOpacity(): void
    {
        $pathImageOpacity = self::PATH_PREFIX . self::IMAGE_IMAGEOPACITY;
       $this->configWriter->save($pathImageOpacity, self::IMAGE_OPACITY_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Image Position
     */
    public function setImagePosition(): void
    {
        $pathImagePosition = self::PATH_PREFIX . self::IMAGE_POSITION;
        $this->configWriter->save($pathImagePosition, self::IMAGE_POSITION_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Small Image Size
     */
    public function setSmallImageSize(): void
    {
        $pathSmallImageSize = self::PATH_PREFIX . self::SMALL_IMAGE_SIZE;
        $this->configWriter->save($pathSmallImageSize, self::SMALL_IMAGE_SIZE_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Small Image
     */
    public function setSmallImage(): void
    {
        $pathSmallImage = self::PATH_PREFIX . self::SMALL_IMAGE_IMAGE;
        $smallImageValue = self::SCOPE.'/'.$this->getStoreId().'/'.self::SMALL_IMAGE_VALUE;
        $this->configWriter->save($pathSmallImage, $smallImageValue, self::SCOPE, $this->getStoreId());
    }

    /**
     * Small Image Opacity
     */
    public function setSmallImageOpacity(): void
    {
        $pathSmallImageOpacity = self::PATH_PREFIX . self::SMALL_IMAGE_IMAGEOPACITY;
        $this->configWriter->save($pathSmallImageOpacity, self::IMAGE_OPACITY_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Small Image Position
     */
    public function setSmallImagePosition(): void
    {
        $pathSmallImagePosition = self::PATH_PREFIX . self::SMALL_IMAGE_POSITION;
        $this->configWriter->save($pathSmallImagePosition, self::IMAGE_POSITION_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Thumbnail Image Size
     */
    public function setThumbnailImageSize(): void
    {
        $pathThumbnailImageSize = self::PATH_PREFIX . self::THUMBNAIL_IMAGE_SIZE;
        $this->configWriter->save($pathThumbnailImageSize, self::THUMBNAIL_IMAGE_SIZE_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Thumbnail Image
     */
    public function setThumbnailImage(): void
    {
        $pathThumbnailImage = self::PATH_PREFIX . self::THUMBNAIL_IMAGE_IMAGE;
        $thumbnailImageValue = self::SCOPE.'/'.$this->getStoreId().'/'.self::THUMBNAIL_IMAGE_VALUE;
        $this->configWriter->save($pathThumbnailImage, $thumbnailImageValue, self::SCOPE, $this->getStoreId());
    }

    /**
     * Thumbnail Image Opacity
     */
    public function setThumbnailImageOpacity(): void
    {
        $pathThumbnailImageOpacity = self::PATH_PREFIX . self::THUMBNAIL_IMAGE_IMAGEOPACITY;
        $this->configWriter->save($pathThumbnailImageOpacity, self::IMAGE_OPACITY_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * Thumbnail Image Position
     */
    public function setThumbnailImagePosition(): void
    {
        $pathThumbnailImagePosition = self::PATH_PREFIX . self::THUMBNAIL_IMAGE_POSITION;
        $this->configWriter->save($pathThumbnailImagePosition, self::IMAGE_POSITION_VALUE, self::SCOPE, $this->getStoreId());
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
