<?php
/**
 * Modified Order Receipt Page and Email
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */

declare(strict_types=1);

namespace Perficient\Order\Block\Product;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\Image as ImageBlock;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\ConfigInterface;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Product image block
 */
class CustomImageFactory extends ImageFactory {
    /**
     * @var ConfigInterface
     */
    private $presentationConfig;

    private readonly AssetImageFactory $viewAssetImageFactory;

    /**
     * @var ParamsBuilder
     */
    private $imageParamsBuilder;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var PlaceholderFactory
     */
    private $viewAssetPlaceholderFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $presentationConfig
     * @param PlaceholderFactory $viewAssetPlaceholderFactory
     * @param ParamsBuilder $imageParamsBuilder
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $presentationConfig,
        AssetImageFactory $viewAssetImageFactory,
        PlaceholderFactory $viewAssetPlaceholderFactory,
        ParamsBuilder $imageParamsBuilder
    ) {
        parent::__construct($objectManager, $presentationConfig, $viewAssetImageFactory, $viewAssetPlaceholderFactory, $imageParamsBuilder);

        $this->objectManager = $objectManager;
        $this->presentationConfig = $presentationConfig;
        $this->viewAssetPlaceholderFactory = $viewAssetPlaceholderFactory;
        $this->viewAssetImageFactory = $viewAssetImageFactory;
        $this->imageParamsBuilder = $imageParamsBuilder;
    }

    /**
     * @param $item
     * @return string
     */
    public function getCustomizedProductImage($item) {
        $imageUrl = '';
        if(!$item) {
            return $imageUrl;
        }

        $options = $item->getProductOptions();
        if (isset($options['info_buyRequest']['pz_objects']) && !empty($options['info_buyRequest']['pz_objects'])) {
            $productimizeObject = json_decode((string) $options['info_buyRequest']['pz_objects']);
            if(json_last_error() === JSON_ERROR_NONE){
                if(is_array($productimizeObject)) {
                    $imageUrlObject = current($productimizeObject);
                    if(isset($imageUrlObject->baseImage->src)) {
                        $imageUrl = $imageUrlObject->baseImage->src;
                    }
                }
            }
        }

        return $imageUrl;
    }

    public function createImage(Product $product, $item, string $imageId, array $attributes = null): ImageBlock {
        $viewImageConfig = $this->presentationConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );

        $imageMiscParams = $this->imageParamsBuilder->build($viewImageConfig);
        $originalFilePath = $product->getData($imageMiscParams['image_type']);

        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $this->viewAssetPlaceholderFactory->create(
                [
                    'type' => $imageMiscParams['image_type']
                ]
            );
        } else {
            $imageAsset = $this->viewAssetImageFactory->create(
                [
                    'miscParams' => $imageMiscParams,
                    'filePath' => $originalFilePath,
                ]
            );
        }

        $attributes ??= [];

        $data = [
            'data' => [
                'template' => 'Magento_Catalog::product/image_with_borders.phtml',
                'image_url' => !empty($this->getCustomizedProductImage($item))?$this->getCustomizedProductImage($item):$imageAsset->getUrl(),
                'width' => $imageMiscParams['image_width'],
                'height' => $imageMiscParams['image_height'],
                'label' => $this->getLabel($product, $imageMiscParams['image_type']),
                'ratio' => $this->getRatio($imageMiscParams['image_width'] ?? 0, $imageMiscParams['image_height'] ?? 0),
                'custom_attributes' => $this->filterCustomAttributes($attributes),
                'class' => $this->getClass($attributes),
                'product_id' => $product->getId()
            ],
        ];

        return $this->objectManager->create(ImageBlock::class, $data);
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getProductImage($product, $imageId, $attributes = []) {
        return $this->create($product, $imageId, $attributes);
    }

    private function getLabel(Product $product, string $imageType): string {
        $label = $product->getData($imageType . '_' . 'label');
        if (empty($label)) {
            $label = $product->getName();
        }
        return (string)$label;
    }

    /**
     * Calculate image ratio
     */
    private function getRatio(int $width, int $height): float {
        if ($width && $height) {
            return $height / $width;
        }
        return 1.0;
    }

    /**
     * Remove class from custom attributes
     */
    private function filterCustomAttributes(array $attributes): array {
        if (isset($attributes['class'])) {
            unset($attributes['class']);
        }
        return $attributes;
    }

    /**
     * Retrieve image class for HTML element
     */
    private function getClass(array $attributes): string {
        return $attributes['class'] ?? 'product-image-photo';
    }
}
