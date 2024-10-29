<?php
/**
 * Create order in Magento from SysPro
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

use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Shipping\Model\Config as ShipConfig;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\Rabbitmq\Helper\Data;
use LogicException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\OfflinePayments\Model\Checkmo;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\AddressFactory as QuoteAddressFactory;
use Perficient\Rabbitmq\Model\InvoiceShipmentCreateUpdate;
use Perficient\Rabbitmq\Model\MagentoToErp;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Perficient\LeadTime\Helper\Data as LeadTimeHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote\ItemFactory as QuoteItemFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Quote\Model\Quote\Address\Rate;
use ParadoxLabs\Authnetcim\Model\Ach\ConfigProvider as AuthnetAchConfigProvider;

/**
 * Class OrderCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class OrderCreateUpdate extends AbstractModel
{
    /**
     * Constants.
     */
    const CUSTOMER_ID_ATTR_CODE = 'customer_id';
    const SYSPRO_CUSTOMER_ID_ATTR_CODE = 'syspro_customer_id';
    const CUSTOMER_EMAIL_ATTR_CODE = 'email';
    const DEFAULT_SHIPPING_METHOD = 'freeshipping_freeshipping';
    const DEFAULT_SHIPPING_DESCRIPTION = 'Free Shipping';
    const CUSTOM_SHIPPING_METHOD_CODE = 'customshipping_customshipping';
    const CUSTOM_SHIPPING_METHOD_DESC = '3rd Party Shipping - Use My Shipping Account';
    const ORDER_SOURCE = 'SysPro';
    const FLATRATE_DESCRIPTION = 'To Be Determined';
    const FLATRATE_SHIPPING_METHOD = 'flatrate_flatrate';

    const STATUS_ON_HOLD = 'On Hold';
    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_COMPLETE = 'Complete';
    const STATUS_CLOSED = 'Closed';
    const STATUS_CANCELED = 'Cancelled';
    /**
     * @var string
     */
    const INITIAL_FUND_CAPTURE_OFFLINE = "offline";
    const INITIAL_FUND_CAPTURE_YES = "yes";
    const INITIAL_FUND_CAPTURE_NO = "no";
    const TRANSACTION_TYPE = 'auth_only';


    private array $allowedOrderStatus = [
        self::STATUS_ON_HOLD,
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETE,
        self::STATUS_CLOSED,
        self::STATUS_CANCELED
    ];

    /**
     * Constants for various order-type.
     */
    const ORDER_TYPE_HEADER    = 'header';
    const ORDER_TYPE_LINE_ATTR = 'line attributes';
    const ORDER_TYPE_ORDER_VAL = 'order values';

    private array $sysProOrderStatuses = [
        'order entry'                       => 'order_entry',
        'awaiting deposit or prepayment'    => 'awaiting_deposit_or_prepayment',
        'awaiting design approval'          => 'awaiting_design_approval',
        'in production'                     => 'in_production',
        'awaiting final payment or release' => 'awaiting_final_payment_or_releas',
        'ready to ship'                     => 'ready_to_ship',
        'partially shipped'                 => 'partially_shipped',
        'processing'                        => Order::STATE_PROCESSING,
        'shipped'                           => Order::STATE_COMPLETE,
        'complete'                          => Order::STATE_COMPLETE,
        'canceled'                          => Order::STATE_CANCELED,
        'closed'                            => Order::STATE_CLOSED,
    ];

    /**
     * Constants for address type.
     */
    const ADDRESS_TYPE_BILL = 'billing';
    const ADDRESS_TYPE_SHIP = 'shipping';

    /**
     * Constant for dummy product sku.
     */
    const DUMMY_PRODUCT_SKU = 'MISC';

    /**
     * @var null
     */
    private $dummyProductDetails = null;

    /**
     * @var
     */
    private $doCancelOrder;

    /**
     * @var
     */
    private $storeId;

    /**
     * @var object
     */
    private $message;

    /**
     * @var array
     */
    private $messageBody;

    /**
     * @var object
     */
    private $order;

    /**
     * @var $messageArray
     */
    private $messageArray;

    private $errorLogger;

    private bool|\Magento\Quote\Model\Quote|null $_quote = false;

    private bool|\Magento\Quote\Model\Quote $_quoteData = false;

    private array $orderData = [];

    private array $updateAllowOrderStatus = [
        Order::STATE_COMPLETE,
        Order::STATE_CLOSED,
        Order::STATE_CANCELED
    ];

    private $oldOrder;

    private ?bool $isItemsDifferent = null;

    const SOURCE_FLAG_SYSPRO = 0;

    private ?int $orderCreateFlag = null;

    private ?int $orderUpdateFlag = null;

    private bool $errorCreditmemo = false;

    private bool $isSequentialOrder = false;

    private $existingPaymentMethod;

    private $company;

    /**
     * OrderCreateUpdate constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $jsonSerializer
     * @param OrderFactory $orderFactory
     * @param CollectionFactory $orderCollectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param CompanyManagementInterface $companyManagement
     * @param AddressFactory $addressFactory
     * @param ItemFactory $itemFactory
     * @param OrderPaymentRepositoryInterface $orderPayment
     * @param DateTime $dateTime
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param CompanyRepositoryInterface $companyRepository
     * @param RegionFactory $regionFactory
     * @param Create $adminCreate
     * @param CartRepositoryInterface $cartRepositoryInterface
     * @param CartManagementInterface $cartManagementInterface
     * @param QuoteFactory $quoteFactory
     * @param QuoteManagement $quoteManagement
     * @param Config $eavConfig
     * @param Order $orderModel
     * @param CreditmemoFactory $creditMemoFactory
     * @param Invoice $invoice
     * @param CreditmemoService $creditMemoService
     * @param OrderInterfaceFactory $orderInterfaceFactory
     * @param DataObjectFactory $objectFactory
     * @param ResourceConnection $resourceConnection
     * @param ShipConfig $shippingConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param Rate $shippingQuoteRate
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly Data $rabbitMqHelper,
        private readonly Json $jsonSerializer,
        private readonly OrderFactory $orderFactory,
        private readonly CollectionFactory $orderCollectionFactory,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly ProductCollectionFactory $productCollectionFactory,
        private readonly AddressFactory $addressFactory,
        private readonly ItemFactory $itemFactory,
        private readonly OrderPaymentRepositoryInterface $orderPayment,
        private readonly DateTime $dateTime,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly StoreManagerInterface $storeManager,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly RegionFactory $regionFactory,
        private readonly Create $adminCreate,
        private readonly CartRepositoryInterface $cartRepositoryInterface,
        private readonly CartManagementInterface $cartManagementInterface,
        private readonly QuoteFactory $quoteFactory,
        private readonly QuoteManagement $quoteManagement,
        private readonly QuoteAddressFactory $quoteAddressFactory,
        private readonly Config $eavConfig,
        private readonly Order $orderModel,
        private readonly InvoiceShipmentCreateUpdate $invoiceShipmentCreateUpdate,
        private readonly CreditmemoFactory $creditMemoFactory,
        private readonly Invoice $invoice,
        private readonly CreditmemoService $creditMemoService,
        private readonly MagentoToErp $magentoToErp,
        private readonly OrderInterfaceFactory $orderInterfaceFactory,
        private readonly Date $date,
        private readonly LeadTimeHelper $leadTimeHelper,
        private readonly DataObjectFactory $objectFactory,
        private readonly QuoteItemFactory $quoteItemFactory,
        private readonly ResourceConnection $resourceConnection,
        private readonly \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        private readonly \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
        private readonly \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
        private readonly ShipConfig $shippingConfig,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Rate $shippingQuoteRate,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Create/update order in Magento based on incoming data from SysPro
     *
     * @param $message
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createUpdateOrder($message): void
    {
    	$e = null;
        //Temp disabled to disable syspro ordr sync
        //return;

        $this->rabbitMqHelper->logOrderSyncDetailed('Inside createUpdateOrder');
        //Log the incoming message
        $this->rabbitMqHelper->logRabbitMqMessage($message);
        $this->errorLogger = $this->rabbitMqHelper->getRabbiMqLogger(Data::ORDER_CREATE_ERROR_LOG_FILE);

		try {
        //validate message body
        $this->messageBody = $this->rabbitMqHelper->cleanString($message->getBody());
        $messageJson = $this->isValidJson($this->messageBody);

        if (!$this->messageBody || !$messageJson) {
            $this->errorLogger->debug('Empty message or message is not in valid json format.');
            $this->errorLogger->debug($this->messageBody);
            $this->rabbitMqHelper->sendErrorEmail(
                __('Empty message or message is not in valid json format.'),
                __('Order Create'),
                $this->messageBody
            );
            return;
        }

        $this->messageArray = $this->jsonSerializer->unserialize($this->messageBody);

        } catch (\LogicException|\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::ORDER_CREATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$message->getBody().'"}';
            //$publishData = ['error' => $e->getMessage(), 'message' => $message->getBody()];
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Order Create Update'),
                $message->getBody()
            );
        }

        if (!isset($this->messageArray['data'])) {
            try{
                $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
            }catch(\Exception $e){
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
            }
        }

        if (isset($this->messageArray['data'])) {
            // Get the store-id
            $this->storeId = $this->storeManager->getStore()->getId();

            foreach ($this->messageArray['data'] as $messageKey => $order) {
                try {
                    $this->message = $order;
                    $this->hasDummyProduct = false;
                    $messageBody = $message->getBody();
                    $this->errorCreditmemo = false;
                    $this->orderCreateFlag = 0;
                    $this->orderUpdateFlag = 0;
                    $orderCompanyId = '';
                    $this->oldOrder = null;
                    $this->_quote = null;
                    $this->isItemsDifferent = false;
                    $this->isSequentialOrder = false;

                    // If customer does not exists then skip this order.
                    $this->company = $this->getCompanyById($order['web_company_id']);
                    $customerId = '';
                    if ($this->company) {
                        $this->order = $this->getOrderFromMagento();
                        if ($this->order === false) {
                            continue;
                        }

                        //check create or update case
                        if (isset($order['syspro_order_id']) && !empty($order['syspro_order_id'])
                            && isset($order['web_order_id']) && empty($order['web_order_id'])
                            && !$this->order->getId()) {
                            //1 means syspro order create case
                            $this->orderCreateFlag = 1;
                            $customerId = $this->company->getSuperUserId();
                        } else {
                            //1 means syspro/magento order update case
                            $this->orderUpdateFlag = 1;
                            $orderCustId = $this->order->getCustomerId();
                            $orderCompanyId = $this->companyManagement->getByCustomerId($orderCustId)->getId();
                        }

                        // start : Check if company match
                        if ($this->orderUpdateFlag == 1 &&
                            isset($orderCompanyId) && !empty($orderCompanyId) &&
                            isset($order['web_company_id']) && !empty($order['web_company_id']) &&
                            $order['web_company_id'] != $orderCompanyId) {
                            $errMessage = __('web_company_id does not match with company id present in Magento order.');
                            $this->moveToErrorQueue($messageBody, $errMessage);
                            continue;
                        }
                        // End : Check if company match

                        // start : Check if customer match
                        if ($this->orderUpdateFlag == 1 &&
                            isset($order['customer_email']) && !empty(trim((string) $order['customer_email']))) {
                            $messageCustEmail = trim((string) $order['customer_email']);
                            $custData = $this->customerRepository->get($messageCustEmail);
                            $orderCustId = $this->order->getCustomerId();
                            //$messageCustId = $custData->getId();
                            //check if customer match or not
                            if ($custData && $orderCustId != $custData->getId()) {
                                $errMessage = __(
                                    'customer_email does not matches with customer id present in Magento order.'
                                );
                                $this->moveToErrorQueue($messageBody, $errMessage);
                                continue;
                            }
                        }
                        // End : Check if customer match

                        //Start : check if already invoiced and amount changed and initial_fund_capture No
                        $grandTotal = (isset($order['grand_total']) && !empty($order['grand_total']))
                            ? (float)$order['grand_total'] : 0;
                        if ($this->order->getId() && $this->orderUpdateFlag == 1
                            && $this->order->hasInvoices() && $this->order->getGrandTotal() != $grandTotal
                            && strtolower((string) $order['initiate_funds_capture']) == self::INITIAL_FUND_CAPTURE_NO) {
                            $errMessage = __(
                                'Order is already invoiced in Magento, there is change in grand total in update '
                                . 'message so initiate_funds_capture need to be Yes.'
                            );
                            $this->moveToErrorQueue($messageBody, $errMessage);
                            continue;
                        }
                        //End : check if already invoiced and amount changed and initial_fund_capture No

                        //Start : check if already invoiced and trying to invoice again with order message type header
                        /*if ($this->order->getId() && $this->orderUpdateFlag == 1
                            && $this->order->hasInvoices()
                            && strtolower($order['initiate_funds_capture']) == self::INITIAL_FUND_CAPTURE_YES
                            && self::ORDER_TYPE_HEADER == strtolower($order['order_message_type'])) {
                            $errMessage = __(
                                'Order is already invoiced in Magento, initiate_funds_capture is Yes but'
                                . ' order_message_type is Header. order_message_type need to be Order Values.'
                            );
                            $this->moveToErrorQueue($messageBody, $errMessage);
                            continue;
                        }*/
                        //End : check if already invoiced and trying to invoice again with order message type header

                        if ($this->orderUpdateFlag == 1) {
                            //source_flag = 0 means syspro order & source_flag = 1 means Magento order
                            if ($this->order->getSourceFlag() == 0) {
                                $customerId = $this->company->getSuperUserId();
                            } elseif ($this->order->getSourceFlag() == 1) {
                                $customerId = $this->order->getCustomerId();
                            }
                        }
                    } else {
                        //move to error queue
                        $errMessage = __('web_company_id is not present in Magento.');
                        $this->moveToErrorQueue($messageBody, $errMessage);
                        continue;
                    }

                    if (!$customerId) {
                        $errMessage = __('Order creation skipped as customer does not exists.');
                        $this->moveToErrorQueue($messageBody, $errMessage);
                        continue;
                    }

                    $erpOrderStatus = $order['order_status'] ?? '';
                    $orderStatus = $this->getStatusFromErpOrderStatus($erpOrderStatus);
                    if ($this->order->getId()
                        && strtolower((string) $order['order_status']) === Order::STATE_CANCELED) {
                        $this->rabbitMqHelper->logOrderSyncDetailed('Inside Canceled Order Case');
                        /* @var $this->order \Magento\Sales\Model\Order */
                        $this->order = $this->getOrderFromMagento();
                        $isInvoiceGenerated = $this->order->hasInvoices();
                        if ($isInvoiceGenerated && $this->generateCreditMemo()) {
                            // Sending false for non-sequential order
                            $this->cancelOrder(false);
                        } else {
                            $this->cancelOrder(false);
                        }
                    } else {
                        //Validate incoming order data
                        //$this->validateOrderData();

                        // If there is any issue in billing address then skip the current order.
                        if (!$this->validateBillingAddress()) {
                            if ($order['web_order_id']) {
                                $orderNumber = $order['web_order_id'];
                            } else {
                                $orderNumber = $order['syspro_order_id'];
                            }
                            $ErrMessage = __(
                                'Billing address detail is not valid or missing some data for order #%1.',
                                $orderNumber
                            );
                            $this->errorLogger->debug($ErrMessage);
                            $publishData = ['error' => $ErrMessage];
                            $jsonData = $this->jsonSerializer->serialize($publishData);
                            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'. $message->getBody().'"}';
                            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
                            $this->rabbitMqHelper->sendErrorEmail(
                                $ErrMessage,
                                __('Order Create/Update'),
                                $this->jsonSerializer->serialize($order)
                            );
                            continue;
                        }

                        // If there is any issue in shipping address then skip the current order.
                        if (!$this->validateShippingAddress()) {
                            if ($order['web_order_id']) {
                                $orderNumber = $order['web_order_id'];
                            } else {
                                $orderNumber = $order['syspro_order_id'];
                            }
                            $ErrMessage = __(
                                'Shipping Address detail is not valid or missing some data for order #%1',
                                $orderNumber
                            );
                            $this->errorLogger->debug($ErrMessage);
                            $publishData = ['error' => $ErrMessage];
                            $jsonData = $this->jsonSerializer->serialize($publishData);
                            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'. $message->getBody().'"}';
                            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
                            $this->rabbitMqHelper->sendErrorEmail(
                                $ErrMessage,
                                __('Order Create/Update'),
                                $this->jsonSerializer->serialize($order)
                            );
                            continue;
                        }

                        // If message contains shipping method other than available methods then skip the order.
                        $shippingMethod = $this->getShippingMethod();
                        if (!$shippingMethod['code']) {
                            if ($order['web_order_id']) {
                                $orderNumber = $order['web_order_id'];
                            } else {
                                $orderNumber = $order['syspro_order_id'];
                            }

                            $ErrMessage = __(
                                'Shipping Method is not valid for order #%1',
                                $orderNumber
                            );
                            $this->errorLogger->debug($ErrMessage);
                            $publishData = ['error' => $ErrMessage];
                            $jsonData = $this->jsonSerializer->serialize($publishData);
                            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'. $message->getBody().'"}';
                            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
                            $this->rabbitMqHelper->sendErrorEmail(
                                $ErrMessage,
                                __('Order Create/Update'),
                                $this->jsonSerializer->serialize($order)
                            );
                            continue;
                        }

                        // If there is any issue in order item then skip the current order.
                        /*if (!$this->validateOrderItems()) {
                            continue;
                        }*/

                        // Create quote and order from incoming data
                        $this->errorLogger->debug('web_order_id: '.$order['web_order_id']);
                        $this->errorLogger->debug('syspro_order_id: '.$order['syspro_order_id']);

                        // Update order.
                        $updateOrCreateOrder = $this->updateOrCreateOrder($customerId);
                        if ($updateOrCreateOrder === false) {
                            continue;
                        }

                        $isInvoiceGenerated = $this->order->hasInvoices();
                        // Code to create invoice/shipment.
                        if (isset($order['initiate_funds_capture']) &&
                            (self::INITIAL_FUND_CAPTURE_YES == strtolower((string) $order['initiate_funds_capture'])) ||
                            (strtolower((string) $order['initiate_funds_capture']) == self::INITIAL_FUND_CAPTURE_OFFLINE)) {
                            $shipmentItems = $order['shipments'] ?? [];
                            $payment = $order['payment'] ?? [];
                            //$isInvoiceGenerated = null;

                            if ($this->order && $this->order->canInvoice() && !$isInvoiceGenerated) {
                                $this->order->setInitiateFundsCapture($order['initiate_funds_capture']);
                                $isInvoiceGenerated = $this->invoiceShipmentCreateUpdate->generateInvoice(
                                    $shipmentItems,
                                    $payment,
                                    $this->order
                                );
                            }

                            if ($this->order && $isInvoiceGenerated) {
                                $this->setOrderStatus();
                                $this->order->save();
                            }
                        }

                        $checkmoPaymentMethod = 0;
                        if ($this->order) {
                            $orderPayment = $this->order->getPayment();
                            if (!empty($orderPayment)
                                && $orderPayment->getMethod() == Checkmo::PAYMENT_METHOD_CHECKMO_CODE) {
                                $checkmoPaymentMethod = 1;
                            }
                        }

                        /**
                         * Check and create Shipment.
                         */
                        $isShipmentGenerated = ($this->order && $this->order->getId())?$this->order->hasShipments():false;
                        if (($this->order && $this->order->canShip() && !$isShipmentGenerated && isset($order['shipments']) && !empty($order['shipments'][0]['track_number']))
                            && ($isInvoiceGenerated || $checkmoPaymentMethod)) {
                            $shipmentItems = $order['shipments'] ?? [];
                            $this->invoiceShipmentCreateUpdate->generateShipment($this->jsonSerializer->serialize($order), $this->order, $shipmentItems);
                        }
                    }

                } catch (LogicException $e) {
                	if ($this->rabbitMqHelper->isLoggingEnabled()) {
	                    $this->errorLogger->debug($e->getMessage() . '::' . $message->getBody());
    				}
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'. $message->getBody().'"}';
                    //$publishData = ['error' => $e->getMessage(), 'message' => $message->getBody()];
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Order Create'),
                        $this->jsonSerializer->serialize($order)
                    );
                } catch (\Exception|\Error $e) {
                	if ($this->rabbitMqHelper->isLoggingEnabled()) {
                    	$this->errorLogger->debug($e);
                    	$this->errorLogger->debug($e->getMessage() . '::' . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'. $message->getBody().'"}';
                    //$publishData = ['error' => $e->getMessage(), 'message' => $message->getBody()];
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Order Create'),
                        $this->jsonSerializer->serialize($order)
                    );
                }
            }
        } else {
        	if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $this->errorLogger->debug('Invalid Message :: ' . $message->getBody());
            }
            $publishData = ['error' => 'Invalid Message '];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$message->getBody().'"}';
            //$publishData = ['error' => $e->getMessage(), 'message' => $message->getBody()];
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Order Create'),
                $message->getBody()
            );
        }
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
     * Validate customer data
     *
     * @param $order
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateCustomerData($order)
    {
        $customerId = false;
        if (isset($order['web_customer_id']) && !empty($order['web_customer_id'])) {
            // Check customer by customer-id
            $customerId = $this->rabbitMqHelper->getCustomerByAttribute(
                self::CUSTOMER_ID_ATTR_CODE,
                $order['web_customer_id']
            );
        } else if (isset($order['syspro_customer_id']) && !empty($order['syspro_customer_id'])) {
            // Check customer by syspro-customer-id
            $customerId = $this->rabbitMqHelper->getCustomerByAttribute(
                self::SYSPRO_CUSTOMER_ID_ATTR_CODE,
                $order['syspro_customer_id']
            );
        } else if (isset($order['customer_email']) && !empty($order['customer_email'])) {
            // Check customer by customer-email
            $customerId = $this->rabbitMqHelper->getCustomerByAttribute(
                self::CUSTOMER_EMAIL_ATTR_CODE,
                $order['customer_email']
            );
        }

        // If customer does not exists in Magento then send the error email notification.
        if (!$customerId) {
            $this->rabbitMqHelper->sendErrorEmail(
                __('Can not create/update order as customer does not exists in Magento system. Skipping Order.'),
                __('Order Create/Update'),
                $this->jsonSerializer->serialize($order)
            );
        }

        // Return the customer exists flag.
        return $customerId;
    }

    /**
     * Method used to get company by name.
     *
     * @param $companyName
     * @return \Magento\Company\Api\Data\CompanyInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCompanyByName($companyName)
    {
        $this->searchCriteriaBuilder->addFilter(
            'company_name',
            trim((string)$companyName)
        );

        $companyData = $this->companyRepository->getList(
            $this->searchCriteriaBuilder
                ->setPageSize(1)
                ->create()
        )->getItems();

        $companyDetails = null;
        if ($companyData) {
            foreach ($companyData as $company) {
                $companyDetails = $company;
                break;
            }
        }

        return $companyDetails;
    }

    /**
     * Method used to get company by id.
     *
     * @param $companyId
     * @return \Magento\Company\Api\Data\CompanyInterface|null
     */
    private function getCompanyById($companyId)
    {
        try {
            $company = $this->companyRepository->get($companyId);
        } catch (NoSuchEntityException) {
            $company = null;
        }

        return $company;
    }

    /**
     * Validate incoming order data before creating order in Magento
     */
    private function validateOrderData()
    {
        $orderData = $this->message;
        $orderId   = $orderData['web_order_id'];

        $field = $value = '';
        if (isset($orderId) && !empty($orderId)) {
            $field = 'entity_id';
            $value = $orderId;
        } elseif (isset($orderData['syspro_order_id']) && !empty($orderData['syspro_order_id'])) {
            $field = 'syspro_order_id';
            $value = $orderData['syspro_order_id'];
        }

        if (!empty($field)) {
            //Check if order_id is already exist in Magento
            $orderCollection = $this->orderCollectionFactory->create()
                ->addFieldToFilter(
                    $field,
                    ['eq' => $value]
                );
        }

        //check for allowed order status
        $orderStatus = $orderData['order_status'];
        if (!in_array($orderStatus, $this->allowedOrderStatus)) {
            throw new \Exception(__('Invalid order status %1.', $orderStatus));
        }

        //Check other data
        if ((($orderData['payment'] === null) || empty($orderData['payment']))
            || (($orderData['subtotal'] === null) || (float) $orderData['subtotal'] < 0)
            || (($orderData['grand_total'] === null) || (float) $orderData['grand_total'] < 0)
            || (($orderData['billing_address'] === null) || empty($orderData['billing_address']))
            || (($orderData['shipping_address'] === null) || empty($orderData['shipping_address']))
        ) {
            throw new \Exception(
                __('Order data like payment, total, address, etc. is/are missing to create/update order.')
            );
        }

        //check if order items exist or not
        $orderItems = $orderData['items'] ?? [];
        if (empty($orderItems) || !isset($orderItems[0]['sku'])
            || (isset($orderItems[0]['sku']) && (($orderItems[0]['sku']) === null) || empty(trim((string) $orderItems[0]['sku'])))
        ) {
            throw new \Exception(
                __('Can not create order as order items are missing for order #%1.', $orderId)
            );
        }
    }

    /**
     * Validate billing address
     *
     * @return bool
     */
    private function validateBillingAddress()
    {
        $validated = true;
        $orderData = $this->message;
        $billingAddress = $orderData['billing_address'];
        if (($billingAddress['street1'] === null) || empty((string) $billingAddress['street1']) ||
            ($billingAddress['city'] === null) || empty(trim((string) $billingAddress['city'])) ||
            ($billingAddress['country_id'] === null) || empty(trim((string) $billingAddress['country_id'])) ||
            ($billingAddress['postcode'] === null) || empty(trim((string) $billingAddress['postcode'])) ||
            ($billingAddress['region_code'] === null) || empty(trim((string) $billingAddress['region_code']))
        ) {
            $validated = false;
        }

        return $validated;
    }

    /**
     * Validate shipping address
     *
     * @return bool
     */
    private function validateShippingAddress()
    {
        $validated = true;
        $orderData = $this->message;
        $shippingAddress = $orderData['shipping_address'];
        if (($shippingAddress['firstname'] === null) || empty(trim((string) $shippingAddress['firstname'])) ||
            ($shippingAddress['lastname'] === null) || empty(trim((string) $shippingAddress['lastname'])) ||
            ($shippingAddress['street1'] === null) || empty($shippingAddress['street1']) ||
            ($shippingAddress['city'] === null) || empty(trim((string) $shippingAddress['city'])) ||
            ($shippingAddress['country_id'] === null) ||  empty(trim((string) $shippingAddress['country_id'])) ||
            ($shippingAddress['postcode'] === null) || empty(trim((string) $shippingAddress['postcode'])) ||
            ($shippingAddress['region_code'] === null) || empty(trim((string) $shippingAddress['region_code']))
        ) {
            $validated = false;
        }

        return $validated;
    }

    /**
     * Method used to validate order items.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateOrderItems()
    {
        $validated  = true;
        $orderItems = $this->message['items'];
        foreach ($orderItems as $item) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect(['entity_id']);
            $productCollection->addFieldToFilter('sku', $item['sku']);
            $product = $productCollection->getFirstItem();

            if (!$product->getId()) {
                $validated = false;
                $this->rabbitMqHelper->sendErrorEmail(
                    __('Product SKU (%1) does not exists. Skipping order.', $item['sku']),
                    __('Order Create/Update'),
                    $this->jsonSerializer->serialize($this->message)
                );
                break;
            }
        }

        return $validated;
    }

    /**
     * Create order in Magento from data coming from SysPro
     *
     * @param $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createQuoteAndOrder($customerId)
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Inside createQuoteAndOrder');

        $currency = $this->priceCurrency->getCurrency()->getCurrencyCode();
        $companyId = $this->company->getId();
        $this->orderData = $this->message;

        try {
            $this->initializeData($customerId);
            $this->_quote->setStoreId($this->storeId);
            $this->_quote->setGlobalCurrencyCode($currency)
                ->setBaseCurrencyCode($currency)
                ->setStoreCurrencyCode($currency);
            $this->setProductsData();
            $this->setAddressesData();
            $this->setShippingMethodData();
            $this->setPaymentMethodData();

            // Set additional data/fields for the order
            $this->setAdditionalData($this->_quote);

            $this->_quote->setCompanyId($companyId);
            $this->_quote->setWebCompanyId($companyId);
            //$this->_quote->save();

            $logger = $this->rabbitMqHelper->getRabbiMqLogger(Data::ORDER_CREATE_ERROR_LOG_FILE);

            // Create Order From Quote
            $order = null;
            try {
                $order = $this->quoteManagement->submit($this->_quote);
            } catch (\Exception $e) {
                $logger->debug('Error: ' . $e->getMessage());
            }
            if ($order && $order->getEntityId()) {
                if ($this->rabbitMqHelper->isLoggingEnabled()) {
                    $logger->debug(
                        __('Success: Order created/updated #%1 (%2).', $order->getIncrementId(), $order->getId())
                    );
                }
                $this->order = $order;
                $erpOrderStatus = $this->orderData['order_status'] ?? '';
                $PaymentMethodFee = (isset($this->orderData['payment_method_fee']) && ($this->orderData['payment_method_fee'] !== null))
                    ? (float)$this->orderData['payment_method_fee'] : 0;
                $orderStatus = $this->getStatusFromErpOrderStatus($erpOrderStatus);
                $orderPayment = $order->getPayment();
                $orderPayment->setCustomerPoNumber($this->message['customer_po_number']);
                $order->setPayment($orderPayment);
                $order->setState($orderStatus)
                      ->setStatus($orderStatus)
                      ->setPaymentMethodFee($PaymentMethodFee);

                if (isset($this->orderData['source_type']) && !empty($this->orderData['source_type'])) {
                    $order->setSourceType($this->orderData['source_type']);
                }
                if (isset($this->orderData['source_name']) && !empty($this->orderData['source_name'])) {
                     $order->setSourceName($this->orderData['source_name']);
                }

                $order->save();
                $logger->debug(
                    __('Success: Order created/updated #%1 (%2).', $order->getIncrementId(), $order->getId())
                );
            } else {
                $logger->debug(__('Failure: Unable to create order for quote #%1.', $this->_quote->getId()));
            }
        } catch (\Exception $e) {
            $this->errorLogger->debug($e);
            throw new NoSuchEntityException(__('Unable to create order.'));
        }
    }

    /**
     * Create order in Magento from data coming from SysPro
     *
     * @param $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateOrCreateOrder($customerId)
    {
        /* @var $this ->order \Magento\Sales\Model\Order */
        $this->order = $this->getOrderFromMagento();

        if ($this->order->getId()) {
            if (!in_array($this->order->getStatus(), $this->updateAllowOrderStatus)) {
                $this->errorLogger->debug('incrementId: '.$this->order->getIncrementId()." | OrderId: ".$this->order->getId()." | Syspro OrderId: ".$this->order->getSysproOrderId());
                $updateOrder = $this->updateOrder($customerId);
                if ($updateOrder === false) {
                    return false;
                }
            } else {
                $errMessage = __('Can not update as order is Cancelled/Closed/Complete.');
                $messageBody = $this->jsonSerializer->serialize($this->messageArray);
                $this->moveToErrorQueue($messageBody, $errMessage);
                return false;
            }
        } else {
            $this->createQuoteAndOrder($customerId);
        }
    }

    /**
     * Create order in Magento from data coming from SysPro
     *
     * @param $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateOrder($customerId)
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Inside updateOrder()');

        $orderData = $this->message;
        if ($orderData['web_order_id']) {
            $messageOrderNumber = $orderData['web_order_id'];
        } else {
            $messageOrderNumber = $orderData['syspro_order_id'];
        }
        $initialFundsCapture = $orderData['initiate_funds_capture'];

        //start : Check if Converting regular order to quickship or vice versa
        if (!$this->checkQuickShip()) {
            $errMessage = __(
                'quick_ship flag in message is not matching with quick_ship flag in Magento order #%1.',
                $messageOrderNumber
            );
            $messageBody = $this->jsonSerializer->serialize($this->messageArray);
            $this->moveToErrorQueue($messageBody, $errMessage);
            return false;
        }
        //end : Check if Converting regular order to quickship or vice versa

        /* @var $this->order \Magento\Sales\Model\Order */
        $this->order = $this->getOrderFromMagento();
        if ($this->order->getId()) {
            $this->isItemsDifferent = $this->checkChangeInItems();
        }
        $paymentAdditionalInfo = $this->order->getPayment()->getAdditionalInformation();
        $transactionType = $paymentAdditionalInfo['transaction_type'] ?? '';

        if (strtolower((string) $initialFundsCapture) === self::INITIAL_FUND_CAPTURE_OFFLINE
            && $transactionType == self::TRANSACTION_TYPE) {
            $this->rabbitMqHelper->logOrderSyncDetailed('Inside updateOrder() : offline case' . $this->order->getId());
            $this->order->getPayment()->void(new \Magento\Framework\DataObject());
            $this->order->setSysproOrderId($this->order->getSysprOrderId());
            $this->order->save();
        }

        //$this->setOrderData($customerId);

        // Save order
        try {
            //$orderResource = $this->order->getResource();
            //$orderResource->save($this->order);
            //if ($this->order->getId() && self::ORDER_TYPE_ORDER_VAL == strtolower($orderData['order_message_type'])) {
            if ($this->order->getId()) {
                //$orderNumber = $this->order->getIncrementId() . '-' . ($this->order->getEditIncrement() + 1);
                $orderNumber = $this->prepareOrderNumber($this->order);
                $this->existingPaymentMethod = $this->order->getPayment()->getMethod();

				//in case of fund no, shipment creation this was getting processed so added this check
                if ((self::INITIAL_FUND_CAPTURE_YES == strtolower((string) $initialFundsCapture) &&
                    $this->generateCreditMemo()) || $this->isItemsDifferent) {
                    /*Need to call generate creditmemo again as creditmemo
                    was not getting generated in case of items change in message.*/
                    $this->generateCreditMemo();
                    if ($this->errorCreditmemo === true) {
                        /*check to stop further execution of code in case of exception in creditmemo generation.
                        Refer function $this->generateCreditMemo()*/
                        return false;
                    }
                    // Sending true for sequential order
                    $this->cancelOrder(true);

                    $this->oldOrder = $this->order;
                    $sourceFlag = $this->oldOrder->getSourceFlag();
                    $this->order = $this->orderFactory->create();
                    $this->order->setId(null);
                    $this->order->setIncrementId($orderNumber);
                    $this->order->setSourceFlag($sourceFlag);
                    $this->rabbitMqHelper->logOrderSyncDetailed('New increment id'. $orderNumber);
                    $this->isSequentialOrder = true;
                    $this->setOrderData($customerId, true);

                    $order = $this->order->save();
                    $shipmentItems = $this->message['shipments'] ?? [];
                    $payment = $this->message['payment'] ?? [];
                    $isInvoiceGenerated = null;
                    $checkmoPaymentMethod = 0;
                    if ($this->order) {
                        $orderPayment = $this->order->getPayment();
                        if (!empty($orderPayment)
                            && $orderPayment->getMethod() == Checkmo::PAYMENT_METHOD_CHECKMO_CODE) {
                            $checkmoPaymentMethod = 1;
                        }
                    }

                    if ($this->isItemsDifferent &&
                        strtolower((string) $initialFundsCapture) == self::INITIAL_FUND_CAPTURE_NO && !$checkmoPaymentMethod) {
                        $seqOrderPayment = $this->order->getPayment();
                        $seqOrderPayment->authorize(true, $orderData['grand_total']);
                        $baseAuthAmt = $seqOrderPayment->getBaseAmountAuthorized();
                        $authAmt = $seqOrderPayment->getAmountAuthorized();
                        if (empty($authAmt) && !empty($baseAuthAmt)) {
                            $seqOrderPayment->setAmountAuthorized($baseAuthAmt);
                        }
                    } else {
                        if ($order && $order->canInvoice() &&
                            self::INITIAL_FUND_CAPTURE_YES == strtolower((string) $initialFundsCapture)) {
                            $order->setInitiateFundsCapture($initialFundsCapture);
                            $isInvoiceGenerated = $this->invoiceShipmentCreateUpdate->generateInvoice(
                                $shipmentItems,
                                $payment,
                                $order
                            );
                        }
                    }

                    $this->setOrderStatus();
                    $this->order->save();
                } elseif ($this->errorCreditmemo === true) {
                    /*check to stop further execution of code in case of exception in creditmemo generation.
                    Refer function $this->generateCreditMemo()*/
                    return false;
                } else {
                    $this->rabbitMqHelper->logOrderSyncDetailed('Non creditmemo update case');
                    $this->setOrderData($customerId);
                    // Set order status
                    $this->setOrderStatus();
                    $this->order->save();
                }
            }

            // Set order status
            $this->setOrderStatus();
            $this->order->save();
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(Data::ORDER_CREATE_ERROR_LOG_FILE);
                $logger->debug(
                    __('Success: Order created/updated #%1 (%2).', $this->order->getIncrementId(), $this->order->getId())
                );
            }
        } catch (\Exception $e) {
        	if ($this->rabbitMqHelper->isLoggingEnabled()) {
            	$this->errorLogger->debug(
                __('An error occurred while creating/updating order #%1.', $messageOrderNumber)
            	);
            	$this->errorLogger->debug($e->getMessage());
            	$this->errorLogger->debug($e);
        	}
            $this->rabbitMqHelper->sendErrorEmail(
                __('Unable to create/update order for #%1', $messageOrderNumber),
                __('Order Create/Update'),
                $this->jsonSerializer->serialize($orderData)
            );
        }
    }

    private function setOrderData($customerId, $reorder = false)
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Inside setOrderData()');

        $currency = $this->priceCurrency->getCurrency()->getCurrencyCode();
        //$companyId = $this->companyManagement->getByCustomerId($customerId)->getId();
        $companyId = $this->company->getId();
        $orderData = $this->message;

        $this->order->setStoreId($this->storeId);
        $this->order->setGlobalCurrencyCode($currency)
            ->setBaseCurrencyCode($currency)
            ->setStoreCurrencyCode($currency)
            ->setOrderCurrencyCode($currency);

        $customer = $this->customerRepository->getById($customerId);
        $this->order->setCustomerId($customerId)
            //->setCustomerEmail($orderData['customer_email'])
            ->setCustomerFirstname($customer->getFirstname())
            ->setCustomerLastname($customer->getLastname())
            ->setCustomerGroupId($customer->getGroupId())
            ->setCustomerIsGuest(0);
        if (!$this->order->getId()) {
            $this->order->setCustomerEmail($customer->getEmail());
        }

        $this->order->setCompanyId($companyId);
        $this->order->setWebCompanyId($companyId);

        if (isset($orderData['source_type']) && !empty($orderData['source_type'])) {
            $this->order->setSourceType($orderData['source_type']);
        }
        if (isset($orderData['source_name']) && !empty($orderData['source_name'])) {
            $this->order->setSourceName($orderData['source_name']);
        }

        if (self::ORDER_TYPE_HEADER != strtolower((string) $orderData['order_message_type'])) {
            // Set items for the order
            $this->setItemsForOrder();
        }

        // Set billing/shipping for the order
        $this->setBillingShippingAddress($customer);

        // Set shipping method for the order
        $this->setShippingMethod();

        // Set payment method for the order
        $this->setPaymentMethod($reorder);

        // Set additional data/fields for the order
        $this->setAdditionalData($this->order);

        // Set order totals
        $this->setOrderTotals();
    }

    /**
     * Cancel the order
     * @param $seq
     */
    private function cancelOrder($seq = false)
    {
        $orderIncrementId = null;
        $this->rabbitMqHelper->logOrderSyncDetailed('Cancel order after creditmemo');
        try {
            /* @var $this->order \Magento\Sales\Model\Order */
            $orderIncrementId = $this->order->getIncrementId();
            $this->order->cancel();
            $this->order->setState(Order::STATE_CANCELED);
            $this->order->setStatus(Order::STATE_CANCELED);
            if (!$seq) {
                $sysproOrdId = $this->message['syspro_order_id'];
                $sysproOrderId = !empty(trim((string) $sysproOrdId)) ? $sysproOrdId : '';
                $this->order->setSysproOrderId($sysproOrderId);
            } else {
                $this->order->setSysproOrderId($this->order->getSysprOrderId());
            }
            $this->order->save();

            $this->errorLogger->debug(__('Order canceled successfully #%1.', $orderIncrementId));
            $this->errorLogger->debug(__('Success: Order canceled successfully #%1.', $this->order->getId()));

        } catch (\Exception $e) {
            $this->errorLogger->debug(__("An error occurred while canceling the order #$orderIncrementId."));
            $this->errorLogger->debug($e->getMessage());
        }
    }

    /**
     * Set items against order
     * @param bool $isNew
     */
    private function setItemsForOrder($isNew = false)
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Inside setItemsForOrder()');
        $orderItems = $this->message['items'] ?? '';
        if (empty($orderItems)) {
            return;
        }
        $totalQty = 0;
        $orderId = $this->order->getId();

        if ($orderId && (int)$orderId > 0) {
            if ((is_countable($this->order->getItems()) ? count($this->order->getItems()) : 0) != (is_countable($orderItems) ? count($orderItems) : 0)) {
                $this->doCancelOrder = true;
            }
        }

        //Looping on clarity msg items
        foreach ($orderItems as $item) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect(['entity_id', 'name', 'type_id', 'sku']);
            $productCollection->addFieldToFilter('sku', $item['sku']);
            $product = $productCollection->getFirstItem();
            $productNameSuffix = '';

            // If product does not exists then get the dummy product details.
            if (!$product->getId()) {
                $this->hasDummyProduct = true;
                $product = $this->getDummyProductDetails();
                $productNameSuffix = ' - ' . $item['sku'];
            }

            $productId = null;
            $productType = 'simple';
            $productName = trim((string) $item['name']);
            if ($product->getId()) {
                $productId = $product->getId();
                $productType = $product->getTypeId();
                $productName = (empty($productName)) ? $product->getName() : $productName . $productNameSuffix;
                $item['sku'] = $product->getSku();
            }

            $itemDiscountAmt = $item['discount_amount'] ?? 0;

            $itemTaxAmt = 0;
            if(isset($item['tax_amount']) && !empty($item['tax_amount'])) {
                if (is_string($item['tax_amount'])) {
                    if (!empty(trim($item['tax_amount']))) {
                        $itemTaxAmt = (int) trim($item['tax_amount']);
                    }
                } else {
                    $itemTaxAmt = $item['tax_amount'];
                }
            }

            $rowTotal = $item['row_total'] ?? $item['qty'] * $item['price'];
            $orderItem = $this->itemFactory->create();
            $orderItemId = null;
            if ($orderId && (int)$orderId > 0) {
                $orderItem = $this->getOrderItem($item);
                if (empty($orderItem)) {
                    $orderItem = $this->itemFactory->create();
                }
                if ($orderItem) {
                    $orderItemId = $orderItem->getId();
                }
            }

            //Created based on magento and clarity msg data
            $orderItem
                ->setStoreId($this->storeId)
                ->setSysproItemId($item['syspro_item_id'])
                ->setProductId($productId)
                ->setProductType($productType)
                ->setName($productName)
                ->setSku($item['sku'])
                ->setTotalQtyOrdered($item['qty'])
                ->setQtyOrdered($item['qty'])
                ->setPrice((float)$item['price'])
                ->setBasePrice((float)$item['price'])
                ->setOriginalPrice((float)$item['price'])
                ->setBaseOriginalPrice((float)$item['price'])
                ->setDiscountAmount($itemDiscountAmt)
                ->setBaseDiscountAmount($itemDiscountAmt)
                ->setTaxAmount($itemTaxAmt)
                ->setBaseTaxAmount($itemTaxAmt)
                ->setRowTotal((float)$rowTotal)
                ->setBaseRowTotal((float)$rowTotal);
            if (isset($item['avatax_nsavtx'])) {
                $orderItem->setAvataxNsavtx($item['avatax_nsavtx']);
            }
            if (isset($item['avatax_entusecodlin'])) {
                $orderItem->setAvataxEntusecodlin($item['avatax_entusecodlin']);
            }
            if (isset($item['avatax_nstkwh'])) {
                $orderItem->setAvataxNstkwh($item['avatax_nstkwh']);
            }
            if (isset($item['avatax_mscavx'])) {
                $orderItem->setAvataxMscavx($item['avatax_mscavx']);
            }

            if ($isNew) {
                $orderItem->setQtyCanceled(0)
                    ->setQtyInvoiced(0)
                    ->setQtyRefunded(0)
                    ->setQtyShipped(0)
                    ;
            }

            //Used clarity msg values to format it in magento accepted structure of 'pz_cart_properties' treatment: TRET201
            $cartProperties = [];
            foreach ($item['cart_properties'] as $cartProp) {
                $cartProperties[$cartProp['selected_option']] = $cartProp['selected_value'];
            }

            //Used same calrity msg values to format it in magento accepted structure of 'additional_options' label value
            $selectedCustomizedoptions = [];
            foreach ($cartProperties as $addedParamlabel => $addedParamValue) {
                if($addedParamlabel != 'CustomImage') {
                    $selectedCustomizedoptions[] = [
                        'label' => $addedParamlabel,
                        'value' => $addedParamValue
                    ];
                }
            }

            //Serialized for magento format
            $cartProperties = $this->jsonSerializer->serialize($cartProperties);
            $requestInfo = [
                'product' => $productId,
                'qty' => $item['qty'],
                'pz_cart_properties' => $cartProperties,
            ];

            //get previous order and current order info_buyRequest
            if ((isset($item['order_item_id']) && !empty($item['order_item_id'])) ||
                (isset($item['syspro_item_id']) && !empty($item['syspro_item_id']))) {
                $oldInfoBuyRequest = $this->getPreviousInfoBuyRequestData($item);
                $requestInfo = array_merge($requestInfo,$oldInfoBuyRequest);
            }

            $options = [];
            $options['info_buyRequest'] = $requestInfo;
            $options['additional_options'] = $selectedCustomizedoptions;
            $orderItem->setProductOptions($options);
            //$orderItem->setProductOptions(['info_buyRequest' => $requestInfo]);

            // Check and set order-item-id of existing order-item.
            if ($orderItemId) {
                $orderItem->save();
            } else {
                $this->order->addItem($orderItem);
            }

            $totalQty += $item['qty'];
            $this->order->setTotalQtyOrdered($totalQty);
        }
    }

    /**
     * Method used to get the dummy product details.
     *
     * @return null
     */
    private function getDummyProductDetails()
    {
        if (!$this->dummyProductDetails) {
            try {
                $this->dummyProductDetails = $this->productRepository->get(self::DUMMY_PRODUCT_SKU);
            } catch ( \Exception $e) {
                $this->errorLogger->debug($e);
            }
        }

        /**
         * Return the dummy product details.
         */
        return $this->dummyProductDetails;
    }

    /**
     * Method used to get the order item id, to update the existing order item.
     *
     * @param $item
     * @return mixed
     */
    /*private function getOrderItemId($item)
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Inside getOrderItemId()');
        $orderItemId = null;
        $cartOptions = [];

        //Clarity msg
        if (isset($item['cart_properties'])) {
            foreach ($item['cart_properties'] as $cartProp) {
                if (isset($cartProp['selected_option']) && isset($cartProp['selected_value'])) {
                    $cartOptions[] = [
                        'label' => $cartProp['selected_option'],
                        'value' => $cartProp['selected_value']
                    ];
                }
            }
        }
        $item['pz_cart_properties'] = $cartOptions;


        //Check if the msg properties exactly match
        foreach ($this->order->getAllItems() as $orderItem) {
            //if ($item['sku'] == $orderItem->getSku() && $item['qty'] == $orderItem->getQtyOrdered()) {
            if ($item['sku'] == $orderItem->getSku()) {

                if (isset($item['cart_properties']['CustomImage'])) {
                    unset($item['cart_properties']['CustomImage']);
                }

                //Just remove CustomImage from magento properties
                $productOptions = $orderItem->getProductOptions();
                $cartProperties = [];
                if (isset($productOptions['additional_options'])) {
                    $cartProperties   = $productOptions['additional_options'];
                    $pzCartProperties = $item['pz_cart_properties'];
                } elseif (isset($productOptions['info_buyRequest']) && isset($productOptions['info_buyRequest']['cart_properties'])) {
                    $cartProperties = $productOptions['info_buyRequest']['cart_properties'];
                    $pzCartProperties = $item['cart_properties'];
                }

                foreach ($cartProperties as $label => $value) {
                    if ((isset($value['label']) && 'CustomImage' == $value['label']) ||
                        (isset($value['selected_option']) && 'CustomImage' == $value['selected_option'])) {
                        unset($cartProperties[$label]);
                    }
                }
                foreach ($pzCartProperties as $label => $value) {
                    if ((isset($value['label']) && 'CustomImage' == $value['label']) ||
                        (isset($value['selected_option']) && 'CustomImage' == $value['selected_option'])) {
                        unset($pzCartProperties[$label]);
                    }
                }
                $hasCommonCartProperties = strcmp(
                    $this->jsonSerializer->serialize($cartProperties),
                    $this->jsonSerializer->serialize($pzCartProperties))
                ;

                $this->rabbitMqHelper->logOrderSyncDetailed('$hasCommonCartProperties = ' . $hasCommonCartProperties);
                if ($hasCommonCartProperties <= 0) {
                    $orderItemId = $orderItem->getId();
                    break;
                }
            }
        }
        return $orderItemId;
    }*/

    /**
     * Set billing/shipping address against order
     * @param $customer
     * @param bool $isNew
     */
    private function setBillingShippingAddress($customer, $isNew = false)
    {
        $orderId = $this->order->getId();
        $billingAddressId = $shippingAddressId = null;
        /*
         * If current order update message creates sequential order then
         * get billing address from oldOrder object otherwise current order object
         */
        $currentOrderObj = $this->order;
        if ($this->isSequentialOrder) {
            $currentOrderObj = $this->oldOrder;
        }
        $sourceFlag = $currentOrderObj->getSourceFlag();
        $fundCapture = $this->message['initiate_funds_capture'];
        $paymentMethod = $currentOrderObj->getPayment()->getMethod();
        if ($sourceFlag == 1 && strtolower((string) $fundCapture) !== self::INITIAL_FUND_CAPTURE_OFFLINE &&
            in_array($paymentMethod, $this->rabbitMqHelper->validPaymentMethods)) {
            /*
             * check if magento originated order i.e. sourceFlag = 1 and
             * initial_fund_capture in message is not offline and
             * order was placed using credit card and ACH
             * then get the billing address from order i.e. not updating billing address
             * getting in message.
             */
            $billingAddressData = $currentOrderObj->getBillingAddress();
        } else {
            $billingAddressData = $this->message['billing_address'];
        }

        $billingAddress = $this->getFormattedAddresses($billingAddressData, $customer);
        $billAddress = $this->addressFactory->create(['data' => $billingAddress]);
        $billAddress->setId($billingAddressId)->setAddressType(Address::TYPE_BILLING);
        $this->order->setBillingAddress($billAddress);

        $shippingAddressData = $this->message['shipping_address'];
        $shippingAddress = $this->getFormattedAddresses($shippingAddressData, $customer, self::ADDRESS_TYPE_SHIP);
        $shipAddress = $this->addressFactory->create(['data' => $shippingAddress]);
        $shipAddress->setId($shippingAddressId)->setAddressType(Address::TYPE_SHIPPING);
        $this->order->setShippingAddress($shipAddress);

        /**
         * Check, if order is already exists then get the existing billing/shipping address-id.
         */
        if ($orderId && (int)$orderId > 0) {
            $existingBillingAddress  = $this->order->getBillingAddress();
            $existingShippingAddress = $this->order->getShippingAddress();
            $billingAddressId  = $existingBillingAddress->getId();
            $shippingAddressId = $existingShippingAddress->getId();

            $billAddress->setId($billingAddressId)->save();
            $shipAddress->setId($shippingAddressId)->save();

            $this->order->setAddresses([$billAddress, $shipAddress]);
            $this->order->save();
        }

        if ($isNew) {
            $billAddress->setId(null)->save();
            $shipAddress->setId(null)->save();
        }
    }

    /**
     * Set shipping method for the order
     */
    private function setShippingMethod()
    {
        $shippingMethod = $this->getShippingMethod('update');
        $this->order
            ->setShippingMethod($shippingMethod['code'])
            ->setShippingDescription($shippingMethod['desc']);
    }

    /**
     * @return array
     */
    private function getShippingMethod($orderFlag = 'create')
    {
        $shippingMethod = '';
        if(isset($this->message['shipping_method']) && !empty($this->message['shipping_method'])) {
            $shippingMethod = $this->message['shipping_method'];
        }

        if ($shippingMethod) {
            $activeCarriers = $this->shippingConfig->getActiveCarriers();
            $allowedShippingMethods = [];
            foreach ($activeCarriers as $carrierCode => $carrierModel) {
                $carrierMethods = $carrierModel->getAllowedMethods();
                if ($carrierMethods) {
                    if (!$carrierTitle = $this->scopeConfig
                        ->getValue('carriers/'.$carrierCode.'/title')) {
                        $carrierTitle = $carrierCode;
                    }
                    if (!$carrierName = $this->scopeConfig
                        ->getValue('carriers/'.$carrierCode.'/carrier_name')) {
                        $carrierName = $carrierCode;
                    }
                    foreach ($carrierMethods as $methodCode => $method) {
                        $code = $carrierCode . '_' . $methodCode;
                        $allowedShippingMethods[$code] = ['carrier_title' => $carrierTitle, 'method' => $method, 'carrier_name' => $carrierName];
                    }
                }
            }

            $description = '';
            foreach ($allowedShippingMethods as $shippingCode => $shippingDetails) {
                if ($shippingCode == $shippingMethod) {
                    $shipMethod = '';
                    if ($shippingDetails['method']) {
                        $shipMethod = " - ".$shippingDetails['method'];
                    }
                    $description = ucfirst((string) $shippingDetails['carrier_title']).$shipMethod;
                    break;
                }
            }

            if($description) {
                $shippingMethodCode = str_replace(' ', '_', strtolower((string) $shippingMethod));
                $shippingDescription = $description ?? $shippingMethod;
            } else {
                $shippingMethodCode = '';
                $shippingDescription = '';
                if(isset($this->oldOrder) && !empty($this->oldOrder)) {
                    $shippingMethodCode = $this->oldOrder->getShippingMethod();
                    $shippingDescription = $this->oldOrder->getShippingDescription();
                }

            }

        } else {
            /**
             * If no shipping method available in ERP,
             * set flatrate(freight manual) as default method for order create
             * else set existing order shipping method and description.
             */
            if ($orderFlag == 'update') {
                $shippingMethodCode = $this->order->getShippingMethod();
                $shippingDescription = $this->order->getShippingDescription();
            } else {
                $shippingMethodCode = self::FLATRATE_SHIPPING_METHOD;
                $shippingDescription = self::FLATRATE_DESCRIPTION;
            }
        }

        return [
            'code' => $shippingMethodCode,
            'desc' => $shippingDescription
        ];
    }

    /**
     * Set payment method for the order
     */
    private function setPaymentMethod($reorder = false)
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Inside setPaymentMethod()');

        $initialFundsCapture = $this->message['initiate_funds_capture'];
        $payment = $this->message['payment'];
        $orderPayment = null;

        if (!$this->order->getId()) {
            //Note: This is not create order case it is separate method. This is creditmemo case
            $this->rabbitMqHelper->logOrderSyncDetailed('setPaymentMethod: Order id dont exists case');
            $orderPayment = $this->orderPayment->create();
            if (!isset($payment['method']) || empty(trim((string) $payment['method']))) {
                if (!isset($this->existingPaymentMethod) || empty($this->existingPaymentMethod)) {
                    $payment['method'] = Checkmo::PAYMENT_METHOD_CHECKMO_CODE;
                } else {
                    $payment['method'] = $this->existingPaymentMethod;
                }
            }
            $orderPayment->setMethod($payment['method']);
            $orderPayment->setAmountOrdered($payment['amount_ordered']);
            $orderPayment->setBaseAmountOrdered($payment['amount_ordered']);
            $orderPayment->setAmountPaid($payment['amount_paid']);
            $orderPayment->setBaseAmountPaid($payment['amount_paid']);
            $orderPayment->setTransactionId($payment['transaction_id']);
            /*if (isset($payment['tokenbase_id'])) {
                $orderPayment->setTokenbaseId($payment['tokenbase_id']);
            }*/

            if ($reorder) {
                //Order is already captured and new amount needs to be captured for new sequential order
                $tokenbase_id = $this->oldOrder->getPayment()->getTokenbaseId();
            } else {
                //New order cases
                $tokenbase_id = $this->order->getPayment()->getTokenbaseId();
            }
            if($tokenbase_id) {
                $orderPayment->setTokenbaseId($tokenbase_id);
                $payment['additional_information']['tokenbase_id'] = $tokenbase_id;
            }


            /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testlogger.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $existingAdditionalInfo = $this->order->getPayment()->getAdditionalInformation();
            $logger->info($existingAdditionalInfo);
            $logger->info($existingAdditionalInfo['amount']);
            $logger->info($payment['amount']);
            if (isset($existingAdditionalInfo['amount']) && $existingAdditionalInfo['amount'] < $payment['amount']) {
                $existingAdditionalInfo['amount'] = $payment['amount'];
                $orderPayment->setAdditionalInformation($existingAdditionalInfo);
            }
            $logger->info($existingAdditionalInfo);*/



            if (isset($payment['additional_information'])) {
                $orderPayment->setAdditionalInformation($payment['additional_information']);
            }
            /*$customerPoNo = (isset($this->message['customer_po_number']) && !empty($this->message['customer_po_number']))
                ? $this->message['customer_po_number'] : null;
            $orderPayment->setCustomerPoNumber($customerPoNo);
            $this->order->setPayment($orderPayment);*/
        } elseif (strtolower((string) $initialFundsCapture) === self::INITIAL_FUND_CAPTURE_OFFLINE) {
            $this->rabbitMqHelper->logOrderSyncDetailed('setPaymentMethod: Offline case');
			//$orderPayment = $this->orderPayment->create();
            //$orderPayment = $this->orderPayment->get($this->order->getId());
            //$this->order->getPayment()->importData(['method' => Checkmo::PAYMENT_METHOD_CHECKMO_CODE]);
            $orderPayment = $this->order->getPayment();
            $payment['method'] = Checkmo::PAYMENT_METHOD_CHECKMO_CODE;
            $orderPayment->setMethod($payment['method']);
            $orderPayment->setAmountOrdered($payment['amount_ordered']);
            $orderPayment->setBaseAmountOrdered($payment['amount_ordered']);
            $orderPayment->setAmountPaid($payment['amount_paid']);
            $orderPayment->setBaseAmountPaid($payment['amount_paid']);
            $orderPayment->setTransactionId($payment['transaction_id']);
            if (isset($payment['additional_information'])) {
                $orderPayment->setAdditionalInformation($payment['additional_information']);
            }
            /*$customerPoNo = (isset($this->message['customer_po_number']) && !empty($this->message['customer_po_number']))
                ? $this->message['customer_po_number'] : null;
            $orderPayment->setCustomerPoNumber($customerPoNo);
            $this->order->setPayment($orderPayment);*/
        } elseif (isset($payment['amount']) && !empty($payment['amount'])
            && strtolower((string) $initialFundsCapture) === self::INITIAL_FUND_CAPTURE_YES) {
            $this->rabbitMqHelper->logOrderSyncDetailed('setPaymentMethod: amount difference case');

            $orderPayment = $this->order->getPayment();
            $authorizedAmt = $orderPayment->getAmountAuthorized();
            if (isset($authorizedAmt) && !empty($authorizedAmt) && $payment['amount'] != $authorizedAmt) {
                //Exisitng authorized is different than what needs to be captured
                $this->order->getPayment()->void(new \Magento\Framework\DataObject());
                $this->order->setSysproOrderId($this->order->getSysprOrderId());
                $this->order->save();

                $this->rabbitMqHelper->logOrderSyncDetailed('setPaymentMethod: after void');
                $payment = $this->message['payment'];
                $orderPayment = $this->orderPayment->create();
                if (!isset($payment['method']) || empty(trim((string) $payment['method']))) {
                    if (!isset($this->existingPaymentMethod) || empty($this->existingPaymentMethod)) {
                        $payment['method'] = Checkmo::PAYMENT_METHOD_CHECKMO_CODE;
                    } else {
                        $payment['method'] = $this->existingPaymentMethod;
                    }
                }
                $orderPayment->setMethod($payment['method']);
                $orderPayment->setAmountOrdered($payment['amount_ordered']);
                $orderPayment->setBaseAmountOrdered($payment['amount_ordered']);
                $orderPayment->setAmountPaid($payment['amount_paid']);
                $orderPayment->setBaseAmountPaid($payment['amount_paid']);
                $orderPayment->setTransactionId($payment['transaction_id']);
                /*if (isset($payment['tokenbase_id'])) {
                    $orderPayment->setTokenbaseId($payment['tokenbase_id']);
                }*/

                if ($reorder) {
                    $tokenbase_id = $this->oldOrder->getPayment()->getTokenbaseId();
                } else {
                    $tokenbase_id = $this->order->getPayment()->getTokenbaseId();
                }
                if($tokenbase_id) {
                    $orderPayment->setTokenbaseId($tokenbase_id);
                    $payment['additional_information']['tokenbase_id'] = $tokenbase_id;
                }

                if (isset($payment['additional_information'])) {
                    $orderPayment->setAdditionalInformation($payment['additional_information']);
                }
                /*$customerPoNo = (isset($this->message['customer_po_number']) && !empty($this->message['customer_po_number']))
                    ? $this->message['customer_po_number'] : null;
                $orderPayment->setCustomerPoNumber($customerPoNo);
                $this->order->setPayment($orderPayment);*/
            }
        } elseif(strtolower((string) $initialFundsCapture) === self::INITIAL_FUND_CAPTURE_NO) {
            $orderPayment = $this->order->getPayment();
        }

        if (isset($orderPayment) && !empty($orderPayment)) {
            $customerPoNo = (isset($this->message['customer_po_number'])
                && !empty($this->message['customer_po_number']))
                ? $this->message['customer_po_number'] : null;
            $orderPayment->setCustomerPoNumber($customerPoNo);
            $this->order->setPayment($orderPayment);

        }
    }

    /**
     * Set other details for the order
     * @param $quoteOrder
     */
    private function setAdditionalData(&$quoteOrder)
    {
        $orderData = $this->message;
        $isQuickShip = $orderData['quick_ship'] ?? 0;
        $leadTimeMsg = ($isQuickShip ?
            $this->leadTimeHelper->getQuickShipLeadTimeMessage() :
            $this->leadTimeHelper->getStandardLeadTimeMessage());
        $quoteOrder->setSysproOrderId($orderData['syspro_order_id']);
        $quoteOrder->setSysproCustomerId($orderData['syspro_customer_id']);
        $quoteOrder->setQuickShip($isQuickShip);

        if ($this->orderCreateFlag == 1) {
            $quoteOrder->setSourceFlag(self::SOURCE_FLAG_SYSPRO);
        }

        // set tax and shipping amounts
        $shippingAmt = (isset($orderData['shipping_amount']) && ($orderData['shipping_amount'] !== null))
            ? (float)$orderData['shipping_amount'] : 0;
        $taxAmt = (isset($orderData['tax_amount']) && ($orderData['tax_amount'])!== null) ? $orderData['tax_amount'] : 0;
        $discountAmt = (isset($orderData['discount_amount']) && ($orderData['discount_amount']!== null))
            ? (float)$orderData['discount_amount'] : 0;
        $subTotal = (isset($orderData['subtotal']) && ($orderData['subtotal']!== null))
            ? (float)$orderData['subtotal'] : 0;
        $grandTotal = (isset($orderData['grand_total']) && ($orderData['grand_total']!== null))
            ? (float)$orderData['grand_total'] : 0;
        $PaymentMethodFee = (isset($orderData['payment_method_fee']) && ($orderData['payment_method_fee']!== null))
            ? (float)$orderData['payment_method_fee'] : 0;

        $quoteOrder->setShippingAmount($shippingAmt);
        $quoteOrder->setBaseShippingAmount($shippingAmt);
        $quoteOrder->setBaseTaxAmount($taxAmt);
        $quoteOrder->setTaxAmount($taxAmt);
        $quoteOrder->setPaymentMethodFee($PaymentMethodFee);
        $quoteOrder->setBaseDiscountAmount(-$discountAmt);
        $quoteOrder->setDiscountAmount(-$discountAmt);
        $quoteOrder->setBaseSubtotal($subTotal);
        $quoteOrder->setSubtotal($subTotal);
        $quoteOrder->setBaseGrandTotal($grandTotal);
        $quoteOrder->setGrandTotal($grandTotal);
        if (isset($orderData['coupon_code'])) {
            $quoteOrder->setCouponCode($orderData['coupon_code']);
        }
        $quoteOrder->setLeadTime($leadTimeMsg);
        $quoteOrder->setUmOrderComment($orderData['order_sidemark']);

        if (isset($orderData['syspro_order_entry_date'])) {
            $quoteOrder->setSysproOrderEntryDate($orderData['syspro_order_entry_date']);
        }
        if (isset($orderData['requested_delivery_date'])) {
            $quoteOrder->setRequestedDeliveryDate($orderData['requested_delivery_date']);
        }
        if (isset($orderData['customer_due_date'])) {
            $quoteOrder->setCustomerDueDate($orderData['customer_due_date']);
        }
        if (isset($orderData['expected_ship_date'])) {
            $quoteOrder->setExpectedShipDate($orderData['expected_ship_date']);
        }

        if (isset($orderData['created_at'])) {
            $orderDate = $orderData['created_at'];
        }
        if (!$this->order || !$this->order->getId()) {
            if (isset($orderDate) && ($orderDate !== null)) {
                $formattedOrderDate = $this->_getFormattedDate($orderDate);
                $orderCreatedDate = $this->dateTime->formatDate($formattedOrderDate, true);
                $quoteOrder->setCreatedAt($orderCreatedDate);
            }
        }

        if (isset($orderData['updated_at'])) {
            $orderUpdateDate = $orderData['updated_at'];
        }
        if (isset($orderUpdateDate) && ($orderUpdateDate !== null)) {
            $formattedOrderUpdateDate = $this->_getFormattedDate($orderUpdateDate);
            $orderUpdateDate = $this->dateTime->formatDate($formattedOrderUpdateDate, true);
            $quoteOrder->setUpdatedAt($orderUpdateDate);
        } else {
            $quoteOrder->setUpdatedAt($this->date->gmtDate());
        }

        // Check, if there is any dummy product exists in order then set the respective flag.
        if ($this->hasDummyProduct) {
            $quoteOrder->setHasDummyProduct(1);
            if ($this->order) {
                $this->order->setHasDummyProduct(1);
            } elseif ($this->_quote) {
                $this->_quote->setHasDummyProduct(1);
            }
        }
    }

    /**
     * Set order status for the order
     */
    private function setOrderStatus()
    {
        $orderStatus = $this->message['order_status'] ?? '';
        $orderStatus = $this->getStatusFromErpOrderStatus($orderStatus);
        if ($orderStatus == Order::STATE_NEW) {
            $this->order->setState($orderStatus)->setStatus('pending');
        } else {
            $this->order->setState($orderStatus)->setStatus($orderStatus);
        }
    }

    /**
     * Set totals like tax, discount, shipping, subtotal, grand total, etc
     */
    private function setOrderTotals()
    {
        $orderData = $this->message;

        // set tax and shipping amounts
        $shippingAmt = (isset($orderData['shipping_amount']) && ($orderData['shipping_amount'] !== null))
            ? (float)$orderData['shipping_amount'] : 0;
        $taxAmt = (isset($orderData['tax_amount']) && ($orderData['tax_amount'] !== null)) ? $orderData['tax_amount'] : 0;
        $discountAmt = (isset($orderData['discount_amount']) && ($orderData['discount_amount'] !== null))
            ? (float)$orderData['discount_amount'] : 0;
        $subTotal = (isset($orderData['subtotal']) && ($orderData['subtotal'] !== null))
            ? (float)$orderData['subtotal'] : 0;
        $grandTotal = (isset($orderData['grand_total']) && ($orderData['grand_total'] !== null))
            ? (float)$orderData['grand_total'] : 0;

        $orderId = $this->order->getId();

        if ($orderId && (int)$orderId > 0) {
            if ($this->order->getGrandTotal() != $grandTotal) {
                $this->doCancelOrder = true;
            }
        }

        $this->order->setShippingAmount($shippingAmt);
        $this->order->setBaseShippingAmount($shippingAmt);
        $this->order->setBaseTaxAmount($taxAmt);
        $this->order->setTaxAmount($taxAmt);
        $this->order->setBaseDiscountAmount(-$discountAmt);
        $this->order->setDiscountAmount(-$discountAmt);
        $this->order->setBaseSubtotal($subTotal);
        $this->order->setSubtotal($subTotal);
        $this->order->setBaseGrandTotal($grandTotal);
        $this->order->setGrandTotal($grandTotal);
    }

    /**
     * Get order id from erp order id
     *
     * @param $erpOrderId
     * @return int
     */
    private function getOrderFromMagento()
    {
        $orderData = $this->message;

        $order = null;
        if (isset($orderData['syspro_order_id']) && !empty(trim((string) $orderData['syspro_order_id']))) {
            $order = $this->getOrderByField('syspro_order_id', $orderData['syspro_order_id']);
            // Return false if multiple order found with same syspro_order_id
            if ($order === false) {
                return false;
            }
        }

        if (!$order && isset($orderData['web_order_id']) && !empty(trim((string) $orderData['web_order_id']))) {
            $order = $this->getOrderByField('increment_id', $orderData['web_order_id']);
        }

        if (!$order || !$order->getId()) {
            $order = $this->orderFactory->create();
        }
        return $order;
    }

    /**
     * Format address as per Magento and return
     * @param $address
     * @param $customer
     * @param string $type
     */
    private function getFormattedAddresses($address, $customer, $type = 'billing'): array
    {
        if (isset($address) && is_object($address) && $type == 'billing') {
            /*
             * If its credit card or ACH placed order and originated from magento
             * then getting address from order, in that case getting $address as an object
             */
            $formattedAddress = [
                'customer_id' => $address->getCustomerId(),
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'street'      => $address->getStreet(),
                'city'        => $address->getCity(),
                'region'      => $address->getRegion(),
                'region_id'   => $address->getRegionId(),
                'postcode'    => $address->getPostcode(),
                'country_id'  => $address->getCountryId(),
                'company' => $address->getCompany(),
                'telephone'   => $address->getTelephone(),
                'location'   => $address->getLocation(),
                'delivery_appointment'   => $address->getDeliveryAppointment(),
                'loading_dock_available'   => $address->getLoadingDockAvailable()
            ];
        } else {
            $street = (isset($address['street1']) && !empty($address['street1']))
                ? trim((string) $address['street1']) : '';
            $street .= (isset($address['street2']) && !empty($address['street2']))
                ? "\n". trim((string) $address['street2']) : '';

            $region = $this->regionFactory->create()->loadByCode($address['region_code'], $address['country_id']);

            $phone = (isset($address['telephone']) && !empty(trim((string) $address['telephone'])))
                ? $address['telephone']
                : $this->company->getTelephone();

            // Set the common fields.
            $formattedAddress = [
                'customer_id' => $customer->getId(),
                'street'      => $street,
                'city'        => trim((string) $address['city']),
                'region'      => $region->getName(),
                'region_id'   => $region->getId(),
                'postcode'    => $address['postcode'],
                'country_id'  => trim((string) $address['country_id']),
                'telephone'   => $phone
            ];

            // Set the additiona fields based on the address type.
            if (self::ADDRESS_TYPE_BILL == $type) {
                $formattedAddress['company'] = $address['company_name'];
                $formattedAddress['firstname'] = $customer->getFirstname();
                $formattedAddress['lastname']  = $customer->getLastname();
            } else {
                $formattedAddress['company'] = $address['company_name'];
                $formattedAddress['firstname'] = trim((string) $address['firstname']);
                $formattedAddress['lastname']  = trim((string) $address['lastname']);
                if (isset($address['order_shipping_notes'])) {
                    $formattedAddress['order_shipping_notes'] = $address['order_shipping_notes'];
                }
            }

            $formattedAddress['location']  = $this->getOptionIdByLabel('customer_address',
                'location',
                $address['location']);
            $formattedAddress['delivery_appointment']  = $this->getOptionIdByLabel('customer_address', 'delivery_appointment', $address['delivery_appointment']);
            $formattedAddress['loading_dock_available'] = $this->getOptionIdByLabel('customer_address',
                'loading_dock_available',
                $address['loading_dock_available']);
        }

        return $formattedAddress;
    }

    /**
     * Get order status from erp order status
     *
     * @param $erpOrderStatus
     */
    private function getStatusFromErpOrderStatus($erpOrderStatus): string
    {
        $erpOrderStatus = strtolower((string) $erpOrderStatus);
        if (in_array($erpOrderStatus, array_keys($this->sysProOrderStatuses))) {
            return $this->sysProOrderStatuses[$erpOrderStatus];
        }
        $orderStatus = match ($erpOrderStatus) {
            self::STATUS_ON_HOLD => Order::STATE_HOLDED,
            self::STATUS_PENDING => Order::STATE_NEW,
            self::STATUS_PROCESSING => Order::STATE_PROCESSING,
            self::STATUS_COMPLETE => Order::STATE_COMPLETE,
            self::STATUS_CLOSED => Order::STATE_CLOSED,
            self::STATUS_CANCELED => Order::STATE_CANCELED,
            default => Order::STATE_PROCESSING,
        };

        return $orderStatus;
    }

    /**
     * Get formatted date
     *
     * @param $date
     *
     */
    private function _getFormattedDate($date): false|int
    {
        $dateUpdated = substr((string) $date, 0, 2) . '-' . substr((string) $date, 2, 2). '-' . substr((string) $date, 4);
        $dateArray = explode("-", $dateUpdated);
        $formattedDate = $dateArray[1] . '-' . $dateArray[0] . '-' . $dateArray[2] . " 12:00:00";
        return strtotime($formattedDate);
    }

    /**
     * Initialize Order data from request
     *
     * @param $customerId
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function initializeData($customerId)
    {
        $this->_getQuote();
        $customer = $this->customerRepository->getById($customerId);
        $this->_quote->assignCustomer($customer); //Assign quote to customer
        //$this->_quote->save();
    }

    /**
     * Create new quote for shopping cart
     *
     * @return void
     */
    private function _getQuote()
    {
        try {
            $this->_quote = $this->quoteFactory->create();
            /*$cartId   = $this->cartManagementInterface->createEmptyCart(); //Create empty cart
            $this->_quote = $this->cartRepositoryInterface->get($cartId); // load empty cart quote*/
            $this->_quote->setStore($this->_getStore());
            $this->_quote->setCurrency();
        } catch (\Exception $e) {
            $this->errorLogger->debug('Quote can not be created: ' . $e->getMessage());
        }
    }

    /**
     * Retrieves store id from store code, if no store id specified,
     * it use set session or admin store
     *
     * @return object|null
     */
    private function _getStore()
    {
        try {
            return $this->storeManager->getStore();
        } catch (\Exception) {
            $this->errorLogger->debug('Can not make operation because store is not exists.');
        }

        return null;
    }

    /**
     * Add product in quote
     *
     */
    protected function setProductsData()
    {
        $errors = [];
        $orderItems = $this->message['items'];
        if (empty($orderItems)) {
            $this->errorLogger->debug('Product(s) data is not valid.');
        } else {
            $counter = 1;
            foreach ($orderItems as $item) {
                $productNameSuffix = '';
                $product = '';
                try {
                    $product = $this->productRepository->get($item['sku'], false, null, true);
                } catch (\Exception $e) {
                    // If product does not exists then get the dummy product details.
                    $this->hasDummyProduct = true;
                    $product = $this->getDummyProductDetails();
                    $productNameSuffix = ' - ' . $item['sku'];
                }
                try{
                $productId = null;
                $productType = 'simple';
                $productName = trim((string) $item['name']);
                if ($product->getId()) {
                    $productId = $product->getId();
                    $productType = $product->getTypeId();
                    $productName = (empty($productName)) ? $product->getName() : $productName . $productNameSuffix;
                    $item['sku'] = $product->getSku();
                }
                $itemTaxAmt = $item['tax_amount'] ?? 0;
                $rowTotal = $item['row_total'] ?? $item['qty'] * $item['price'];
                $itemDiscountAmt = (float) ($item['discount_amount'] ?? 0);

                $itemTaxAmt = $item['tax_amount'] ?? 0;
                $rowTotal = $item['row_total'] ?? $item['qty'] * $item['price'];
                $rowTotal = ($rowTotal - abs($itemDiscountAmt));
                $rowTotal = (floatval($rowTotal) + floatval($itemTaxAmt));
                //$orderItem = $this->itemFactory->create();
                $product
                    ->setStoreId($this->storeId)
                    ->setSysproItemId($item['syspro_item_id'])
                    ->setProductId($productId)
                    ->setProductType($productType)
                    ->setName($productName)
                    ->setSku($item['sku'])
                    ->setTotalQtyOrdered($item['qty'])
                    ->setQtyOrdered($item['qty'])
                    ->setPrice((float)$item['price'])
                    ->setBasePrice((float)$item['price'])
                    ->setOriginalPrice((float)$item['price'])
                    ->setBaseOriginalPrice((float)$item['price'])
                    ->setDiscountAmount((float)$itemDiscountAmt)
                    ->setBaseDiscountAmount((float)$itemDiscountAmt)
                    ->setTaxAmount($itemTaxAmt)
                    ->setBaseTaxAmount($itemTaxAmt)
                    ->setRowTotal((float)$rowTotal)
                    ->setBaseRowTotal((float)$rowTotal);
                if (isset($item['avatax_nsavtx'])) {
                    $product->setAvataxNsavtx($item['avatax_nsavtx']);
                }
                if (isset($item['avatax_entusecodlin'])) {
                    $product->setAvataxEntusecodlin($item['avatax_entusecodlin']);
                }
                if (isset($item['avatax_nstkwh'])) {
                    $product->setAvataxNstkwh($item['avatax_nstkwh']);
                }
                if (isset($item['avatax_mscavx'])) {
                    $product->setAvataxMscavx($item['avatax_mscavx']);
                }

                $cartProperties = [];
                foreach ($item['cart_properties'] as $cartProp) {
                    $cartProperties[$cartProp['selected_option']] = $cartProp['selected_value'];
                }

                $selectedCustomizedoptions = [];
                foreach ($cartProperties as $addedParamlabel => $addedParamValue) {
                    if($addedParamlabel != 'CustomImage') {
                        $selectedCustomizedoptions[] = [
                            'label' => $addedParamlabel,
                            'value' => $addedParamValue
                        ];
                    }
                }
                $cartProperties = $this->jsonSerializer->serialize($cartProperties);

                $product->addCustomOption('additional_options', $this->jsonSerializer->serialize($selectedCustomizedoptions));
                $product->setAdditionalData($counter);
                $requestInfo = [
                    'product' => $productId,
                    'qty' => $item['qty'],
                    'pz_cart_properties' => $cartProperties,
                    'counter' => $counter
                ];

                $request = $this->objectFactory->create();
                $request->setData($requestInfo);
                $quoteItem = null;
                try {
                    $quoteItem = $this->_quote->addProduct($product, $request);
                    $this->_quote->save();
                    $fields = [
                        'price' => (float)$item['price'],
                        'base_price' => (float)$item['price'],
                        'discount_amount' => (float)abs($itemDiscountAmt),
                        'base_discount_amount' => (float)abs($itemDiscountAmt),
                        'tax_amount' => $itemTaxAmt,
                        'base_tax_amount' => $itemTaxAmt,
                        'row_total' => (float)$rowTotal,
                        'base_row_total' => (float)$rowTotal,
                        'syspro_item_id' => $item['syspro_item_id']
                    ];
                    $this->updateQuoteItem($quoteItem->getId(), $fields);
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }  catch (\Exception $e){
                    $publishData = ['error' => $e->getMessage(), 'Data'=>$orderItems];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
                }
                $counter++;
                unset($product);
            }
        }

        if (count($errors)) {
            $this->errorLogger->debug(json_encode($errors, 1));
        }
    }

    private function updateQuoteItem($quoteItemId, $fields)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $connection->update(
                'quote_item',
                $fields,
                 'item_id = ' . $quoteItemId
            );
        } catch (\Exception $e) {
            $this->errorLogger->debug($e->getMessage());
        }
    }

    /**
     * Method used to set the address data.
     */
    private function setAddressesData()
    {
        try {
            $customer = $this->_quote->getCustomer();
            $billingAddressId = $shippingAddressId = null;
            $billingAddressData = $this->message['billing_address'];
            $billingAddress = $this->getFormattedAddresses($billingAddressData, $customer);
            $billAddress = $this->quoteAddressFactory->create();
            $billAddress->setData($billingAddress);
            $billAddress->setId($billingAddressId)->setAddressType(Address::TYPE_BILLING);
            $this->_quote->setBillingAddress($billAddress);

            $shippingAddressData = $this->message['shipping_address'];
            $shippingAddress = $this->getFormattedAddresses($shippingAddressData, $customer, self::ADDRESS_TYPE_SHIP);
            $shipAddress = $this->quoteAddressFactory->create();
            $shipAddress->setData($shippingAddress);
            $shipAddress->setId($shippingAddressId)->setAddressType(Address::TYPE_SHIPPING);
            $this->_quote->setShippingAddress($shipAddress);
            $this->_quote->save();
        } catch (\Exception $e) {
            $this->errorLogger->debug('Unable to set address data: ' . $e->getMessage());
        }
    }

    /**
     * @param $entityType
     * @param $attributeCode
     * @param $optionLabel
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOptionIdByLabel($entityType, $attributeCode, $optionLabel)
    {
        $optionId = '';

        $attribute = $this->eavConfig->getAttribute($entityType, $attributeCode);
        if ($attribute && $attribute->usesSource()) {
            foreach ($attribute->getOptions() as $option) {
                $label = $option->getLabel();
                if (strtolower((string) $optionLabel) == strtolower((string) $label)) {
                    $optionId = $option->getValue();
                    break;
                }
            }
        }

        return $optionId;
    }

    /**
     * Set an Shipping Method for Shopping Cart
     *
     */
    private function setShippingMethodData()
    {
        $shippingMethod = $this->getShippingMethod('create');
        $quoteShippingAddress = $this->_quote->getShippingAddress();

        if ($quoteShippingAddress->getId() === null) {
            $this->errorLogger->debug(__('Can not make operation because of customer shipping address is not set.'));
        } else {
            try {
                // Collect Rates and Set Shipping Method
                $this->shippingQuoteRate->setCarrier($shippingMethod['code']);
                $this->shippingQuoteRate->setCode($shippingMethod['code']);
                $this->shippingQuoteRate->setMethod($shippingMethod['code']);
                $this->shippingQuoteRate->setMethodTitle($shippingMethod['desc']);
                $this->_quote->getShippingAddress()->setCollectShippingRates(false)
                    ->collectShippingRates()
                    ->setShippingMethod($shippingMethod['code']); //shipping method
                $this->_quote->getShippingAddress()->addShippingRate($this->shippingQuoteRate);
                $this->_quote->save();
            } catch (\Exception $e) {
                $this->errorLogger->debug('Can not set shipping method.' .  $e->getMessage());
            }
        }
    }

    /**
     * Set payment method in quote
     *
     */
    private function setPaymentMethodData()
    {
        $payment = $this->message['payment'];

        //if (!isset($payment['method']) || empty(trim($payment['method']))) {
            $payment['method'] = Checkmo::PAYMENT_METHOD_CHECKMO_CODE;
        //}

        try {
            // Collect Rates and Set Payment Method
            $this->_quote->setPaymentMethod($payment['method']);
            $this->_quote->setInventoryProcessed(false);
            $this->_quote->setAmountOrdered($payment['amount_ordered']);
            $this->_quote->setBaseAmountOrdered($payment['amount_ordered']);
            $this->_quote->setAmountPaid($payment['amount_paid']);
            $this->_quote->setBaseAmountPaid($payment['amount_paid']);
            $this->_quote->setTransactionId($payment['transaction_id']);
            if (isset($payment['tokenbase_id'])) {
                $this->_quote->setTokenbaseId($payment['tokenbase_id']);
            }
            if (isset($payment['additional_information'])) {
                $this->_quote->setAdditionalInformation($payment['additional_information']);
            }
            $customerPoNo = (isset($this->message['customer_po_number']) && !empty($this->message['customer_po_number']))
                ? $this->message['customer_po_number'] : null;
            $payment['customer_po_number'] = $customerPoNo;
            $this->_quote->setCustomerPoNumber($customerPoNo);
            $this->_quote->getPayment()->importData(['method' => $payment['method']]);

            // Collect Totals & Save Quote
            //$this->_quote->collectTotals()->save();
        } catch (\Exception $e) {
            $this->errorLogger->debug('Payment method is not set.' . $e->getMessage());
        }
    }

    /**
     * Method used to process refund or generate credit memo.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generateCreditMemo()
    {
        $this->rabbitMqHelper->logOrderSyncDetailed('Creditmemo creation case');

        if (!$this->order) {
            /* @var $this->order \Magento\Sales\Model\Order */
            $this->order = $this->getOrderFromMagento();
        }
        $order = $this->order;
        $isCreditMemoGenerated = false;
        if ($order->canCreditmemo()) {
            $paymentInfo  = $order->getPayment()->getAdditionalInformation();
            $lastCcNumber = $order->getPayment()->getCcLast4();

            $incrementId = $order->getIncrementId();
            $incrementIdArray = explode('-', (string) $incrementId);
            $newIncrementId = $incrementIdArray[0];
            $invoiceJsonArray = [
                'web_order_id'       => $newIncrementId,
                'syspro_order_id'    => $order->getSysproOrderId(),
                'syspro_customer_id' => $order->getSysproCustomerId(),
                'customer_email'     => $order->getCustomerEmail(),
                'transaction_id'     => $paymentInfo['transaction_id'] ?? '',
                'transaction_type'   => $paymentInfo['transaction_type'] ?? '',
                'acc_number'         => !empty($lastCcNumber) ? $lastCcNumber : '',
                'credit_card_type'   => $paymentInfo['card_type'] ?? '',
                'amount'             => $paymentInfo['amount'] ?? '',
                'creditmemo_created' => false,
                'error_message'      => '',
                'creditmemo_number'  => '',
            ];

            try {
                $invoices = $order->getInvoiceCollection();
                $invoiceIncrementId = null;
                foreach ($invoices as $invoice) {
                    $invoiceIncrementId = $invoice->getIncrementId();
                    break;
                }

                $invoiceObj = $this->invoice->loadByIncrementId($invoiceIncrementId);
                $creditMemo = $this->creditMemoFactory->createByOrder($order);

                $creditMemo->setInvoice($invoiceObj);

                $this->creditMemoService->refund($creditMemo);

                $invoiceJsonArray['creditmemo_created'] = $isCreditMemoGenerated = true;
                $invoiceJsonArray['creditmemo_number']  = $creditMemo->getIncrementId();
            } catch (\Exception $e) {
                $payMethod = $order->getPayment()->getMethod();
                $message = '';
                if ($payMethod == AuthnetAchConfigProvider::CODE) {
                    $message = __('Cannot generate credit memo for this eCheck order #%1. ', $order->getIncrementId());
                }
                $message .= __('Unable to generate credit memo for order #%1. ', $order->getIncrementId())
                    . $e->getMessage();
                $invoiceJsonArray['error_message'] = $message;
                $this->errorLogger->debug($message);

                $publishData = ['error' => $message];
                $jsonData = $this->jsonSerializer->serialize($publishData);
                $messageJsonData = $this->jsonSerializer->serialize($this->message);
                $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$messageJsonData.'"}';
                //$publishData = ['error' => $e->getMessage(), 'message' => $message->getBody()];
                $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);

                $this->rabbitMqHelper->sendErrorEmail(
                    $message,
                    __('Credit Memo'),
                    $this->jsonSerializer->serialize($this->message)
                );
                //if error in creditmemo terminate execution
                $this->errorCreditmemo = true;
            }

            /**
             * Send the credit-memo details to SysPro only for the CC payment methods.
             */
            $paymentMethod = $order->getPayment()->getMethod();
            if (in_array($paymentMethod, $this->rabbitMqHelper->validPaymentMethods)) {
                $topic = Data::TOPIC_CREDIT_MEMO_SUCCESS_FAILURE;
                $message = $this->jsonSerializer->serialize($invoiceJsonArray);
                $this->magentoToErp->sendDataFromMagentoToERP($topic, $message);
            }
        } else {
            $this->errorLogger->debug(__('Unable to generate credit memo or it is already generated for order #%1', $order->getIncrementId()));
        }

        return $isCreditMemoGenerated;
    }

    private function prepareOrderNumber($order): array|string
    {
        $orderNumber = explode('-', (string) $order->getIncrementId());
        $orderSuffix = 1;
        if (isset($orderNumber[1]) && (int)$orderNumber[1] > 0) {
            $orderSuffix = (int)$orderNumber[1] + $orderSuffix;
        }
        $orderNumber = $orderNumber[0] . '-' . $orderSuffix;

        try {
            $order = $this->orderInterfaceFactory->create()->loadByIncrementId($orderNumber);
        } catch (\Exception) {
            $order = null;
        }

        if (!$order || !$order->getId()) {
            return $orderNumber;
        }
        $this->prepareOrderNumber($order);
    }

    /**
     * Method used to get order by field.
     *
     * @param $field
     * @param $value
     */
    private function getOrderByField($field, $value)
    {
        $this->searchCriteriaBuilder->addFilter(
            $field,
            trim((string) $value),
            'eq'
        );

        $orderData = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                //->setPageSize(1)
                ->create()
        )->getItems();

        $orderDetails = null;
        // move to error queue if multiple order found with same syspro id
        if ((is_countable($orderData) ? count($orderData) : 0) > 1) {
            $errMessage = __('Multiple order present with same syspro_order_id.');
            $messageBody = $this->jsonSerializer->serialize($this->messageArray);
            $this->moveToErrorQueue($messageBody, $errMessage);
            return false;
        }

        if ($orderData) {
            foreach ($orderData as $order) {
                $orderDetails = $order;
                break;
            }
        }

        return $orderDetails;
    }

    /**
     * Method used to get the order item to update the existing order item.
     *
     * @param $item
     * @return mixed
     */
    private function getOrderItem($item)
    {
        $orderItem = null;

        foreach ($this->order->getAllItems() as $magOrderItem) {
            if (isset($item['order_item_id']) && !empty($item['order_item_id'])
                && $magOrderItem->getId() == $item['order_item_id']) {
                $orderItem = $magOrderItem;
                break;
            } elseif (isset($item['syspro_item_id']) && !empty($item['syspro_item_id'])
                && $magOrderItem->getSysproItemId() == $item['syspro_item_id']) {
                $orderItem = $magOrderItem;
                break;
            }
        }

        return $orderItem;
    }

    /**
     * Method to check whether converting quickship to Regular or vice versa
     * @return bool
     */
    private function checkQuickShip()
    {
        $orderMessage = $this->message;
        $messageQuickShip = $orderMessage['quick_ship'] ?? 0;
        $orderData = $this->getOrderFromMagento();
        $quickShip = $orderData->getQuickShip();
        $orderQuickShip = $quickShip ?? 0;

        if ($messageQuickShip != $orderQuickShip) {
            return false;
        }
        return true;
    }

    /**
     * check if new sku added or removed or item qty increase
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkChangeInItems()
    {
        try {
            $orderItems = $this->message['items'] ?? '';
            if (empty($orderItems)) {
                return;
            }

            $oldOrder = $this->getOrderFromMagento();
            $this->order = $oldOrder;
            $orderedItems = $oldOrder->getItems();
            $orderedProdData = [];
            $messageProdData = [];

            if (isset($orderedItems) && !empty($orderedItems)) {
                foreach ($orderedItems as $orderedItem) {
                    $orderedSku = $orderedItem->getSku();
                    $orderedQty = $orderedItem->getQtyOrdered();
                    $orderedProdData[$orderedSku] = (int)$orderedQty;
                }
            }
            if (isset($orderItems) && !empty($orderItems)) {
                foreach ($orderItems as $item) {
                    $sku = $item['sku'];
                    $itemQty = $item['qty'];
                    $messageProdData[$sku] = $itemQty;

                }
            }
            if (isset($messageProdData) && !empty($messageProdData) &&
                isset($orderedProdData) && !empty($orderedProdData)) {
                // To check Item Add/Remove/Qty change
                if ($messageProdData != $orderedProdData) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\LogicException $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::ORDER_CREATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $this->message);
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->message.'"}';
            //$publishData = ['error' => $e->getMessage(), 'message' => $message->getBody()];
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Order Create Update'),
                $this->message
            );
        }
    }

    /**
     * Method used to get all old order or current order product options.
     * @param $item
     * @return array
     */
    private function getPreviousInfoBuyRequestData($item)
    {
        $oldOrderItem = null;
        $infoBuyRequestProperty = [];
        if (isset($this->oldOrder) && !empty($this->oldOrder)) {
            $order = $this->oldOrder;
        } else {
            $order = $this->order;
        }
        foreach ($order->getAllItems() as $magOldOrderItem) {
            if (isset($item['order_item_id']) && !empty($item['order_item_id'])
                && $magOldOrderItem->getId() == $item['order_item_id']) {
                $oldOrderItem = $magOldOrderItem;
                break;
            } elseif (isset($item['syspro_item_id']) && !empty($item['syspro_item_id'])
                && $magOldOrderItem->getSysproItemId() == $item['syspro_item_id']) {
                $oldOrderItem = $magOldOrderItem;
                break;
            }
        }
        if(isset($oldOrderItem)) {
            $productOptions = $oldOrderItem->getProductOptions();
            if (isset($productOptions) && isset($productOptions['info_buyRequest'])) {
                $infoByRequest = $productOptions['info_buyRequest'];
                foreach ($infoByRequest as $oldParamKey => $oldParamValue) {
                    if ($oldParamKey == 'product' || $oldParamKey == 'qty' ||
                        $oldParamKey == 'pz_cart_properties') {
                        continue;
                    }
                    $infoBuyRequestProperty[$oldParamKey] = $oldParamValue;
                }
            }
        }
        return $infoBuyRequestProperty;
    }

    /**
     * Move to error queue.
     * @param $messageBody
     * @param $errMessage
     * @return void
     */
    private function moveToErrorQueue($messageBody, $errMessage)
    {
        try {
            $this->errorLogger->debug($errMessage);
            $publishData = ['error' => $errMessage];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'. $messageBody.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_ORDER_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $errMessage,
                __('Order Create/Update'),
                $this->jsonSerializer->serialize($this->message)
            );
        } catch (\Exception $e) {
            $this->errorLogger->debug('Unable to send message to error queue: ' . $e->getMessage());
            $this->errorLogger->debug($messageBody);
        }
    }
}

