<?php
/**
 * This file is used to copy the quote fields into order table.
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Observer;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\App\Area;

/**
 * Class QuoteFieldsToOrder
 * @package Perficient\LeadTime\Observer
 */
class QuoteFieldsToOrder implements ObserverInterface
{
    private array $fields = [
        'syspro_order_id',
        'syspro_customer_id',
        'syspro_salesrep',
        'customer_due_date',
        'has_dummy_product',
        'web_company_id',
        'syspro_salesrep_id',
        'syspro_order_entry_date',
        'requested_delivery_date',
        'expected_ship_date',
        'grand_total',
        'base_grand_total',
        'subtotal'
    ];

    private array $fieldsSyrpoToMagento = [
        'base_shipping_amount',
        'shipping_amount',
        'discount_amount',
        'base_discount_amount',
        'tax_amount',
        'base_tax_amount',
        'syspro_item_id'
    ];
    //WENDM2-2124: Removed above array elements as quote table dont have these fields and it reset these values against order to 0

    /**
     * QuoteFieldsToOrder constructor.
     * @param CompanyManagementInterface $companyRepository
     */
    public function __construct(
        private readonly CompanyManagementInterface $companyRepository,
        private readonly \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        private readonly AppState $appState
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');

        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        $areaCode = $this->appState->getAreaCode();

        /**
         * Set the fields
         */
        foreach ($this->fields as $field) {
            $value = $quote->getData($field);
            if ('syspro_customer_id' == $field && empty($value)) {
                $value = $this->getSysproCustomerId($quote->getCustomer());
                $quote->setData($field, $value)->save();
            }
            $order->setData($field, $value);
        }

        if ($areaCode != Area::AREA_WEBAPI_REST) {
            foreach ($this->fieldsSyrpoToMagento as $field) {
                $value = $quote->getData($field);
                $order->setData($field, $value);
            }
        }

        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getQuoteItemId()) {
                $quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId());
                if ($quoteItem) {
                    $orderItem->setDiscountAmount($quoteItem->getDiscountAmount());
                    $orderItem->setBaseDiscountAmount($quoteItem->getBaseDiscountAmount());
                    $orderItem->setTaxAmount($quoteItem->getTaxAmount());
                    $orderItem->setBaseTaxAmount($quoteItem->getBaseTaxAmount());
                    $orderItem->setSysproItemId($quoteItem->getSysproItemId());
                }
            }
        }
    }

    /**
     * @param $customer
     * @return string
     */
    private function getSysproCustomerId($customer)
    {
        $sysproCustomerId = '';
        if ($customer) {
            $customAttr = $customer->getCustomAttribute('syspro_customer_id');
            if ($customAttr) {
                $sysproCustomerId = $customAttr->getValue();
            }

            if (!$sysproCustomerId && empty($sysproCustomerId) && trim($sysproCustomerId) == 0) {
                $sysproCustomerId = $this->getSysproCustomerIdByCompany($customer->getId());
            }
        }
        return $sysproCustomerId;
    }

    /**
     * @param $customerId
     * @return mixed
     */
    private function getSysproCustomerIdByCompany($customerId)
    {
        $company = $this->companyRepository->getByCustomerId($customerId);
        return $company->getSysproCustomerId();
    }

    /**
     * @param $quoteItemId
     * @return null
     */
    private function getQuoteItemById($quoteItemId)
    {
        $quoteItem = null;
        try {
            $quoteItemCollection = $this->quoteItemCollectionFactory->create();
            $quoteItem           = $quoteItemCollection
                ->addFieldToSelect('*')
                ->addFieldToFilter('item_id', $quoteItemId)
                ->getFirstItem();
        } catch (\Exception) {
            $quoteItem = null;
        }
        return $quoteItem;
    }
}

