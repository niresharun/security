<?php

namespace Wendover\ProductVideo\Plugin;

use Magento\Framework\DataObject;

class Gallery
{

    public function afterGetGalleryImagesJson(
        \Magento\Catalog\Block\Product\View\Gallery $subject,
        $result
    ) {
       
        $imagesItems = [];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $om->get('Magento\Store\Model\StoreManager');
         
        /** @var DataObject $image */
        foreach ($subject->getGalleryImages() as $image) {
            $mediaType = $image->getMediaType();
           	$videoPreviewUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product/videos/video_icon.png';

            $imageItem = new DataObject(
                [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => $image->getLabel() ?: $subject->getProduct()->getName(),
                    'position' => $image->getData('position'),
                    'isMain' => $subject->isMainImage($image),
                    'type' => $mediaType !== null ? str_replace('external-', '', $mediaType) : '',
                    'videoUrl' => $image->getVideoUrl(),
                ]
            );
            foreach ($subject->getGalleryImagesConfig()->getItems() as $imageConfig) {
                $imageItem->setData(
                    $imageConfig->getData('json_object_key'),
                    $image->getData($imageConfig->getData('data_object_key'))
                );
            }
            $imaArray = $imageItem->toArray();
           
            if($imaArray && $mediaType==='external-video') {
            	$imaArray['thumb'] 	= $videoPreviewUrl;
            	$imaArray['img'] 	= $videoPreviewUrl;
            	$imaArray['full'] 	= $videoPreviewUrl;
            }
            $imagesItems[] = $imaArray;
        }
        if (empty($imagesItems)) {
            $imagesItems[] = [
                'thumb' => $subject->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $subject->_imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $subject->_imageHelper->getDefaultPlaceholderUrl('image'),
                'caption' => '',
                'position' => '0',
                'isMain' => true,
                'type' => 'image',
                'videoUrl' => null,
            ];
        }
       
        return json_encode($imagesItems);
        
    }
}
