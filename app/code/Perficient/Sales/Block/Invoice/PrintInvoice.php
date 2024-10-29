<?php

namespace Perficient\Sales\Block\Invoice;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Block\Order\PrintOrder\Invoice;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;

class PrintInvoice extends Invoice
{
    public function __construct(
        Context $context,
        Registry $registry,
        Data $paymentHelper,
        Renderer $addressRenderer,
        protected ShipmentRepositoryInterface $shipmentRepository,
        protected PriceCurrencyInterface $priceCurrency,
        protected Repository $transactionRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        protected FilterBuilder $filterBuilder,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $paymentHelper, $addressRenderer, $data);
    }

    function getShipmentDetails() {

        $shipmentId = null;
        $_order = $this->getOrder();
        $shipmentCount = $_order->getShipmentsCollection()->getSize();
        if($shipmentCount) {
            $shipmentId = $_order->getShipmentsCollection()->getFirstItem()->getId();
        }

        if(!$shipmentId) {
            return null;
        }

        $shipment = $this->getShipmentById($shipmentId);

        $shipmentTracks = [];
        if ($shipment) {
            $shipmentTracks = $shipment->getTracks();
        }

        return $shipmentTracks;
    }

    public function getShipmentById($id)
    {
        try {
            $shipment = $this->shipmentRepository->get($id);
        } catch (Exception)  {
            $shipment = null;
        }
        return $shipment;
    }

    /**
     * Method used to format the price.
     *
     * @param $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $value !== null ?
            $this->priceCurrency->format((float)$value, true,PriceCurrencyInterface::DEFAULT_PRECISION) :
            '';
    }

    public function getPaymentTransaction() {
        $order = $this->getOrder();
        $filters = [];
        $filters[] = $this->filterBuilder->setField('payment_id')
            ->setValue($order->getPayment()->getId())
            ->create();

        $filters[] = $this->filterBuilder->setField('order_id')
            ->setValue($order->getId())
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder->addFilters($filters)
            ->create();

        return $this->transactionRepository->getList($searchCriteria)->getFirstItem();
    }

    /**
     * @return array|mixed|null
     */
    public function getInvoice()
    {
        $invoice = $this->_coreRegistry->registry('current_invoice');
        if ($invoice) {
            return $invoice;
        } else {
            $invoiceId = 0;
            foreach ($this->getOrder()->getInvoiceCollection() as $invoice) {
                $invoiceId = $invoice->getData('entity_id');
            }
            $invoice = $this->invoiceRepository->get($invoiceId);
            $this->_coreRegistry->register('current_invoice', $invoice);
        }

        return $this->_coreRegistry->registry('current_invoice');
    }
}
