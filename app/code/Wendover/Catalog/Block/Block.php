<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Wendover\Catalog\Block;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Block\Product\View;


/**
 * Cms block content block
 * @deprecated This class introduces caching issues and should no longer be used
 * @see BlockByIdentifier
 */
class Block extends \Magento\Cms\Block\Block
{
    /**
     * Prefix for cache key of CMS block
     */
    const CACHE_KEY_PREFIX = 'CMS_BLOCK_';

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Block factory
     *
     * @var BlockFactory
     */
    protected $_blockFactory;

    /**
     * Construct
     *
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        Context $context,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory,
        View $product,
        array $data = []
    ) {
        parent::__construct($context, $filterProvider,$storeManager,$blockFactory,$data);
        $this->product = $product;
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $blockId = $this->getBlockId();
        $html = '';

        if ($blockId) {
            $currentProduct = $this->product->getProduct();

            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);
            if ($block->isActive() && $currentProduct->getData($this->getProductAttribute())) {
                $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
            }
        }
        return $html;
    }
}
