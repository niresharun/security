<?php

declare(strict_types=1);

namespace Wendover\Catalog\Plugin\Data;

use Magento\Framework\Data\Collection as DataCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Plugin to return last page if current page greater then collection size.
 */
class Collection
{
    public function __construct(
        private readonly CollectionFactory $productCollectionFactory,
        private readonly RequestInterface $request,
    )
    {
    }
    /**
     * Return last page if current page greater then last page.
     *
     * @param DataCollection $subject
     * @param int $result
     * @return int
     */
    public function aftergetLastPageNumber(DataCollection $subject, int $result): int
    {
        $params = $this->request->getParams();
        if (!empty($params) && !empty($params['searchSku'])) {
            $pagePerItems = isset($params['product_list_limit']) ? $params['product_list_limit'] : 12;
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('sku', array('like' => '%'.$params['searchSku'].'%'));
            $collection->addCategoriesFilter(['in' => $params['id']]);
            $collectionSize = $collection->count();
    
            if (0 === $collectionSize) {
                return 1;
            } elseif ($pagePerItems) {
                return (int)ceil($collectionSize / $pagePerItems);
            }
        }
        
        return $result;
    }
}
