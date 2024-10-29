<?php
/**
 * Add New field in catalog product
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */

namespace Perficient\Catalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Eav\Model\AttributeProvider;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class ProductLockAttributes
 * @package Perficient\Catalog\Observer
 */
class ProductLockAttributes implements ObserverInterface
{
    /**
     * M1 Source Identifier
     */
    const M1_SOURCE_IDENTIFIER = 'M1';

    /**
     * @param AttributeProvider $attributeProvider
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        private readonly AttributeProvider            $attributeProvider,
        private readonly AttributeRepositoryInterface $attributeRepository,
        private readonly SearchCriteriaBuilder        $searchCriteriaBuilder
    )
    {
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $product = $event->getProduct();

        $m1Attributes = $this->getProductM1Attributes();
        foreach ($m1Attributes as $attributeCode) {
            $product->lockAttribute($attributeCode);
        }
    }

    /**
     * get attributes for which source identifier is M1
     * @return array
     */
    private function getProductM1Attributes()
    {
        $searchResult = $this->attributeRepository->getList(
            'catalog_product',
            $this->searchCriteriaBuilder
                ->addFilter('attribute_set_id', null, 'neq')
                ->addFilter('source_identifier', self::M1_SOURCE_IDENTIFIER)
                ->create()
        );

        $attributes = [];
        foreach ($searchResult->getItems() as $attribute) {
            $attributes[] = $attribute->getAttributeCode();
        }

        return $attributes;
    }
}
