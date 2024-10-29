<?php
/**
 * Product Alert Observer to check only qty for frame
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar <Amin.Akhtar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Model;

use Magento\ProductAlert\Model\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory as StockCollectionFactory;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\ProductAlert\Model\Mailing\Publisher;

class FrameStockAlertObserver extends Observer
{
    const FRAME_ATTR_SET = 'Frame';

    public function __construct(
        ScopeConfigInterface                             $scopeConfig,
        StoreManagerInterface                            $storeManager,
        CollectionFactory                                $priceColFactory,
        Publisher                                        $publisher,
        StockCollectionFactory                           $stockColFactory,
        private readonly AttributeSetRepositoryInterface $attributeSetRepository
    )
    {
        parent::__construct(
            $scopeConfig,
            $storeManager,
            $priceColFactory,
            $stockColFactory,
            $publisher
        );
    }

    /**
     * Process stock emails
     *
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _processStock(\Magento\ProductAlert\Model\Email $email)
    {
        $email->setType('stock');

        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->_scopeConfig->getValue(
                self::XML_PATH_STOCK_ALLOW,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )
            ) {
                continue;
            }
            try {
                $collection = $this->_stockColFactory->create()->addWebsiteFilter(
                    $website->getId()
                )->addStatusFilter(
                    0
                )->setCustomerOrder();
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
                throw $e;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                $this->setAlertStoreId($alert, $email);
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = $this->customerRepository->getById($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomerData($customer);
                    } else {
                        $customer = $previousCustomer;
                    }

                    $product = $this->productRepository->getById(
                        $alert->getProductId(),
                        false,
                        $website->getDefaultStore()->getId()
                    );

                    $product->setCustomerGroupId($customer->getGroupId());

                    $prodAttributeSet = $this->attributeSetRepository->get($product->getAttributeSetId());
                    $attributeSetName = $prodAttributeSet->getAttributeSetName();
                    $attrSetName = !empty($attributeSetName) ? $attributeSetName : null;
                    if ($attrSetName && $attrSetName == self::FRAME_ATTR_SET) {
                        //If product attribute set is frame than only check qty
                        $frameExtensionAttr = $product->getExtensionAttributes();
                        if ($frameExtensionAttr) {
                            $frameStockData = $frameExtensionAttr->getStockItem();
                            $frameQty = $frameStockData->getQty();
                            if ($frameQty && $frameQty > 0) {
                                $email->addStockProduct($product);

                                $alert->setSendDate($this->_dateFactory->create()->gmtDate());
                                $alert->setSendCount($alert->getSendCount() + 1);
                                $alert->setStatus(1);
                                $alert->save();
                            }
                        }
                    } else {
                        if ($this->productSalability->isSalable($product, $website)) {
                            $email->addStockProduct($product);

                            $alert->setSendDate($this->_dateFactory->create()->gmtDate());
                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }
                    }

                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                    throw $e;
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                } catch (\Exception $e) {
                    $this->_errors[] = $e->getMessage();
                    throw $e;
                }
            }
        }

        return $this;
    }

    /**
     * Set alert store id.
     *
     * @return Observer
     */
    private function setAlertStoreId(\Magento\ProductAlert\Model\Price|\Magento\ProductAlert\Model\Stock $alert, \Magento\ProductAlert\Model\Email $email): Observer
    {
        $alertStoreId = $alert->getStoreId();
        if ($alertStoreId) {
            $email->setStoreId((int)$alertStoreId);
        }

        return $this;
    }
}
