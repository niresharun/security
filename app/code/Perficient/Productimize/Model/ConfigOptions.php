<?php
/**
 * The class used to get the default options of the product as well as calculate the image, item size.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Perficient\Catalog\Helper\Data;

/**
 * Class used to get the default options of the product as well as calculate the image, item size.
 *
 * Class ConfigOptions
 */
class ConfigOptions extends AbstractModel
{
    /**
     * Constants used in image/item/glass calculation.
     */
    const ADDITIONAL_LENGTH = 0.5;
    const MULTIPLIER_LENGTH = 2;
    const FLOATER_LENGTH    = 0.25;

    /**
     * Constants for frame types.
     */
    const FRAME_TYPE_STANDARD = 'standard';
    const FRAME_TYPE_FLOATER  = 'floater';
    const FRAME_TYPE_LINER    = 'liner';

    /**
     * @var
     */
    private $optionsJson;

    /**
     * @var
     */
    private $artProduct;

    /**
     * @var
     */
    private $frameProduct;

    /**
     * @var
     */
    private $linerProduct;

    /**
     * @var Json
     */
    private \Magento\Framework\Serialize\Serializer\Json $json;

    /**
     * @var LoggerInterface
     */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * @var ProductRepositoryInterface
     */
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    /**
     * @var
     */
    private \Perficient\Catalog\Helper\Data $perficientCatalogHelper;

    public static $defaultArtConfLabel = [
        'liner_sku' => 'Liner',
        'frame_default_sku' => 'Frame',
        'top_mat_default_sku' => 'Top Mat',
        'bottom_mat_default_sku' => 'Bottom Mat',
        //'side-mark' => 'Side Mark',
        //'bottom_mat_sku' => 'Bottom Mat SKU',
        'frame_width' => 'Frame Width',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        'medium' => 'Medium',
        'glass_width' => 'Glass Width',
        'glass_height' => 'Glass Height',
        'art_work_color' => 'Artwork Color',
        'side_mark' => 'Side Mark',
        'liner_width' => 'Liner Width',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        'image_height' => 'Image Height',
        'image_width' => 'Image Width',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
        'treatment' => 'Treatment',
        //'frame_sku' => 'Frame SKU',
        //'top_mat_sku' => 'Top Mat SKU',
        'default_frame_depth' => 'Frame Depth',
        'default_liner_depth' => 'Liner Depth',
        'default_frame_color' => 'Frame Color',
        'default_liner_color'=> 'Liner Color',
        'default_top_mat_color'=> 'Top Mat Color',
        'default_bottom_mat_color'=>'Bottom Mat Color'
    ];

    /**
     * ConfigOptions constructor.
     * @param Json $json
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param Data $perficientCatalogHelper
     */
    public function __construct(
        Json $json,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        Data  $perficientCatalogHelper
    ) {
        $this->json              = $json;
        $this->logger            = $logger;
        $this->productRepository = $productRepository;
        $this->perficientCatalogHelper = $perficientCatalogHelper;
    }

    /**
     * Method used to get the default configuration options.
     *
     * @param int $artProductId
     * @param array $configJson
     * @return bool|false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultConfigurationOptions($artProductId, $configJson = [])
    {
        try {
            $this->artProduct = null;
            if ($configJson && !empty($configJson)) {
                $configJson = $this->perficientCatalogHelper->createValidJson($configJson);
            }
            if (!empty($configJson)) {
                $configJson = $this->json->unserialize($configJson);
                // Get the blank options.
                $this->_getBlankOptions($configJson);

                // Get the artwork product options.
                $this->_getArtProductOptions($configJson, $artProductId);

                // Get the image size.
                $imageSize = $this->getImageSize($artProductId, $configJson);
                $this->optionsJson['image_width'] = $imageSize['width'];
                $this->optionsJson['image_height'] = $imageSize['height'];

                // Get the glass size.
                $glassSize = $this->getGlassSize($artProductId, $configJson);
                $this->optionsJson['glass_width'] = $glassSize['width'];
                $this->optionsJson['glass_height'] = $glassSize['height'];


                // Get the item size.
                $itemSize = $this->getItemSize($artProductId, $configJson);
                $this->optionsJson['item_width'] = $itemSize['width'];
                $this->optionsJson['item_height'] = $itemSize['height'];

                // Return the final options.
                return $this->json->serialize($this->optionsJson);
            }
        }catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return false;
        }

    }

    /**
     * @param $artProductId
     * @param array $configJson
     * @param null $frameSku
     * @param null $linerSku
     * @return bool|false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSelectedConfigurationOptions($artProductId, $configJson = [], $frameSku = null, $linerSku = null)
    {

       try {
           $this->artProduct = null;
           if ($configJson && !empty($configJson)) {

               $configJsonRaw = $this->perficientCatalogHelper->createValidJson($configJson);

               $configJson = $this->json->unserialize($configJsonRaw);
           }
           // Get the blank options.
           $this->_getBlankOptions($configJson);

           // Get the artwork product options.
           $this->_getArtProductOptions($configJson, $artProductId);

           // Get the image size.
           $imageSize = $this->getImageSize($artProductId, $configJson, $frameSku, $linerSku);
           $this->optionsJson['image_width'] = $imageSize['width'];
           $this->optionsJson['image_height'] = $imageSize['height'];

           // Get the glass size.
           $glassSize = $this->getGlassSize($artProductId, $configJson, $frameSku, $linerSku);
           $this->optionsJson['glass_width'] = $glassSize['width'];
           $this->optionsJson['glass_height'] = $glassSize['height'];

           // Get the item size.
           $itemSize = $this->getItemSize($artProductId, $configJson);
           $this->optionsJson['item_width'] = $itemSize['width'];
           $this->optionsJson['item_height'] = $itemSize['height'];

           // Return the final options.
           return $this->json->serialize($this->optionsJson);
       }catch (\Exception $e) {
           $this->logger->debug($e->getMessage());
           return false;
       }
    }

    /**
     * @param $artProductId
     * @param array $configJson
     * @param null $frameSku
     * @param null $linerSku
     * @param bool $arrayForm
     * @return array|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSelectedConfOptionsWishlist(
        $artProductId,
        $configJson = [],
        $frameSku = null,
        $linerSku = null,
        $arrayForm = false
    ) {
        $this->artProduct = null;
        if ($configJson && !empty($configJson)) {
            $configJson = $this->json->unserialize($configJson);
        }
        // Get the blank options.
        $this->_getBlankOptions($configJson);

        // Get the artwork product options.
        $this->_getArtProductOptions($configJson, $artProductId);

        // Get the image size.
        $imageSize = $this->getImageSize($artProductId, $configJson, $frameSku, $linerSku);
        $this->optionsJson['image_width'] = $imageSize['width'];
        $this->optionsJson['image_height'] = $imageSize['height'];

        // Get the glass size.
        $glassSize = $this->getGlassSize($artProductId, $configJson, $frameSku, $linerSku);
        $this->optionsJson['glass_width'] = $glassSize['width'];
        $this->optionsJson['glass_height'] = $glassSize['height'];

        // Get the item size.
        $itemSize = $this->getItemSize($artProductId, $configJson);
        $this->optionsJson['item_width'] = $itemSize['width'];
        $this->optionsJson['item_height'] = $itemSize['height'];

        $optionsJsonWithLabel = [];
        $optionLabels = self::$defaultArtConfLabel;
        foreach ($this->optionsJson as $optionKey => $value) {
            if (isset($optionLabels[$optionKey])) {
                $optionsJsonWithLabel[$optionLabels[$optionKey]] = $value;
            } else {
                $optionsJsonWithLabel[$optionKey] = $value;
            }
        }

        if ($arrayForm) {
            return $optionsJsonWithLabel;
        } else {
            return $this->json->serialize($optionsJsonWithLabel);
        }
    }

    /**
     * Method used to set the check and set blank configuration options
     *
     * @param $configJson
     *
     * @return void
     */
    private function _getBlankOptions($configJson)
    {
      try {
          foreach ($this->perficientCatalogHelper->configFieldsBlank as $field) {
              if (!isset($configJson[$field])) {
                  $configJson[$field] = '';
              }
              $this->optionsJson[$field] = $configJson[$field];
          }
      }catch (\Exception $e) {
          $this->logger->debug($e->getMessage());
      }
    }

    /**
     * Method used to check and get artwork product configuration options
     *
     * @param $configJson
     *
     * @param $artProductId
     * @return void
     */
    private function _getArtProductOptions($configJson, $artProductId)
    {
        try {
            if (!$this->artProduct) {
                $this->_getArtProduct($artProductId);
            }
            // Loop on all the fields to load from the product.
            foreach ($this->perficientCatalogHelper->configFieldsRetrieve as $field) {
                $configJson[$field] = !isset($configJson[$field]) && $this->artProduct ? $this->artProduct->getData($field) : '';
                $this->optionsJson[$field] = $configJson[$field];
            }
        }catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * Method used to calculate the image width and height.
     *
     * @param $artProductId
     * @param $configJson
     * @param null $frameSku
     * @param null $linerSku
     * @return array
     */
    public function getImageSize($artProductId, $configJson, $frameSku = null, $linerSku = null)
    {
        try {
            // if configJson already contains image_width and image_height pass default values.
            if (isset($configJson['image_width']) && !empty($configJson['image_width']) &&
                isset($configJson['image_height']) || !empty($configJson['image_height'])) {
                // Return the image width and height.
                return [
                    'width' => $configJson['image_width'],
                    'height' => $configJson['image_height']
                ];
            }

            // Check, if the art-product is not available then load it by passed art-product-id.
            if (!$this->artProduct) {
                $this->_getArtProduct($artProductId);
            }

            // Set the default value for image width and height.
            $imageWidth = $imageHeight = 0;

            if ($this->artProduct) {
                $artProduct = $this->artProduct;

                // Set the values to vars.
                $imageWidth = (float)$artProduct->getImageWidth();
                $imageHeight = (float)$artProduct->getImageHeight();
                $glassWidth = (float)$artProduct->getGlassWidth();
                $glassHeight = (float)$artProduct->getGlassHeight();
                $topMatSizeLeft = (float)$artProduct->getTopMatSizeLeft();
                $topMatSizeRight = (float)$artProduct->getTopMatSizeRight();
                $topMatSizeTop = (float)$artProduct->getTopMatSizeTop();
                $topMatSizeBottom = $artProduct->getTopMatSizeBottom();

                // If glass width/height is not set then first calculate the glass width and height.
                if (!$glassWidth || !$glassHeight || empty($glassWidth) || empty($glassHeight)) {
                    $glassSize = $this->getGlassSize($artProductId, $frameSku, $linerSku);
                    if (!$glassWidth || empty($glassWidth)) {
                        $glassWidth = $glassSize['width'];
                    }

                    if (!$glassHeight || empty($glassHeight)) {
                        $glassHeight = $glassSize['height'];
                    }
                }

                /**
                 * If image-width is not set then calculate it based on below calculation:
                 *
                 * Image Width (if mat present) = Glass Width - (Mat Left Size + Mat Right Sizes) + .5
                 * Image Width (if no mat) = GlassWidth
                 */
                if (!$imageWidth || empty($imageWidth)) {
                    $imageWidth = $glassWidth;
                    if ($topMatSizeLeft && $topMatSizeRight && !empty($topMatSizeLeft) && !empty($topMatSizeRight)) {
                        $imageWidth = $glassWidth - ($topMatSizeLeft + $topMatSizeRight) + self::ADDITIONAL_LENGTH;
                    }
                }

                /**
                 * If image-height is not set then calculate it based on below calculation:
                 *
                 * Image Height (if mat present) = Glass Height - (Mat Top Size + Mat Bottom Sizes) + .5
                 * Image Height (if no mat) = GlassHeight
                 */
                if (!$imageHeight || empty($imageHeight)) {
                    $imageHeight = $glassHeight;
                    if ($topMatSizeTop && $topMatSizeBottom && !empty($topMatSizeTop) && !empty($topMatSizeBottom)) {
                        $imageHeight = $glassHeight - ($topMatSizeTop + $topMatSizeBottom) + self::ADDITIONAL_LENGTH;
                    }
                }
            }

            // Return the image width and height.
            return [
                'width' => $imageWidth,
                'height' => $imageHeight
            ];
        }catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * Method used to calculate the glass width and height.
     *
     * @param $artProductId
     * @param $configJson
     * @param null $frameSku
     * @param null $linerSku
     * @return array
     */
    public function getGlassSize($artProductId, $configJson, $frameSku = null, $linerSku = null)
    {
        try {
            // if configJson already contains glass_width and glass_height pass default values.
            if (isset($configJson['glass_width']) && !empty($configJson['glass_width']) &&
                isset($configJson['glass_height']) || !empty($configJson['glass_height'])) {
                // Return the glass width and height.
                return [
                    'width' => $configJson['glass_width'],
                    'height' => $configJson['glass_height']
                ];
            }

            // Check, if the art-product is not available then load it by passed art-product-id.
            if (!$this->artProduct) {
                $this->_getArtProduct($artProductId);
            }

            // Set the default value for glass width and height.
            $glassWidth = $glassHeight = 0;

            if ($this->artProduct) {
                $artProduct = $this->artProduct;

                // Set the values to var.
                $glassWidth = (float)$artProduct->getGlassWidth();
                $glassHeight = (float)$artProduct->getGlassHeight();
                $outerWidth = (float)$artProduct->getItemWidth();
                $outerHeight = (float)$artProduct->getItemHeight();
                $frameWidth = (float)$artProduct->getFrameWidth();

                $standardFrameType = $floaterFrameType = $liner = $linerWidth = 0;

                // Check for the frame-sku
                if (!$frameSku || empty($frameSku)) {
                    $frameSku = $artProduct->getFrameDefaultSku();
                }
                if (!empty($frameSku)) {
                    if (!$this->frameProduct) {
                        $this->frameProduct = $this->_getProductBySku($frameSku);
                    }
                    if ($this->frameProduct) {
                        $frameType = $this->frameProduct->getFrameType();
                        if (!empty($frameType) && self::FRAME_TYPE_STANDARD == strtolower($frameType)) {
                            $standardFrameType = self::ADDITIONAL_LENGTH;
                        }
                        if (!empty($frameType) && self::FRAME_TYPE_FLOATER == strtolower($frameType)) {
                            $floaterFrameType = self::FLOATER_LENGTH;
                        }
                    } else {
                        $this->logger->error(__('Unable to load frame product #%1.', $frameSku));
                    }
                }

                // Check for the liner-sku
                if (!$linerSku || empty($linerSku)) {
                    $linerSku = $artProduct->getLinerSku();
                }
                if (!empty($linerSku)) {
                    if (!$this->linerProduct) {
                        $this->linerProduct = $this->_getProductBySku($linerSku);
                    }
                    if ($this->linerProduct) {
                        $linerWidth = $this->linerProduct->getFrameWidth();
                        $frameType = $this->linerProduct->getFrameType();
                        if (!empty($frameType) && self::FRAME_TYPE_LINER == strtolower($frameType)) {
                            $liner = self::ADDITIONAL_LENGTH;
                        }
                    } else {
                        $this->logger->error(__('Unable to load liner product #%1.', $linerSku));
                    }
                }

                // Check, if outer/item width is not set then first calculate it.
                if (!$outerWidth || empty($outerWidth)) {
                    $itemSize = $this->getItemSize($artProductId, $frameSku, $linerSku);
                    $outerWidth = $itemSize['width'];
                }

                /**
                 * If glass-width is not set then calculate it based on below calculation:
                 *
                 * Glass Width = Outer Width - FrameWidth*2 - Liner Width*2 + (0.5" if frame type = Standard)
                 *               - (0.25" if frame type = Floater) + (0.5" if liner is selected)
                 */
                if (!$glassWidth || empty($glassWidth)) {
                    $glassWidth = $outerWidth - ($frameWidth * self::MULTIPLIER_LENGTH) -
                        ($linerWidth * self::MULTIPLIER_LENGTH) + $standardFrameType - $floaterFrameType + $liner;
                }

                /**
                 * If glass-height is not set then calculate it based on below calculation:
                 *
                 * Glass Height = Outer Height - FrameWidth*2 - Liner Width*2 + (0.5" if frame type = Standard)
                 *                - (0.25" if frame type = Floater) + (0.5" if liner is selected)
                 */
                if (!$glassHeight || empty($glassHeight)) {
                    $glassHeight = $outerHeight - ($frameWidth * self::MULTIPLIER_LENGTH) -
                        ($linerWidth * self::MULTIPLIER_LENGTH) + $standardFrameType - $floaterFrameType + $liner;
                }
            }

            // Return the glass width and height.
            return [
                'width' => $glassWidth,
                'height' => $glassHeight
            ];
        }catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * Method used to calculate the item size and width.
     *
     * @param $artProductId
     * @param $configJson
     * @param null $frameSku
     * @param null $linerSku
     * @return array
     */
    public function getItemSize($artProductId, $configJson, $frameSku = null, $linerSku = null)
    {
        try {
            // if configJson already contains item_width and item_height pass default values.
            if (isset($configJson['item_width']) && !empty($configJson['item_width']) &&
                isset($configJson['item_height']) && !empty($configJson['item_height'])) {
                // Return the item width and height.
                return [
                    'width' => $configJson['item_width'],
                    'height' => $configJson['item_height']
                ];
            }

            // Check, if the art-product is not available then load it by passed art-product-id.
            if (!$this->artProduct) {
                $this->_getArtProduct($artProductId);
            }

            // Set the default value for item width and height.
            $itemWidth = $itemHeight = 0;

            if ($this->artProduct) {
                $artProduct = $this->artProduct;

                // Set the values to var.
                $glassWidth = (float)$artProduct->getGlassWidth();
                $glassHeight = (float)$artProduct->getGlassHeight();
                $frameWidth = (float)$artProduct->getFrameWidth();

                $standardFrameType = $floaterFrameType = $liner = $linerWidth = 0;

                // Check for the frame-sku
                if (!$frameSku || empty($frameSku)) {
                    $frameSku = $artProduct->getFrameDefaultSku();
                }
                if (!empty($frameSku)) {
                    if (!$this->frameProduct) {
                        $this->frameProduct = $this->_getProductBySku($frameSku);
                    }
                    if ($this->frameProduct) {
                        $frameType = $this->frameProduct->getFrameType();
                        if (!empty($frameType) && self::FRAME_TYPE_STANDARD == strtolower($frameType)) {
                            $standardFrameType = self::ADDITIONAL_LENGTH;
                        }
                        if (!empty($frameType) && self::FRAME_TYPE_FLOATER == strtolower($frameType)) {
                            $floaterFrameType = self::FLOATER_LENGTH;
                        }
                    } else {
                        $this->logger->error(__('Unable to load frame product #%1.', $frameSku));
                    }
                }

                // Check for the liner-sku
                if (!$linerSku || empty($linerSku)) {
                    $linerSku = $artProduct->getLinerSku();
                }
                if (!empty($linerSku)) {
                    if (!$this->linerProduct) {
                        $this->linerProduct = $this->_getProductBySku($linerSku);
                    }
                    if ($this->linerProduct) {
                        $linerWidth = $this->linerProduct->getFrameWidth();
                        $frameType = $this->linerProduct->getFrameType();
                        if (!empty($frameType) && self::FRAME_TYPE_LINER == strtolower($frameType)) {
                            $liner = self::ADDITIONAL_LENGTH;
                        }
                    } else {
                        $this->logger->error(__('Unable to load liner product #%1.', $linerSku));
                    }
                }

                /**
                 * Outer Width = Glass Width + FrameWidth*2 - Liner Width*2 + (0.5" if frame type = Standard)
                 *               - (0.25" if frame type = Floater) + (0.5" if liner is selected)
                 */
                $itemWidth = $glassWidth + ($frameWidth * self::MULTIPLIER_LENGTH) -
                    ($linerWidth * self::MULTIPLIER_LENGTH) + $standardFrameType - $floaterFrameType + $liner;

                /**
                 * Outer Height = Glass Height + FrameWidth*2 - Liner Width*2 + (0.5" if frame type = Standard)
                 *               - (0.25" if frame type = Floater) + (0.5" if liner is selected)
                 */
                $itemHeight = $glassHeight + ($frameWidth * self::MULTIPLIER_LENGTH) -
                    ($linerWidth * self::MULTIPLIER_LENGTH) + $standardFrameType - $floaterFrameType + $liner;
            }

            // Return the glass width and height.
            return [
                'width' => $itemWidth,
                'height' => $itemHeight
            ];
        }catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * Method used to get the art product by passed id.
     *
     * @param $artProductId
     */
    private function _getArtProduct($artProductId)
    {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $artProduct */
            $this->artProduct = $this->productRepository->getById($artProductId);
        } catch (\Exception $e) {
            $this->logger->error(__('Unable to load artwork product #%1.', $artProductId));
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Method used to get the product by sku.
     *
     * @param $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function _getProductBySku($sku)
    {
        $product = null;

        try {
            $product = $this->productRepository->get($sku);
        } catch (\Exception $e) {
            $this->logger->error(__('Unable to load product #%1.', $sku));
            $this->logger->error($e->getMessage());
        }
        return $product;
    }
}
