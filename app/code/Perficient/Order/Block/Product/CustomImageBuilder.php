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

use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Model\Product;
use Perficient\Order\Block\Product\CustomImageFactory as PerficientImageFactory;

/**
 * @deprecated 103.0.0
 * @see ImageFactory
 */
class CustomImageBuilder extends ImageBuilder {
    /**
     * @var PerficientImageFactory
     */
    protected $customImageFactory;
    /**
     * @var HelperFactory
     */
    protected $helperFactory;
    /**
     * @var Product
     */
    protected $product;
    /**
     * @var string
     */
    protected $imageId;
    /**
     * @var
     */
    protected $item;
    /**
     * @var array
     */
    protected $attributes = [];
    public function __construct(
        HelperFactory $helperFactory,
        PerficientImageFactory $customImageFactory
    ) {
        parent::__construct($helperFactory, $customImageFactory);
        $this->customImageFactory = $customImageFactory;
    }

    /**
     * @param Product|null $product
     * @param null $item
     * @param string|null $imageId
     * @param array|null $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function customImageCreate(Product $product = null, $item = null, string $imageId = null, array $attributes = null) {
        $product ??= $this->product;
        $imageId ??= $this->imageId;
        $attributes ??= $this->attributes;
        $item ??= $this->item;
        return $this->customImageFactory->createImage($product, $item, $imageId, $attributes);
    }

    /**
     * @param $item
     * @return $this
     */
    public function setItem($item) {
        $this->item = $item;
        return $this;
    }
}
