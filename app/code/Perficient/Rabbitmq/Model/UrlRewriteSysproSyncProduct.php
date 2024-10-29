<?php
/**
 * Rabbitmq product url rewrite creation for syspro sync
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\App\ResourceConnection;
use OlegKoval\RegenerateUrlRewrites\Helper\Regenerate as RegenerateHelper;
use OlegKoval\RegenerateUrlRewrites\Model\AbstractRegenerateRewrites;
use OlegKoval\RegenerateUrlRewrites\Model\RegenerateProductRewrites;
use Magento\Catalog\Model\ResourceModel\Product\ActionFactory as ProductActionFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGeneratorFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGeneratorFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class UrlRewriteSysproSyncProduct
 * @package Perficient\Rabbitmq\Model
 */
class UrlRewriteSysproSyncProduct extends RegenerateProductRewrites
{
    /**
     * UrlRewriteSysproSyncProduct constructor.
     * @param ResourceConnection $resourceConnection
     * @param ProductUrlRewriteGeneratorFactory $productUrlRewriteGenerator
     * @param ProductUrlPathGeneratorFactory $productUrlPathGenerator
     */
    public function __construct(
        RegenerateHelper $helper,
        ResourceConnection $resourceConnection,
        ProductActionFactory $productActionFactory,
        ProductUrlRewriteGeneratorFactory\Proxy $productUrlRewriteGenerator,
        ProductUrlPathGeneratorFactory\Proxy $productUrlPathGenerator,
        ProductCollectionFactory $productCollectionFactory,
        private readonly File $fileManager
    ) {
        parent::__construct($helper, $resourceConnection, $productActionFactory, $productUrlRewriteGenerator, $productUrlPathGenerator, $productCollectionFactory);
    }

    /**
     * @param $productId
     * @param int $storeId
     * @return $this
     */
    public function regenerateSpecificSysproProductUrlRewrites($productId, $storeId = 0)
    {
        $this->regenerateSysproProductsRangeUrlRewrites([$productId], $storeId);
        return $this;
    }

    /**
     * @param array $productsFilter
     * @param int $storeId
     * @return $this
     */
    public function regenerateSysproProductsRangeUrlRewrites($productsFilter = [], $storeId = 0)
    {
        $products = $this->_getProductsCollection($productsFilter, $storeId);
        $pageCount = $products->getLastPageNumber();
        $this->progressBarProgress = 1;
        $this->progressBarTotal = (int)$products->getSize();
        $currentPage = 1;

        while ($currentPage <= $pageCount) {
            $products->clear();
            $products->setCurPage($currentPage);

            foreach ($products as $product) {
                $this->_showProgress();
                $this->processSysproProduct($product, $storeId);
            }

            $currentPage++;
        }

        $this->_updateSecondaryTable();

        return $this;
    }

    /**
     * @param $entity
     * @param int $storeId
     * @return $this
     * @throws \Exception
     */
    public function processSysproProduct($entity, $storeId = 0)
    {
        $entity->setStoreId($storeId)->setData('url_path', null);

        if ($this->regenerateOptions['saveOldUrls']) {
            $entity->setData('save_rewrites_history', true);
        }

        // reset url_path to null, we need this to set a flag to use a Url Rewrites:
        // see logic in core Product Url model: \Magento\Catalog\Model\Product\Url::getUrl()
        // if "request_path" is not null or equal to "false" then Magento do not serach and do not use Url Rewrites
        $updateAttributes = ['url_path' => null];
        $updateAttributes['url_key'] = $entity->getUrlKey();

        $this->_getProductAction()->updateAttributes(
            [$entity->getId()],
            $updateAttributes,
            $storeId
        );
        $urlRewrites = $this->_getProductUrlRewriteGenerator()->generate($entity);
        $urlRewrites = $this->helper->sanitizeProductUrlRewrites($urlRewrites);
        if (!empty($urlRewrites)) {
            $this->saveUrlSysproProductRewrites(
                $urlRewrites,
                [['entity_type' => $this->entityType, 'entity_id' => $entity->getId(), 'store_id' => $storeId]]
            );
        }

        $this->progressBarProgress++;

        return $this;
    }

    /**
     * @param $urlRewrites
     * @param array $entityData
     * @return $this
     * @throws \Exception
     */
    public function saveUrlSysproProductRewrites($urlRewrites, $entityData = [])
    {
        $data = $this->_prepareProductUrlRewrites($urlRewrites);

        if (!$this->regenerateOptions['saveOldUrls']) {
            if (empty($entityData) && !empty($data)) {
                $entityData = $data;
            }
            $this->_deleteCurrentRewrites($entityData);
        }

        $this->_getResourceConnection()->getConnection()->beginTransaction();
        try {
            $this->_getResourceConnection()->getConnection()->insertMultiple(
                $this->_getMainTableName(),
                $data
            );
            $this->_getResourceConnection()->getConnection()->commit();

        } catch (\Exception $e) {
            $this->_getResourceConnection()->getConnection()->rollBack();
            throw $e;
        }

        return $this;
    }
    /**
     * @param array $urlRewrites
     * @return array
     */
    protected function _prepareProductUrlRewrites($urlRewrites)
    {
        $result = [];
        foreach ($urlRewrites as $urlRewrite) {
            $rewrite = $urlRewrite->toArray();

            // check if same Url Rewrite already exists
            $originalRequestPath = trim((string) $rewrite['request_path']);

            // skip empty Url Rewrites - I don't know how this possible, but it happens in Magento:
            // maybe someone did import product programmatically and product(s) name(s) are empty
            if (empty($originalRequestPath)) continue;

            // split generated Url Rewrite into parts
            $pathParts = $this->fileManager->getPathInfo($originalRequestPath);

            // remove leading/trailing slashes and dots from parts
            $pathParts['dirname'] = trim((string) $pathParts['dirname'], './');
            $pathParts['filename'] = trim((string) $pathParts['filename'], './');

            // If the last symbol was slash - let's use it as url suffix
            $urlSuffix = str_ends_with($originalRequestPath, '/') ? '/' : '';

            // re-set Url Rewrite with sanitized parts
            $rewrite['request_path'] = $this->_mergePartsIntoRewriteRequest($pathParts, '', $urlSuffix);

            // check if we have a duplicate (maybe exists product with same name => same Url Rewrite)
            // if exists then add additional index to avoid a duplicates
            $rewrite['request_path'] = $this->_mergePartsIntoRewriteRequest($pathParts, '', $urlSuffix);
            $result[] = $rewrite;
        }

        return $result;
    }
}
