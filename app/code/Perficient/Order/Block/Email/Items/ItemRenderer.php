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

namespace Perficient\Order\Block\Email\Items;

use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder;
use Perficient\Order\Block\Product\CustomImageBuilder;
use Perficient\Order\Block\Item\CustomizedProductOption;

/**
 * Class ItemRenderer
 * @package Perficient\Order\Block\Email\Items
 */
class ItemRenderer extends DefaultOrder {
    /**
     * ItemRenderer constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        protected CustomImageBuilder $customImageBuilder,
        protected CustomizedProductOption $customizedProductOption,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param $item
     * @param $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getProductImage($item, $imageId, $attributes = []) {
        $product = $this->getProductData();

        return $this->customImageBuilder
            ->setProduct($product)
            ->setItem($item)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->customImageCreate();
    }

    /**
     * Identify the product from which thumbnail should be taken.
     *
     * @return \Magento\Catalog\Model\Product
     * @codeCoverageIgnore
     */
    public function getProductData() {
        return $this->getItem()->getProduct();
    }

    /**
     * @return array|mixed
     */
    public function getCustomizeOptions() {
        $item = $this->getItem();
        $options = $item->getProductOptions();
        return  $this->customizedProductOption->getOptions($options);
    }
}
