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

namespace Perficient\Order\Block\Item;

use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Perficient\Order\Block\Product\CustomImageBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;

/**
 * Class Renderer
 * @package Perficient\Order\Block\Item
 */
class Renderer extends DefaultRenderer {
    /**
     * @var
     */
    protected $string;

    /**
     * @var
     */
    protected $_productOptionFactory;
    /**
     * @var mixed
     */
    protected $itemResolver;

    /**
     * Renderer constructor.
     * @param Context $context
     * @param StringUtils $string
     * @param OptionFactory $productOptionFactory
     * @param ItemResolverInterface|null $itemResolver
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        OptionFactory $productOptionFactory,
        protected CustomImageBuilder $customImageBuilder,
        protected CustomizedProductOption $customizedProductOption,
        ItemResolverInterface $itemResolver = null,
        array $data = []
    ) {
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);
        parent::__construct($context, $string, $productOptionFactory, $data);
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

    /**
     * @return array|mixed
     */
    public function getCustomizeOptionsForInvoice() {
        $item = $this->getOrderItem();
        $options = $item->getProductOptions();
        return  $this->customizedProductOption->getOptions($options);
    }
}
