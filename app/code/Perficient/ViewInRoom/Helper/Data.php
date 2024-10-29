<?php
/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Tahir Aziz <tahir.aziz@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_ViewInRoom
 */

declare(strict_types=1);

namespace Perficient\ViewInRoom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use \Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Perficient\ViewInRoom\Helper
 */
class Data extends AbstractHelper
{
    const XML_LIFESTYLE_PATH = 'productimize/view_setting/lifestyle_path';

    public function __construct(
        Context                                      $context,
        protected SerializerInterface                $serializer,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly SessionFactory              $customerSession
    )
    {
        parent::__construct($context);
    }

    public function getConfig(Product $product) {
        $lifestylePath = $this->scopeConfig->getValue(
            self::XML_LIFESTYLE_PATH,
            ScopeInterface::SCOPE_STORE
        );
        $categoryCollection = $product->getCategoryCollection();
        $mediaUrl = $product->getStore()->getUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productBaseImage = $mediaUrl . '/catalog/product/' . $product->getImage();
        $data = [];
        $flag = false;
        $counter = 1;
        $totalAssociatedCategory = $categoryCollection->getSize();
        foreach ($categoryCollection as $category) {
            $category = $this->categoryRepository->get($category->getId());
            if ($category->getId()) {
                $category = $this->getVirCategory($category);
            }
            /**
             * Fixes to avoid error while exploding the data.
             */
            $virCenterOffset = $category->getData('vir_center_offset');
            if (!$virCenterOffset) {
                continue;
            }
            if (isset($lifestylePath) && !empty($lifestylePath) &&
                str_contains((string)$category->getPath(), (string)$lifestylePath)) {
                $flag = true;
                $data = $this->getCategoryData($product, $virCenterOffset, $category, $productBaseImage);
            }
            if ($counter == $totalAssociatedCategory && !$flag) {
                $data = $this->getCategoryData($product, $virCenterOffset, $category, $productBaseImage);
            }
            $counter++;
        }
        return $data;
    }

    public function getJsonConfig(Product $product)
    {
        $data = $this->getConfig($product);
        if (empty($data)) {
            return false;
        }
        return $this->serializer->serialize($data);
    }

    /* check if customizable configurator product*/
    public function isProductCustomizer($product)
    {
        $layoutType = $product->getData("product_customizer");
        if ($layoutType == 1) {
            return true;
        }
        return false;
    }

    public function isLoggedInCustomer()
    {
        $session = $this->customerSession->create();
        return $session->isLoggedIn();
    }

    public function isValidData($options)
    {
        foreach ($options as $option) {
            if (empty($option)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getVirCategory($category)
    {
        if (!$category->getData('vir_background_img') || $category->getData('vir_background_img') == '') {
            $parentId = $category->getParentId();
            if ($parentId) {
                $parentCategory = $this->categoryRepository->get($parentId);
                $category = $this->getVirCategory($parentCategory);
            }
        }
        return $category;
    }

    /**
     * @param Product $product
     * @param $virCenterOffset
     * @param $category
     * @param $productBaseImage
     */
    protected function getCategoryData(Product $product, $virCenterOffset, $category, $productBaseImage): array
    {
        $centreOffset = explode(',', (string)$virCenterOffset);
        $data = [];
        if ($category && !empty($centreOffset[0] && !empty($centreOffset[1]))) {
            $cat = [];
            $cat['id'] = $category->getId();
            $cat['vir_wall_width'] = $category->getData('vir_wall_width');
            $cat['vir_wall_height'] = $category->getData('vir_wall_height');
            $cat['vir_center_offset_width'] = $centreOffset[0];
            $cat['vir_center_offset_height'] = $centreOffset[1];
            $cat['vir_background_img'] = $category->getData('vir_background_img');
            $cat['item_width'] = $product->getData('item_width');
            $cat['item_height'] = $product->getData('item_height');
            $cat['item_image'] = $productBaseImage;
            if (!empty($cat)) {
                if ($this->isValidData($cat)) {
                    $data[] = $cat;
                }
            }
        }
        return $data;
    }
}
