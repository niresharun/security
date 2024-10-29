<?php
/**
 * Create new order from magento to syspro
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Perficient\Rabbitmq\Helper\Data as RabbitMQHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Perficient\Rabbitmq\Model\MagentoToErp;
use Perficient\Productimize\Model\ConfigOptions;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Config As EavConfig;
use Perficient\Catalog\Helper\Data;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class CreateOrderMagentoToSyspro
 * @package Perficient\Rabbitmq\Observer
 */
class CreateOrderMagentoToSyspro implements ObserverInterface
{
    /**
     * Constant for alternate shipping method.
     */
    const ALTERNATE_SHIPPING_METHOD = 'Shipping TBD';

    const CONTACTINFO = 'contactInfo';

    /**
     * Valid available shipping methods.
     * @var array
     */
    private array $validShippingMethods = [
        'flatrate',
        'blueship',
        'ups'
    ];

    /**
     * @var string
     */
    private $orderComment;

    /**
     * @var array
     */
    private array $duplicateField = [
        'location',
        'loadingDock',
        'contactInfo_notes',
    ];

    /**
     * CreateOrderMagentoToSyspro constructor.
     * @param Json $jsonSerializer
     * @param TimezoneInterface $dateTime
     * @param ConfigOptions $customizerConfigOption
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly RabbitMQHelper $rabbitMqHelper,
        private readonly Json $jsonSerializer,
        private readonly TimezoneInterface $dateTime,
        private readonly MagentoToErp $magentoToErp,
        private readonly ConfigOptions $customizerConfigOption,
        private readonly ProductRepositoryInterface $productRepository,
        protected EavConfig $config,
        private readonly Data $catalogHelperData,
    )
    {
    }

    /**
     * Method used to publish the order data from Magento to SysPro
     * through RabbitMQ after placing the order.
     *
     * @param Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $this->sendOrderToSysPro($order);
    }

    /**
     * @param $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendOrderToSysPro($order)
    {
        /**
         * Retrieve the Customer and ERP Customer Id value.
         */
        $customerId = $order->getCustomerId();
        $customer = $this->rabbitMqHelper->getCustomerById($order->getCustomerId());

        /**
         * Send order data from Magento to SysPro ERP
         */
        try {
            $this->sendOrderDataFromMagentoToSyspro(
                RabbitMQHelper::OPERATION_CREATE,
                $customerId,
                $customer,
                $order
            );
        } catch (\Exception $e) {
            $erroMessage  = __("Publish order message failed due to error : ");
            $erroMessage .= $e->getMessage();
            $this->rabbitMqHelper->logRabbitmqOrderErrorMessages(
                "Magento Order Id ".$order->getIncrementId(),
                $e->getMessage()."::".$order->getIncrementId(),
                $erroMessage
            );
        }
    }

    /**
     * Send Order Data From Magento to Syspro
     * @param $operation
     * @param $customerId
     * @param $customer
     * @param $order
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendOrderDataFromMagentoToSyspro(
        $operation,
        $customerId,
        $customer,
        $order
    ): void
    {
        $message = null;
        try {
            $orderJsonArray = [];
            $orderJsonArray['operation'] = $operation;
            $shippingMethod = $order->getShippingMethod();
            /**
             * If it is an CRM order then pass the alternate shipping method
             */
            $uuid = $order->getUuid();
            if (!empty($uuid)) {
                $shippingMethod = self::ALTERNATE_SHIPPING_METHOD;
            }

            /**
             * WENDM2-2341 - To convert ordersidemark json array coming from crm to string
             */
            $this->orderComment = $this->getOrderSideMark($order->getUmOrderComment());

            /**
             * WENDM2-2346 - PO Number From App Orders Not Syncing to Syspro
             */
            $customerPoNumber = $order->getPayment()->getData('customer_po_number');
            if (isset($customerPoNumber) && !empty($customerPoNumber)) {
                $poNumber = $customerPoNumber;
            } else {
                $poNumber = $order->getPayment()->getData('po_number');
            }

            $orderJsonArray['data'][] = [
                'web_order_id' => $order->getIncrementId(),
                'syspro_order_id' => '',
                'syspro_customer_id' => $order->getSysproCustomerId(),
                'syspro_salesrep_id' => $this->getCustomerCustAttr($customer, 'sales_rep_name'),
                'customer_email' => $order->getCustomerEmail(),
                'created_at' => $this->dateTime->date($order->getCreatedAt())->format('Y-m-d H:i:s'),
                'updated_at' => $this->dateTime->date($order->getUpdatedAt())->format('Y-m-d H:i:s'),
                'quick_ship' => $order->getQuickShip() ?? '',
                'source_type' => $order->getSourceType() ?? '',
                'source_name' => $order->getSourceName() ?? '',
                'order_status' => $order->getStatus(),
                'coupon_code' => $order->getCouponCode(),
                'discount_amount' => (float) $order->getDiscountAmount(),
                'payment_method' => $order->getPayment()->getMethod(),
                'customer_po_number' => $poNumber ?? '',
                'shipping_method' => $shippingMethod,
                'shipping_amount_without_upcharge' => (float) $order->getShippingAmountWithoutDiscount(),
                'shipping_amount' => (float) $order->getShippingAmount(),
                'tax_amount' => (float) $order->getTaxAmount(),
                'payment_method_fee'=> (float) $order->getPaymentMethodFee(),
                'subtotal' => (float) $order->getSubtotal(),
                'grand_total' => (float) $order->getGrandTotal(),
                'lead_time' => $order->getLeadTime(),
                'order_sidemark' => $this->orderComment,
                'shipping_address' => $this->getOrderShippingAddressData($order),
                'items' => $this->getOrderItemsData($order->getAllItems()),
                'shipments' => [],
                'payment' => $this->getOrderPaymentDetails($order)
        ];

        $topic = RabbitMQHelper::TOPIC_MAGENTO_ORDER_CREATE;
        $message = $this->jsonSerializer->serialize($orderJsonArray);
        
        $this->magentoToErp->sendDataFromMagentoToERP($topic, $message);
		} catch (\LogicException $e) {
			if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    RabbitMQHelper::ORDER_CREATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage());
                $logger->debug($message);
            }
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Create Order '),
                $orderJsonArray
            );
            $publishData = ['error' => $e->getMessage(), 'message' => $orderJsonArray];
            $this->rabbitMqHelper->publishErrMessage(RabbitMQHelper::ERR_TOPIC_MAGENTO_ORDER_CREATE, $this->jsonSerializer->serialize($publishData));
        } catch (\Exception $e) {
			if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    RabbitMQHelper::ORDER_CREATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage());
                $logger->debug($message);
            }
			$publishData = ['error' => $e->getMessage(), 'message' => $orderJsonArray];
            $this->rabbitMqHelper->publishErrMessage(RabbitMQHelper::ERR_TOPIC_MAGENTO_ORDER_CREATE, $this->jsonSerializer->serialize($publishData));
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Create Order '),
                $orderJsonArray
            );
         }
    }

    /**
     * Get Order Items Data
     * @param $items
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOrderItemsData($items): array
    {
        $orderItems = [];
        foreach ($items as $item) {
            if ($item->getProductType() === Configurable::TYPE_CODE) {
                continue;
            }
            $name = $item->getName();
            if ($parentItem = $item->getParentItem()) {
                $name = $item->getName();
                $item = $parentItem;
            }
            $productOptions = $item->getProductOptions();
            if (is_string($productOptions)) {
                $productOptionsArray = $this->jsonSerializer->unserialize($productOptions);
            } else {
                $productOptionsArray = $productOptions;
            }
            $cartProperties = [];
            $pzCartProperties =
            $productOptionsArray['info_buyRequest']['pz_cart_properties'] ?? '';
            if (isset($pzCartProperties) && !empty($pzCartProperties)) {
                $decodePzCartProperties =  $this->jsonSerializer->unserialize($pzCartProperties);
                foreach ($decodePzCartProperties as $key => $value) {
                    $cartProperties[] = [
                        'selected_option' => $key,
                        'selected_value' => $value
                    ];
                }
            }

            /**
             * feature/WENDM2-1882-syspro-integration-testing-orders
             * Commented the below code to pass only cart-properties in proper format.
             */
            // Get configuration options.
            /*$product = $this->productRepository->get($item->getSku());
            $configurationOptions = $this->jsonSerializer->unserialize(
                $this->customizerConfigOption->getSelectedConfigurationOptions(
                    $item->getId(),
                    $product->getDefaultConfigurations()
                )
            );*/

            // If cart properties exist, merge them with the missing configuration options
            /*if (isset($decodePzCartProperties)) {
                $missingProperties = $this->buildMissingCartPropertiesArray($configurationOptions, $decodePzCartProperties);
                $mergedArray = array_merge($decodePzCartProperties,$missingProperties);
            } else {
                $mergedArray = $cartProperties;
            }*/            

            $itemData = [];
            $itemData['order_item_id'] = $item->getId();
            $itemData['syspro_item_id'] = $item->getSysproItemId();
            $itemData['sku'] = $item->getSku();
            $itemData['name'] = $name;
            $itemData['qty'] = (int) $item->getQtyOrdered();
            $itemData['price'] = (float) number_format((float)str_replace(',', '', (string) $item->getPrice()), 2, '.', '');
            $itemData['discount_amount'] = (float) $item->getDiscountAmount();
            $itemData['tax_amount'] = (float) $item->getTaxAmount();
            $itemData['row_total'] = (float) $item->getRowTotal();
            $itemData['cart_properties'] = $cartProperties;

            // Get parent product sku;
            if ($item->getProductType() === Configurable::TYPE_CODE) {
                $parentProduct = $this->catalogHelperData->getProductById($item->getProductId());
                if (!empty($parentProduct)) {
                    $itemData['parent_sku'] = $parentProduct->getSku();
                }
            }

            $orderItems[] = $itemData;
        }

        return $orderItems;
    }

    /**
     * Loops through decoded cart properties.
     * Format and cleanse options. Check if option
     * is currently present in decode cart properties, if not add and merge into single array
     * @param $optionArray
     * @param null $decodePzCartProperties
     * @return array
     */
    private function buildMissingCartPropertiesArray($optionArray, $decodePzCartProperties = null)
    {
        $retVal = [];
        // If $decodePzCartProperties exists/has been set, check it for existing properties
        if ($decodePzCartProperties) {
            foreach($optionArray as $key => $val) {
                // Formatting the option to prevent duplicate entry
                $str = $this->cleanString($key);
                $str = ucwords($str, ' ');
                if (array_search($str, array_column($decodePzCartProperties, 'selected_option')) === false) {
                    $retVal[] = [
                        'selected_option' => $str,
                        'selected_value' => $val
                    ];
                }

            }
        } else {
            foreach($optionArray as $key => $val) {
                // Formatting the option to prevent duplicate entry
                $str = $this->cleanString($key);
                $str = ucwords($str, ' ');

                $retVal[] = [
                    'selected_option' => $str,
                    'selected_value' => $val
                ];
            }
        }
        return $retVal;
    }

    /**
     * Get a custom customer attribute. Return value if set
     * else return empty string.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $attrName
     * @return string $retVal
     */
    private function getCustomerCustAttr($customer, $attrName)
    {
        $retVal   = '';
        $custAttr = $customer->getCustomAttribute($attrName);
        // If the attribute has been set, get the value
        if ($custAttr) {
            $retVal = $custAttr->getValue();
        }

        return $retVal;
    }

    /**
     * Loop through character sets that need to be removed from the
     * provided string/key
     * @param $string
     * @return string
     */
    private function cleanString($string) {
        $retVal = $string;
        $cleansingRules = [
          '_sku' => '',
            '_' => ' '
        ];

        foreach ($cleansingRules as $key => $value) {
            $retVal = str_replace($key, $value, (string) $retVal);
        }

        return $retVal;
    }

    /**
     * Get Order Billing Address Data
     * @param $order
     *
     */
    private function getOrderBillingAddressData($order): array
    {
        $address = [];
        $billingAddress = $order->getBillingAddress();
        $billingStreet = $billingAddress->getStreet();

        // get attribute source for getting option text
        $attributeLocation = $this->getCustomerAddressAttribute('location');
        $attributeDockAvail = $this->getCustomerAddressAttribute('loading_dock_available');
        $attributeDeliveryApp = $this->getCustomerAddressAttribute('delivery_appointment');

        $address['region'] = $billingAddress->getRegion();
        $address['region_id'] = $billingAddress->getRegionId();
        $address['region_code'] = $billingAddress->getRegionCode();
        $address['country_id'] = $billingAddress->getCountryId();
        $address['street'] = $billingStreet;
        $address['street1'] = $billingStreet[0];
        $address['street2'] = ($billingStreet[1] ?? '');
        $address['postcode'] = $billingAddress->getPostcode();
        $address['city'] = $billingAddress->getCity();
        $address['telephone'] = $billingAddress->getTelephone();
        $address['firstname'] = $billingAddress->getFirstname();
        $address['lastname'] = $billingAddress->getLastname();
        $address['receiving_hours'] = $billingAddress->getReceivingHours();
        $address['receiver_telephone'] = $billingAddress->getReceiverTelephone();
        $address['receiver_name'] = $billingAddress->getReceiverName();
        $address['order_shipping_notes'] = $billingAddress->getOrderShippingNotes();
        $address['location'] = $attributeLocation->getSource()->getOptionText($billingAddress->getLocation());
        $address['delivery_appointment'] = $attributeDeliveryApp->getSource()->getOptionText($billingAddress->getDeliveryAppointment());
        $address['loading_dock_available'] = $attributeDockAvail->getSource()->getOptionText($billingAddress->getLoadingDockAvailable());

        return $address;
    }

    /**
     * Get Order Shipping Address Data
     * @param $order
     *
     */
    private function getOrderShippingAddressData($order): array
    {
        $address = [];
        $shippingAddress = $order->getShippingAddress();
        $shippingStreet = $shippingAddress->getStreet();

        // get attribute source for getting option text
        $attributeLocation = $this->getCustomerAddressAttribute('location');
        $attributeDockAvail = $this->getCustomerAddressAttribute('loading_dock_available');
        $attributeDeliveryApp = $this->getCustomerAddressAttribute('delivery_appointment');
        $companyName = $shippingAddress->getCompany();
        $address['company_name'] = !empty($companyName) ? $companyName : '';
        $address['region_code'] = $shippingAddress->getRegionCode();
        $address['country_id'] = $shippingAddress->getCountryId();
        $address['street1'] = $shippingStreet[0];
        $address['street2'] = (isset($shippingStreet[1])
            ? (isset($shippingStreet[2]) ? implode(' ', [$shippingStreet[1], $shippingStreet[2]]) : $shippingStreet[1])
            : '');
        $address['postcode'] = $shippingAddress->getPostcode();
        $address['city'] = $shippingAddress->getCity();
        $address['telephone'] = $shippingAddress->getTelephone();
        $address['firstname'] = $shippingAddress->getFirstname();
        $address['lastname'] = $shippingAddress->getLastname();
        $address['order_shipping_notes'] = $shippingAddress->getOrderShippingNotes();
        $address['location'] = $attributeLocation->getSource()->getOptionText($shippingAddress->getLocation());
        $address['delivery_appointment'] = $attributeDeliveryApp->getSource()->getOptionText($shippingAddress->getDeliveryAppointment());
        $address['loading_dock_available'] = $attributeDockAvail->getSource()->getOptionText($shippingAddress->getLoadingDockAvailable());

        return $address;
    }

    /**
     * Get Order Payment Details
     * @param $order
     *
     */
    private function getOrderPaymentDetails($order): array
    {
        $paymentMethod = [];
        $payment = $order->getPayment();
        $paymentMethod['method'] = $payment->getMethod();
        $paymentMethod['transaction_id'] = $payment->getData('last_trans_id');
        if (is_string($payment->getData('additional_information'))) {
            $additionalOptions = $this->jsonSerializer->unserialize($payment->getData('additional_information'));
        } else {
            $additionalOptions = $payment->getData('additional_information');
        }
        $paymentMethod['additional_information'] = $additionalOptions;
        $paymentMethod['tokenbase_id'] = $payment->getData('tokenbase_id');
        $paymentMethod['amount_ordered'] = (float) $order->getData('grand_total');
        $paymentMethod['amount_paid'] = (float) $order->getData('total_paid');

        return $paymentMethod;
    }

    /**
     * @param $attributeCode
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerAddressAttribute($attributeCode)
    {
        $attributeSource = $this->config->getAttribute('customer_address', $attributeCode);
        return $attributeSource;

    }

    /**To get given string is JSON or not
     * @param $string
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStringIsJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    /**To get Order sidemark
     * @param $orderComment
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOrderSideMark($orderComment)
    {
        if ($this->getStringIsJson($orderComment)) {
            $orderCommentArr = $this->jsonSerializer->unserialize($orderComment);
            if (is_array($orderCommentArr) && !empty($orderCommentArr)) {
                $comments = '';
                if (isset($orderCommentArr['contactInfo']) && is_array($orderCommentArr['contactInfo'])
                    && !empty($orderCommentArr['contactInfo'])) {
                    foreach ($orderCommentArr['contactInfo'] as $infoKey => $infoValue) {
                        if (!empty($infoValue)) {
                            if (is_array($infoValue)) {
                                foreach ($infoValue as $key => $value) {
                                    $contactInfoKey = self::CONTACTINFO . "_" . $infoKey . "_" . $key;
                                    $orderCommentArr[$contactInfoKey] = $value;
                                }
                            } else {
                                $contactInfoKey = self::CONTACTINFO . "_" . $infoKey;
                                $orderCommentArr[$contactInfoKey] = $infoValue;
                            }
                        }
                    }
                    unset($orderCommentArr['contactInfo']);
                }
                foreach ($this->duplicateField as $data) {
                    unset($orderCommentArr[$data]);
                }

                foreach ($orderCommentArr as $key => $value) {
                    $comments .= $key . ":" . $value . "||";
                }
                $this->orderComment = $comments;
            }
        } else {
            $this->orderComment = $orderComment;
        }

        return $this->orderComment;
    }
}
