<?php
namespace Wendover\Catalog\Block;

use Magento\Catalog\Block\Category\View as ViewCategory;
use Magento\Cms\Block\Block;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Category;


class View extends ViewCategory
{
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        Category $categoryHelper,
        Block $cmsblock,
        array $data = []
    ) {
        $this->cmsblock = $cmsblock;
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }

    public function getCmsBlock($identifier)
    {
        $currentCategory = $this->getCurrentCategory();

        if ($currentCategory) {
            $cmsblock = $this->cmsblock->setBlockId($currentCategory->getData($identifier))->toHtml();
            return $cmsblock;
        }
    }
}
