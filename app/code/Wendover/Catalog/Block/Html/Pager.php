<?php
/**
 * This module is used to send request to Bloomreach and display returning results
 *
 * @category: Magento
 * @package: Perficient/Bloomreach
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Bloomreach
 */
declare(strict_types=1);

namespace Wendover\Catalog\Block\Html;

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Pager as MagentoPager;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class Pager
 * @package Wendover\Catalog\Block\Html
 */
class Pager extends MagentoPager
{
    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * Pager constructor.
     * @param Template\Context $context
     */
    public function __construct(
        Template\Context $context,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Method used to retrieve total number of pages
     *
     * @return int
     */
    public function getTotalNum()
    {
        $params = $this->request->getParams();
        if (!empty($params) && !empty($params['searchSku'])) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('sku', array('like' => '%'.$params['searchSku'].'%'));
            $collection->addCategoriesFilter(['in' => $params['id']]);
            $resultCount = $collection->count();
    
            if (is_object($collection)) {
                return $resultCount;
            }
        }

        return parent::getTotalNum();
    }

    /**
     * Returns data collection
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        $params = $this->request->getParams();
        if (!empty($params) && !empty($params['searchSku'])) {
            $pagePerItems = isset($params['product_list_limit']) ? $params['product_list_limit'] : 12;
            $page = isset($params['p']) ? $params['p'] : 1;
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('sku', array('like' => '%'.$params['searchSku'].'%'));
            $collection->addCategoriesFilter(['in' => $params['id']]);
            $collection->setPageSize($pagePerItems);
            $collection->setCurPage($page);
    
            if (is_object($collection)) {
                return $collection;
            }
        }

        return parent::getCollection();
    }

    /**
     * Method used to get the last page number.
     *
     * @return int
     */
    private function getLastPageNumber()
    {
        $collectionSize = 1;
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('sku', array('like' => '%'.$params['searchSku'].'%'));
        $collection->addCategoriesFilter(['in' => $params['id']]);
        $resultCount = $collection->count();
        if (is_object($collection)) {
            $collectionSize = $resultCount;
        }

        if (0 === $collectionSize) {
            return 1;
        } else {
            return (int)ceil($collectionSize / $this->getLimit());
        }
    }

    /**
     * Return current page
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $currentPage = $this->getRequest()->getParam('p', 1);

        if (isset($currentPage)) {
            return $currentPage;
        }
    }

    /**
     * Retrieve page URL
     *
     * @param string $page
     *
     * @return string
     */
    public function getPageUrl($page)
    {
        return $this->getPagerUrl(
            [
                $this->getPageVarName() => $page > 1 ? $page : 1,
            ]
        );
    }
}
