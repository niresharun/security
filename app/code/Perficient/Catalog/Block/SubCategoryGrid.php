<?php
/**
 * Render Sub-categories
 * @category: Magento
 * @package: Perficient/Perficient_Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Block;

use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Perficient\Catalog\Helper\Data;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Class SubCategoryGrid
 * @package Perficient\Catalog\Block
 */
class SubCategoryGrid extends Template
{

    /**
     * @const string
     */
    const CATALOG_PRODUCT_SUB_DIR_PATH = 'catalog/product/placeholder/';

    /**
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ImageFactory $helperImageFactory
     * @param Repository $assetRepos
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context                                $context,
        private readonly CategoryRepository    $categoryRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly ScopeConfigInterface  $scopeConfig,
        private readonly Data                  $helperData,
        private readonly ImageFactory          $helperImageFactory,
        private readonly Repository            $assetRepos,
        private readonly DirectoryList         $directoryList,
        private readonly File                  $fileDriver,
        array                                  $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * Get category data by category ID
     *
     * @param int $catId
     * @return \Magento\Catalog\Api\Data\CategoryInterface $categoryObj
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryDetail($catId)
    {
        $categoryObj = $this->categoryRepository->get(
            $catId,
            $this->storeManager->getStore()->getId()
        );

        return $categoryObj;
    }

    /**
     * Get Subcategories for current category
     *
     * @return array $subcategories
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getChildCategories(): array
    {
        $subcategories = [];
        $categoryObj = $this->getCategoryDetail($this->helperData->getCurrentCategory()->getId());
        $childrenCategories = $categoryObj->getChildrenCategories();

        if (!empty($childrenCategories)) {
            $imagePlaceHolder = $this->getPlaceholderImage();
            /* Get public folder */
            $pubFolderPath = $this->directoryList->getPath('pub');
            foreach ($childrenCategories as $children) {
                $subCategory = $this->getCategoryDetail($children->getId());
                $catImageURL = '';
                if (!empty($subCategory->getImageUrl('thumbnail'))) {
                    $catImageURL = $subCategory->getImageUrl('thumbnail');
                }
                $parsedImageURL = \Laminas\Uri\UriFactory::factory($catImageURL);
                $catImageURL = !empty($catImageURL) && $this->fileDriver->isExists($pubFolderPath . $parsedImageURL->getPath()) ?
                    $catImageURL : $imagePlaceHolder;
                $subcategories[] = [
                    'id' => $subCategory->getId(),
                    'name' => $subCategory->getName(),
                    'url' => $subCategory->getUrl(),
                    'image' => $catImageURL,
                    'description' => $subCategory->getDescription()
                ];
            }
        }

        return $subcategories;
    }

    /**
     * Show Category Placeholder image
     * @return string
     */
    public function getPlaceholderImage()
    {
        $isRelativeUrl = $this->scopeConfig->getValue('catalog/placeholder/image_placeholder');

        if (!empty($isRelativeUrl)) {
            // Default Configurable Image
            $store = $this->storeManager->getStore();
            $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $url = $mediaBaseUrl . self::CATALOG_PRODUCT_SUB_DIR_PATH . ltrim((string)$isRelativeUrl, '/');
        } else {
            // Default Magento Image
            $imagePlaceholder = $this->helperImageFactory->create();
            $url = $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('image'));
        }
        return $url;
    }
}
