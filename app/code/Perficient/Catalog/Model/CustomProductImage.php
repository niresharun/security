<?php
/**
 * Custom Product Image
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain <hiral.jain@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Perficient\Catalog\Api\Data\CustomProductImageInterface;
use Perficient\Catalog\Model\ResourceModel\CustomProductImage as CustomProductImageResource;
use Perficient\Catalog\Api\Data\CustomProductImageInterfaceFactory;

/**
 * Class CustomProductImage
 * @package Perficient\Catalog\Model
 */
class CustomProductImage extends AbstractModel implements CustomProductImageInterface
{
    /**
     * CustomProductImage constructor.
     * @param Context $context
     * @param Registry $registry
     * @param CustomProductImageInterfaceFactory $customProductImageInterfaceFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context                                             $context,
        Registry                                            $registry,
        private readonly CustomProductImageResource         $customProductImageResource,
        private readonly CustomProductImageRepository       $customProductImageRepository,
        private readonly CustomProductImageInterfaceFactory $customProductImageInterfaceFactory,
        AbstractResource                                    $resource = null,
        AbstractDb                                          $resourceCollection = null,
        array                                               $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    public function getCustomProductImageId()
    {
        return $this->getData('custom_product_image_id');
    }

    /**
     * @inheritdoc
     */
    public function setCustomProductImageId($value)
    {
        return $this->setData('custom_product_image_id', $value);
    }

    /**
     * @inheritdoc
     */
    public function getCustomProductImageSku()
    {
        return $this->getData('sku');
    }

    /**
     * @inheritdoc
     */
    public function setCustomProductImageSku($value)
    {
        return $this->setData('sku', $value);
    }

    /**
     * @inheritdoc
     */
    public function getCustomProductImageImage()
    {
        return $this->getData('image');
    }

    /**
     * @inheritdoc
     */
    public function setCustomProductImageImage($value)
    {
        return $this->setData('image', $value);
    }

    /**
     * @inheritdoc
     */
    public function getCustomProductImageType()
    {
        return $this->getData('type');
    }

    /**
     * @inheritdoc
     */
    public function setCustomProductImageType($value)
    {
        return $this->setData('type', $value);
    }

    /**
     * resource model
     */
    protected function _construct()
    {
        $this->_init(\Perficient\Catalog\Model\ResourceModel\CustomProductImage::class);
    }

    /**
     * @param $data
     */
    public function saveAll($data)
    {
        foreach ($data as $imageData) {
            $customImageId = $this->customProductImageResource->getIdBySkuAndType($imageData);
            if ($customImageId) {
                $customImage = $this->customProductImageRepository->getById($customImageId);
                $customImage->setCustomProductImageSku($imageData['sku'])
                    ->setCustomProductImageImage($imageData['image'])
                    ->setCustomProductImageType($imageData['type']);
                $customImage->save();
            } else {
                $imageModel = $this->customProductImageInterfaceFactory->create();
                $imageModel->setCustomProductImageSku($imageData['sku'])
                    ->setCustomProductImageImage($imageData['image'])
                    ->setCustomProductImageType($imageData['type']);
                $imageModel->save();
            }
        }
    }

}
