<?php
/**
 * Create/Update order invoice/shipment in Magento from SysPro
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Perficient, Inc.
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_Rabbitmq Syspro
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

//use Perficient\WellsFargoPayments\Model\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice;
use Perficient\Rabbitmq\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\Data\InvoiceItemCreationInterfaceFactory;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\Data\ShipmentItemCreationInterfaceFactory;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\CompanyCredit\Model\CompanyCreditPaymentConfigProvider;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Convert\Order;
use Magento\Sales\Model\Order As OrderModel;
use Magento\Shipping\Model\ShipmentNotifier;
use Perficient\Rabbitmq\Model\MagentoToErp;

/**
 * Class InvoiceShipmentCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class InvoiceShipmentCreateUpdate
{
    /**
     * @var
     */
    private $errorLogger;

    /**
     * @var
     */
    private $messageArrayFull;

    /**
     * @var
     */
    private $messageArray;

    /**
     * @var
     */
    private $order;

    const INITIAL_FUND_CAPTURE_OFFLINE = "offline";

    /**
     * InvoiceShipmentCreateUpdate constructor.
     *
     * @param Json $jsonSerializer
     * @param OrderFactory $orderFactory
     * @param InvoiceItemCreationInterfaceFactory $invoiceItemCreationInterfaceFactory
     * @param InvoiceOrderInterface $invoiceOrderInterface
     * @param ShipmentItemCreationInterfaceFactory $shipmentItemCreationInterfaceFactory
     * @param ShipOrderInterface $shipOrderInterface
     * @param InvoiceRepositoryInterface $invoiceRepositoryInterface
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param ShipmentTrackCreationInterfaceFactory $trackCreationFactory
     * @param Order $convertOrder
     * @param ShipmentNotifier $shipmentNotifier
     */
    public function __construct(
        private readonly Data $rabbitMqHelper,
        private readonly Json $jsonSerializer,
        private readonly OrderFactory $orderFactory,
        private readonly InvoiceItemCreationInterfaceFactory $invoiceItemCreationInterfaceFactory,
        private readonly InvoiceOrderInterface $invoiceOrderInterface,
        private readonly ShipmentItemCreationInterfaceFactory $shipmentItemCreationInterfaceFactory,
        private readonly ShipOrderInterface $shipOrderInterface,
        private readonly InvoiceRepositoryInterface $invoiceRepositoryInterface,
        private readonly OrderRepositoryInterface $orderRepositoryInterface,
        private readonly ShipmentTrackCreationInterfaceFactory $trackCreationFactory,
        private readonly \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        private readonly \Magento\Framework\DB\TransactionFactory $transaction,
        private readonly Order $convertOrder,
        private readonly \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        private readonly ShipmentNotifier $shipmentNotifier,
        private readonly MagentoToErp $magentoToErp
    ) {
    }

    /**
     * Validate Json
     * @param $rawJson
     */
    private function isValidJson($rawJson): bool
    {
        $unSerializedData = $this->jsonSerializer->unserialize($rawJson);
        return ($unSerializedData == null) ? false : true;
    }

    /**
     * Method used to log message and send email.
     *
     * @param $data
     * @param $message
     */
    private function errorLoggerForInvoiceShipmentCreateProcess($data, $message): void
    {
        if (!$this->errorLogger) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::ORDER_INVOICE_SHIPMENT_CREATE_ERROR_LOG_FILE
                );
            }
        }

        try {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger->debug($message . "::" . $data);
            }
            $publishData = ['error' => $message];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$data.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $message,
                __('Invoice/Shipment Create/Update'),
                $this->jsonSerializer->serialize($data)
            );
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger->debug($e->getMessage(). "::" . $data);
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$data.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE, $jsonData);
        }
    }

    /**
     * Get invoice items of order
     * @return mixed
     */
    private function getInvoiceItems()
    {
        $invoiceItems = $this->messageArray['shipments'] ?? [];
        if (empty($invoiceItems)) {
            return false;
        }
        $itemsArray = $this->getItemsArrayInvoice($invoiceItems);
        /*if (!$itemsArray['product_available'] || empty($itemsArray['invoice_items'])) {
            return false;
        }*/
        return $itemsArray['invoice_items'];
    }

    /**
     * Create array for Invoice items for partial shipping
     */
    private function getItemsArrayInvoice(array $invoiceItems): array
    {
        /** @var \Magento\Sales\Model\Order\Item $allItems */
        $allItems = $this->order->getAllVisibleItems();
        $items = [];
        $check = true;
        foreach ($invoiceItems as $itemKey => $invoiceItem) {
            foreach ($invoiceItem['items'] as $item) {
                $sku = $item['sku'] ?? false;
                $qty = $item['qty'] ?? false;
                foreach ($allItems as $key => $orderItem) {
                    if ($qty && $sku && $sku === $orderItem->getSku() && $orderItem->getQtyToInvoice() >= $qty) {
                        $items[$orderItem->getId()] = [
                            'qty'   => $qty,
                            'total' => $orderItem->getRowTotal()
                        ];
                    }
                }
            }
        }

        if (empty($items) || \count($invoiceItems) !== \count($items)) {
            $check = false;
            $items = $invoiceItems;
        }

        return ['product_available' => $check, 'invoice_items' => $items];
    }

    /**
     * Method used to get shipment items.
     *
     * @param null $order
     * @param array $shipmentItems
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateShipment($orderMessage, $order = null, $shipmentItems = [])
    {
        if (!$order) {
            $order = $this->order;
            $shipmentItems = $this->messageArray['shipments'] ?? [];
        }
        if (empty($shipmentItems)) {
            return false;
        }

        /** @var \Magento\Sales\Model\Order\Item $allItems */
        $allItems = $order->getAllVisibleItems();
        $items = [];

        // Initialize the order shipment object
        $convertOrder = $this->convertOrder;
        $shipment = $convertOrder->toShipment($order);

        $doShipment = false;
        foreach ($shipmentItems as $itemKey => $shipmentItem) {
            if (isset($shipmentItem['items']) && !empty($shipmentItem['items']) && ($shipmentItem['items'] !== null)) {
                foreach ($shipmentItem['items'] as $item) {
                    $sku = $item['sku'] ?? false;
                    $qty = $item['qty'] ?? false;
                    foreach ($allItems as $key => $orderItem) {
                        if ($qty && $sku && $sku === $orderItem->getSku() && $orderItem->getQtyToShip() >= $qty) {
                            $doShipment = true;
                            $qtyShipped = $orderItem->getQtyToShip();

                            // Create shipment item with qty
                            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                            // Add shipment item to shipment
                            $shipment->addItem($shipmentItem);
                            continue 2;
                        }
                    }
                }
            } else {
                foreach ($allItems as $key => $orderItem) {
                    $doShipment = true;
                    $qtyShipped = $orderItem->getQtyToShip();

                    // Create shipment item with qty
                    $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                    // Add shipment item to shipment
                    $shipment->addItem($shipmentItem);
                    continue;
                }
            }
        }

        if ($doShipment) {
            // Register shipment
            $shipment->register();

            $shipment->getOrder()->setIsInProcess(true);
            try {
                /*Add Multiple tracking information*/
                foreach ($shipmentItems as $itemKey => $shipmentItem) {
                    $data = [
                        'carrier_code' => $shipmentItem['carrier_code'],
                        'title' => $shipmentItem['title'],
                        'track_number' => $shipmentItem['track_number']
                    ];
                    $track = $this->trackFactory->create()->addData($data);
                    $shipment->setCartonCount($shipmentItem['carton_count']);
                    $shipment->setPalletCount($shipmentItem['pallet_count']);
                    $shipment->setShipmentWeight($shipmentItem['shipment_weight']);
                    $shipment->setShipperNumber($shipmentItem['shipper_number']);
                    if (isset($shipmentItem['shipped_date']) && !empty($shipmentItem['shipped_date'])) {
                        $shipment->setCreatedAt($shipmentItem['shipped_date']);
                    }
                    $shipment->addTrack($track)->save();
                }

                // Save created shipment and order
                $shipment->save();
                $shipment->getOrder()->save();

                /**
                 * Commented below code because it was setting order status as closed instead of status sent in message
                 */
                // Change order status to complete
                /*$shipment->getOrder()
                    ->setState(OrderModel::STATE_COMPLETE)
                    ->setStatus(OrderModel::STATE_COMPLETE)
                    ->save();*/

                // Send email (out of scope as per FDD)
                // $this->shipmentNotifier->notify($shipment);

                // success log for shipment
                $message = __(
                    'Shipment #%1 has been generated successfully for the order #%2',
                    $shipment->getIncrementId(),
                    $shipment->getOrder()->getIncrementId()
                );
                if (!$this->errorLogger) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $this->errorLogger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::ORDER_INVOICE_SHIPMENT_CREATE_ERROR_LOG_FILE
                        );
                        $this->errorLogger->debug($message);
                    }
                }
            } catch (\Exception $e) {
                $this->errorLoggerForInvoiceShipmentCreateProcess(
                    $e,
                    $e->getMessage()
                );
                $publishData = ['error' => $e->getMessage()];
                $jsonData = $this->jsonSerializer->serialize($publishData);
                $jsonData = rtrim((string) $jsonData,'}') . ', "Message" :"' . $orderMessage. '"}';
                $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE, $jsonData);
            }
        }
    }

    /**
     * Method used to generate invoice.
     * @param $shipments
     * @param null $order
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateInvoice($shipments, $payment, $order = null)
    {
        $invoiceJsonArray = [];
        $initialFundsCapture = $order->getInitiateFundsCapture();
        if (!$this->errorLogger) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::ORDER_INVOICE_SHIPMENT_CREATE_ERROR_LOG_FILE
                );
            }
        }

        $isInvoiceGenerated = false;
        if (!$order) {
            $order = $this->order;
        }

        if (!$order->getId()) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger->debug(__('The order no longer exists.'));
            }
        } elseif (!$order->canInvoice()) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger->debug(
                    __('The order #%1 does not allow an invoice to be created.', $order->getIncrementId())
                );
            }
        } else {
            $orderNumber = $order->getIncrementId();
            $paymentInformation = $order->getPayment()->getAdditionalInformation();

            $invoiceAmount = $balanceDue = 0;
            if (isset($payment) && isset($payment['amount'])) {
                $invoiceAmount = $payment['amount'];
            }
            if (isset($shipments[0]) && isset($shipments[0]['shipments']['balance_due']) && !empty($shipments[0]['shipments']['balance_due'])) {
                $balanceDue = $shipments[0]['shipments']['balance_due'];
            }

            $invoiceMode = (isset($initialFundsCapture) &&
                strtolower((string) $initialFundsCapture) == self::INITIAL_FUND_CAPTURE_OFFLINE)
                ? Invoice::CAPTURE_OFFLINE : Invoice::CAPTURE_ONLINE;
            try {
                $invoice = $this->invoiceService->prepareInvoice($order);
                if (!$invoice) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $this->errorLogger->debug(
                            __('We can\'t save the invoice right now for the order #%1.', $orderNumber)
                        );
                    }
                } elseif (!$invoice->getTotalQty()) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $this->errorLogger->debug(
                            __('You can\'t create an invoice without products for order #%1.', $orderNumber)
                        );
                    }
                } else {
                    $invoice->setRequestedCaptureCase($invoiceMode);
                    $invoice->setInvoiceAmount($invoiceAmount);
                    $invoice->setBalanceDue($balanceDue);
                    $invoice->register();
                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);
                    $order->addStatusHistoryComment('Invoice generated (Automatically)', true);
                    $transactionSave = $this->transaction->create()->addObject($invoice)->addObject($invoice->getOrder());
                    $transactionSave->save();

                    /*// send invoice emails, If you want to stop mail disable below try/catch code
                    try {
                        $this->invoiceSender->send($invoice);
                    } catch (\Exception $e) {
                        $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
                    }*/

                    // success log for invoice generation
                    $message = __(
                        'Invoice #%1 has been generated successfully for the order #%2',
                        $invoice->getIncrementId(),
                        $orderNumber
                    );
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $this->errorLogger->debug($message);
                    }
                    $isInvoiceGenerated = true;
                    $invoiceOrder = $invoice->getOrder();
                    $invoicePaymentDetails = $invoiceOrder->getPayment()->getAdditionalInformation();
                    $lastCcNumber = $invoiceOrder->getPayment()->getCcLast4();

                    $incrementId = $order->getIncrementId();
                    $incrementIdArray = explode('-', (string) $incrementId);
                    $newIncrementId = $incrementIdArray[0];

                    $invoiceJsonArray = [
                        'web_order_id' => $newIncrementId,
                        'syspro_order_id' => $order->getSysproOrderId(),
                        'syspro_customer_id' => $order->getSysproCustomerId(),
                        'customer_email' => $order->getCustomerEmail(),
                        'transaction_id' => ($invoiceMode == Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['transaction_id'])) ? $invoicePaymentDetails['transaction_id'] : '',
                        'transaction_type' => ($invoiceMode == Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['transaction_type'])) ? $invoicePaymentDetails['transaction_type'] : '',
                        'acc_number' => ($invoiceMode == Invoice::CAPTURE_ONLINE && !empty($lastCcNumber)) ? $lastCcNumber : '',
                        'credit_card_type' => ($invoiceMode == Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['card_type'])) ? $invoicePaymentDetails['card_type'] : '',
                        'amount' => Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['amount']) ? $invoicePaymentDetails['amount'] : $invoice->getGrandTotal(),
                        'invoice_created' => true,
                        'error_message' => '',
                        'invoice_number' => $invoice->getIncrementId(),
                        'invoice_type' => $invoiceMode,
                        'method' => $invoiceOrder->getPayment()->getMethod(),
                        'response_reason_text' => ($invoiceMode == Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['response_reason_text'])) ? $invoicePaymentDetails['response_reason_text'] : '',
                        'approval_code' => ($invoiceMode == Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['approval_code'])) ? $invoicePaymentDetails['approval_code'] : '',
                        'auth_code' => ($invoiceMode == Invoice::CAPTURE_ONLINE && isset($invoicePaymentDetails['auth_code'])) ? $invoicePaymentDetails['auth_code'] : '',
                    ];
                }
            } catch (\Exception $e) {
                $invoiceJsonArray['error_message'] = $e->getMessage();
                $this->errorLoggerForInvoiceShipmentCreateProcess(
                    $e->getMessage(),
                    __('Unable to create invoice of the order #%1', $orderNumber)
                );
                $publishData = ['error' => $e->getMessage()];
                $jsonData = $this->jsonSerializer->serialize($publishData);
                $jsonData=rtrim((string) $jsonData,'}').', "Order Number" :"'.$orderNumber.'"}';
                $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_INVOICE_SUCCESS_FAILURE, $jsonData);
            }

            /**
             * Send the invoice details to SysPro only for the CC payment methods.
             */
            $paymentMethod = $order->getPayment()->getMethod();
//            if (in_array($paymentMethod, $this->rabbitMqHelper->validPaymentMethods)) {
                if (isset($initialFundsCapture) && strtolower((string) $initialFundsCapture) != self::INITIAL_FUND_CAPTURE_OFFLINE) {
                    $topic = Data::TOPIC_INVOICE_SUCCESS_FAILURE;
                    $message = $this->jsonSerializer->serialize($invoiceJsonArray);
                    $this->magentoToErp->sendDataFromMagentoToERP($topic, $message);
                }
//            }
        }
        return $isInvoiceGenerated;
    }
}
