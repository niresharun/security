<?php
/**
 * Rabbitmq product create update
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Tax\Api\Data\TaxClassKeyInterface;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollection;
use Magento\Catalog\Model\ProductFactory;

class Product extends AbstractModel
{
    const DELIMITER_CATEGORY = '/';

    /**
     * Categories id to object cache.
     *
     */
    protected array $categoriesCache = [];

    /**
     * Product constructor.
     * @param CollectionFactory $attributeSetCollection
     * @param Visibility $visibility
     * @param TaxClassManagementInterface $taxClassManagementInterface
     * @param ProductFactory $productFactory
     */
    public function __construct(
        protected CollectionFactory $attributeSetCollection,
        private readonly Visibility $visibility,
        private readonly TaxClassManagementInterface $taxClassManagementInterface,
        private readonly \Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory $taxClassKeyDataObjectFactory,
        private readonly CategoryCollection $categoryColFactory,
        private readonly ProductFactory $productFactory
    ) {
        $this->initCategories();
    }

    /**
     *
     * @param string $attributeSetName
     * @return int attributeSetId
     */
    public function getAttributeSetId($attributeSetName)
    {
        $attributeSetCollection = $this->attributeSetCollection->create()
            ->addFieldToSelect('attribute_set_id')
            ->addFieldToFilter('attribute_set_name', $attributeSetName)
            ->addFieldToFilter('entity_type_id', 4)
            ->getFirstItem()
            ->toArray();

        if (empty($attributeSetCollection['attribute_set_id'])) {
            return -1;
        }

        $attributeSetId = (int) $attributeSetCollection['attribute_set_id'];
        // OR (see benchmark below for make your choice)
        $attributeSetId = (int) implode($attributeSetCollection);

        return $attributeSetId;
    }

    /**
     * @param $name
     * @return null
     */
    public function getVisibilityIdByName($name)
    {
        $visibilityArray = $this->visibility->getOptionArray();
        $searchTerm = ucwords((string) $name);
        $key = array_keys($visibilityArray, $searchTerm);

        if (!empty($key)) {
            return $key[0];
        } else {
            return null;
        }

    }

    /**
     * @param $clasName
     * @return int|null
     */
    public function getTaxClassId($clasName){
        $taxClassId = $this->taxClassManagementInterface->getTaxClassId(
            $this->taxClassKeyDataObjectFactory->create()
                ->setType(TaxClassKeyInterface::TYPE_NAME)
                ->setValue($clasName)
        );
        return $taxClassId;
    }

    /**
     * @param $categoryPath
     * @return null
     */
    public function getCategoryIdFromName($categoryPath) {
        /** @var string $index */
        $index = $this->standardizeString($categoryPath);
        if (isset($this->categories[$index])) {
            $categoryId = $this->categories[$index];
            return $categoryId;
        }
        return null;
    }

    /**
     * @param $string
     * @return string
     */
    private function standardizeString($string)
    {
        return mb_strtolower((string) $string);
    }

    /**
     * @param $string
     * @return mixed
     */
    private function quoteDelimiter($string)
    {
        return str_replace(self::DELIMITER_CATEGORY, '\\' . self::DELIMITER_CATEGORY, (string) $string);
    }

    /**
     * Initialize categories
     *
     * @return $this
     */
    protected function initCategories()
    {
        if (empty($this->categories)) {
            $collection = $this->categoryColFactory->create();
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('url_path');
            $collection->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            foreach ($collection as $category) {
                $structure = explode(self::DELIMITER_CATEGORY, (string) $category->getPath());
                $pathSize = count($structure);
                $this->categoriesCache[$category->getId()] = $category;
                if ($pathSize > 1) {
                    $path = [];
                    for ($i = 1; $i < $pathSize; $i++) {
                        $name = $collection->getItemById((int)$structure[$i])->getName();
                        $path[] = $this->quoteDelimiter($name);
                    }
                    /** @var string $index */
                    $index = $this->standardizeString(
                        implode(self::DELIMITER_CATEGORY, $path)
                    );
                    $this->categories[$index] = $category->getId();
                }
            }
        }
        return $this;
    }

    /* Get Option id by Option Label */
    public function getOptionIdByLabel($attributeCode, $optionLabel)
    {
        $product = $this->productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            if(is_array($optionLabel)) {
                $optionIds = [];
                foreach ($optionLabel as $label) {
                    $optionIds[] = $isAttributeExist->getSource()->getOptionId($label);
                }
                $optionId = implode(",", $optionIds);
            } else {
                $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
            }

        } else {
            $optionId = $optionLabel;
        }
        return $optionId;
    }


}