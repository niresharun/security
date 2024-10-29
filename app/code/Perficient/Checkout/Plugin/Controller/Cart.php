<?php
/**
 * Checkout Addresses Custom Attribute Installer.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */
declare(strict_types=1);

namespace Perficient\Checkout\Plugin\Controller;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Catalog\Helper\Data;
use Magento\Checkout\Controller\Cart as ParentCart;

class Cart
{
    /**
     * Cart constructor.
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param Data $perficientCatalogHelper
     */
    public function __construct(
        private readonly StoreManagerInterface      $storeManager,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly Data                       $perficientCatalogHelper
    )
    {
    }

    public function beforeExecute(
        ParentCart $subject
    )
    {
        $params = $subject->getRequest()->getParams();
        if (is_array($params) && array_key_exists('pz_cart_properties', $params)) {
            return;
        }
        if (is_array($params) && array_key_exists('product', $params)) {
            $storeId = $this->storeManager->getStore()->getId();
            $addedItemObj = $this->productRepository->getById($subject->getRequest()->getParam('product'),
                false,
                $storeId);
            $defaultConfigurationsValue = $addedItemObj->getData('default_configurations');
            if (!empty($defaultConfigurationsValue)) {
                $defaultConfigurationsValue = $this->perficientCatalogHelper->getDefaultConfigurationJson($defaultConfigurationsValue);
                $subject->getRequest()->setParam('pz_cart_properties', $defaultConfigurationsValue['jsonStr']);
                return;
            }
        }
        return;
    }
}
