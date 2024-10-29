<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\Catalog\Model\ResourceModel\Product\Indexer\Eav;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Source as ParentSource;

/**
 * Class Source
 * @package Perficient\Catalog\Model\ResourceModel\Product\Indexer\Eav
 */
class Source extends ParentSource
{
    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * Source constructor.
     * @param null $connectionName
     * @param \Magento\Eav\Api\AttributeRepositoryInterface|null $attributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder|null $criteriaBuilder
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context  $context,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        \Magento\Eav\Model\Config                          $eavConfig,
        \Magento\Framework\Event\ManagerInterface          $eventManager,
        \Magento\Catalog\Model\ResourceModel\Helper        $resourceHelper,
                                                           $connectionName = null,
        \Magento\Eav\Api\AttributeRepositoryInterface      $attributeRepository = null,
        \Magento\Framework\Api\SearchCriteriaBuilder       $criteriaBuilder = null
    )
    {
        parent::__construct(
            $context,
            $tableStrategy,
            $eavConfig,
            $eventManager,
            $resourceHelper
        );
        $this->_resourceHelper = $resourceHelper;
        $this->attributeRepository = $attributeRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Eav\Api\AttributeRepositoryInterface::class);
        $this->criteriaBuilder = $criteriaBuilder
            ?: \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
    }

    /**
     * Prepare data index for indexable multiply select attributes
     *
     * @param array $entityIds the entity ids limitation
     * @param int $attributeId the attribute id limitation
     * @return $this
     */
    protected function _prepareMultiselectIndex($entityIds = null, $attributeId = null)
    {
        $connection = $this->getConnection();
// prepare multiselect attributes
        $attrIds = $attributeId === null ? $this->_getIndexableAttributes(true) : [$attributeId];
        if (!$attrIds) {
            return $this;
        }
        $productIdField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();
// load attribute options
        $options = [];
        $select = $connection->select()->from(
            $this->getTable('eav_attribute_option'),
            ['attribute_id', 'option_id']
        )->where('attribute_id IN(?)', $attrIds);
        $query = $select->query();
        while ($row = $query->fetch()) {
            $options[$row['attribute_id']][$row['option_id']] = true;
        }
// Retrieve any custom source model options
        $sourceModelOptions = $this->getMultiSelectAttributeWithSourceModels($attrIds);
        $options = array_replace_recursive($options, $sourceModelOptions);
// prepare get multiselect values query
        $productValueExpression = $connection->getCheckSql('pvs.value_id > 0', 'pvs.value', 'pvd.value');
        $select = $connection->select()->from(
            ['pvd' => $this->getTable('catalog_product_entity_varchar')],
            []
        )->join(
            ['cs' => $this->getTable('store')],
            '',
            []
        )->joinLeft(
            ['pvs' => $this->getTable('catalog_product_entity_varchar')],
            "pvs.{$productIdField} = pvd.{$productIdField} AND pvs.attribute_id = pvd.attribute_id"
            . ' AND pvs.store_id=cs.store_id',
            []
        )->joinLeft(
            ['cpe' => $this->getTable('catalog_product_entity')],
            "cpe.{$productIdField} = pvd.{$productIdField}",
            []
        )->where(
            'pvd.store_id=?',
            $connection->getIfNullSql('pvs.store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
        )->where(
            'cs.store_id!=?',
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        )->where(
            'pvd.attribute_id IN(?)',
            $attrIds
        )->where(
            'cpe.entity_id IS NOT NULL'
        )->columns(
            [
                'entity_id' => 'cpe.entity_id',
                'attribute_id' => 'attribute_id',
                'store_id' => 'cs.store_id',
                'value' => $productValueExpression,
                'source_id' => 'cpe.entity_id',
            ]
        );
        $statusCond = $connection->quoteInto('=?', ProductStatus::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', "pvd.{$productIdField}", 'cs.store_id', $statusCond);
        if ($entityIds !== null) {
            $select->where('cpe.entity_id IN(?)', $entityIds);
        }
        /**
         * Add additional external limitation
         */
        $this->_eventManager->dispatch(
            'prepare_catalog_product_index_select',
            [
                'select' => $select,
                'entity_field' => new \Zend_Db_Expr('cpe.entity_id'),
                'website_field' => new \Zend_Db_Expr('cs.website_id'),
                'store_field' => new \Zend_Db_Expr('cs.store_id'),
            ]
        );
        $this->saveDataFromSelect($select, $options);
        return $this;
    }

    /**
     * Get options for multiselect attributes using custom source models
     * Based on @maderlock's fix from:
     * https://github.com/magento/magento2/issues/417#issuecomment-265146285
     *
     * @param array $attrIds
     *
     * @return array
     */
    private function getMultiSelectAttributeWithSourceModels($attrIds)
    {
// Add options from custom source models
        $this->criteriaBuilder
            ->addFilter('attribute_id', $attrIds, 'in')
            ->addFilter('source_model', true, 'notnull');
        $criteria = $this->criteriaBuilder->create();
        $attributes = $this->attributeRepository->getList(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $criteria
        )->getItems();
        $options = [];
        foreach ($attributes as $attribute) {
            $sourceModelOptions = $attribute->getOptions();
// Add options to list used below
            foreach ($sourceModelOptions as $option) {
                $options[$attribute->getAttributeId()][$option->getValue()] = true;
            }
        }
        return $options;
    }

    /**
     * Save data from select
     *
     * @return void
     */
    private function saveDataFromSelect(\Magento\Framework\DB\Select $select, array $options)
    {
        $i = 0;
        $data = [];
        $query = $select->query();
        while ($row = $query->fetch()) {
            $values = explode(',', (string)$row['value']);
            $values = array_unique($values, SORT_REGULAR);
            foreach ($values as $valueId) {
                if (isset($options[$row['attribute_id']][$valueId])) {
                    $data[] = [$row['entity_id'], $row['attribute_id'], $row['store_id'], $valueId, $row['source_id']];
                    $i++;
                    if ($i % 10000 == 0) {
                        $this->_saveIndexData($data);
                        $data = [];
                    }
                }
            }
        }
        $this->_saveIndexData($data);
    }
}
