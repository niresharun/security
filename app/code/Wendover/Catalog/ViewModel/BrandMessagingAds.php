<?php

namespace Wendover\Catalog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Cms\Block\Block as cmsBlock;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\CategoryFactory;


class BrandMessagingAds implements ArgumentInterface
{
    public function __construct(
        protected cmsBlock $cmsblock,
        protected RequestInterface $request,
        protected CategoryFactory $categoryFactory
    ) {
    }

    public function getCategoryData()
    {
        $categoryId = (int)$this->request->getParam('id', false);
        $categoryCollection = [];
        if ($categoryId) {
            $categoryCollection = $this->categoryFactory->create()->load($categoryId);
        }
        return $categoryCollection;
    }

    public function canDisplayBrand($count, $categoryData, $_productCollection)
    {
        if (
            ($categoryData->getId() && $categoryData->getData('brand_messaging_ad')) &&
            (!$this->isFirstPage() && $count == 6) ||
            ($_productCollection->count() == $count && $count <= 6 && !$this->isFirstPage())
        ) {
            return true;
        }
        return false;
    }

    public function getCmsBlock($category)
    {
        if ($category->getId()) {
            $cmsblock = $this->cmsblock->setBlockId($category->getData('brand_messaging_ad'))->toHtml();
            return $cmsblock;
        }
    }

    public function isFirstPage()
    {
        return $this->request->getParam('p') > 1;
    }
}
