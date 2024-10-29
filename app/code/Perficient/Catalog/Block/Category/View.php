<?php
/**
 * Render Category View
 * @category: Magento
 * @package: Perficient/Perficient_Catalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Dominic Henry<dominic.henry@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Block\Category;

use Magento\Catalog\Block\Category\View as BaseCatalogCategoryView;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Driver\File;

class View extends BaseCatalogCategoryView
{
    /**
     * @const string
     */
    const CATALOG_PRODUCT_SUB_DIR_PATH = 'catalog/product/placeholder/';

    /**
     * View constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param Category $categoryHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ImageFactory $imageFactory
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context                                $context,
        Resolver                               $layerResolver,
        Registry                               $registry,
        Category                               $categoryHelper,
        private readonly ScopeConfigInterface  $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
        private readonly ImageFactory          $imageFactory,
        private readonly AssetRepository       $assetRepos,
        private readonly DirectoryList         $directoryList,
        private readonly File                  $fileDriver,
        array                                  $data = []
    ) {
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }

    /**
     * @param $_category
     * @return string
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function getCategoryImageUrl($_category)
    {
        $imagePlaceHolder = $this->getPlaceholderImage();
        $pubFolderPath = $this->directoryList->getPath('pub');
        $catImageURL = $this->getImage()->getUrl($_category);
        $parsedImageURL = \Laminas\Uri\UriFactory::factory($catImageURL);
        $catImageURL = !empty($catImageURL) && $this->fileDriver->isExists($pubFolderPath . $parsedImageURL->getPath()) ?
            $catImageURL : $imagePlaceHolder;

        return $catImageURL;
    }

    /**
     * Show Category Placeholder image
     * @return string
     * @throws NoSuchEntityException
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
            $imagePlaceholder = $this->imageFactory->create();
            $url = $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('image'));
        }
        return $url;
    }
}
