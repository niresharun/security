<?php
/**
 * Rabbitmq helper
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Store\Model\Store;
use Magento\Framework\DataObject;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\DriverInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Magento\Framework\Logger\Monolog as MonologLogger;
use Magento\Framework\Exception\MailException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Data
 * @package Perficient\Rabbitmq\Helper
 */
class Data extends AbstractHelper
{
    const WEBSITE_CODE = 'base';

    const B2B_STORE_CODE = 'default';

    const OPERATION_CREATE = 'create';

    const OPERATION_UPDATE = 'update';

    const PRICE_QUANTITY = 1;

    const QUEUE_PRODUCT_CREATE_UPDATE = 'queue.erp.catalog.product.create.update';

    const TOPIC_PRODUCT_CREATE_UPDATE = 'erp.catalog.product.create.update';

    const ERR_QUEUE_PRODUCT_CREATE_UPDATE = 'queue.error.erp.catalog.product.create.update';

    const ERR_TOPIC_PRODUCT_CREATE_UPDATE = 'error.erp.catalog.product.create.update';

    const TOPIC_BASE_COST_CREATE_UPDATE = 'erp.base.cost.create.update';

	const QUEUE_BASE_COST_CREATE_UPDATE = 'queue.erp.base.cost.create.update';

    const ERR_TOPIC_BASE_COST_CREATE_UPDATE = 'error.erp.base.cost.create.update';

    const ERR_QUEUE_BASE_COST_CREATE_UPDATE = 'queue.error.erp.base.cost.create.update';

    const TOPIC_MEDIA_TREATMENT_CREATE_UPDATE = 'erp.media.treatment.create.update';

	const QUEUE_MEDIA_TREATMENT_CREATE_UPDATE = 'queue.erp.media.treatment.create.update';

    const ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE = 'error.erp.media.treatment.create.update';

    const ERR_QUEUE_MEDIA_TREATMENT_CREATE_UPDATE = 'queue.error.erp.media.treatment.create.update';

    const QUEUE_PRODUCT_INVENTORY_UPDATE = 'queue.erp.catalog.inventory.update';

    const TOPIC_PRODUCT_INVENTORY_UPDATE = 'erp.catalog.inventory.update';

    const ERR_QUEUE_PRODUCT_INVENTORY_UPDATE = 'queue.error.erp.catalog.inventory.update';

    const ERR_TOPIC_PRODUCT_INVENTORY_UPDATE = 'error.erp.catalog.inventory.update';

    const CATALOG_PRICE_UPDATE_ERROR_LOG_FILE = 'catalog_price_update_error.log';

    const CATALOG_INVENTORY_UPDATE_ERROR_LOG_FILE = 'catalog_inventory_update_error.log';

    const CATALOG_INVENTORY_UPDATE_REQUEUE_LOG_FILE = 'catalog_inventory_update_requeue.log';

    const CATALOG_PRODUCT_REQUEUE_LOG_FILE = 'catalog_product_create_update_requeue.log';

    const CATALOG_PRODUCT_CREATE_UPDATE_SUCCESS_LOG_FILE = 'catalog_product_create_update_success.log';

    const CUSTOM_LOG_IS_ENABLED = 'rabbitmq/general/custom_log_for_incoming_messages';

    const CATALOG_PRODUCT_CREATE_UPDATE_ERROR_LOG_FILE = 'catalog_product_create_update_error.log';

    const BASE_COST_UPDATE_ERROR_LOG_FILE = 'base_cost_create_update_error.log';

    const BASE_MEDIA_TREATMENT_ERROR_LOG_FILE = 'media_treatment_create_update_error.log';

    const MEDIA_ERROR_LOG_FILE = 'media_treatment_create_update_error.log';

    const QUEUE_MAGENTO_ORDER_CREATE = 'queue.magento.sales.order.create';

    const TOPIC_MAGENTO_ORDER_CREATE = 'magento.sales.order.create';

    const ERR_QUEUE_MAGENTO_ORDER_CREATE = 'queue.error.magento.sales.order.create';

    const ERR_TOPIC_MAGENTO_ORDER_CREATE = 'error.magento.sales.order.create';

    const MAGENTO_ORDER_PUBLISH_ERROR_LOG_FILE = 'magento_order_publish_error.log';

    const TOPIC_MEDIA_FLAT_TABLE_CREATE_UPDATE = 'erp.media.flat.table.create.update';

    const QUEUE_MEDIA_FLAT_TABLE_CREATE_UPDATE = 'queue.erp.media.flat.table.create.update';

    const ERR_TOPIC_MEDIA_FLAT_TABLE_CREATE_UPDATE = 'error.erp.media.flat.table.create.update';

    const ERR_QUEUE_MEDIA_FLAT_TABLE_CREATE_UPDATE = 'queue.error.erp.media.flat.table.create.update';

    const MEDIA_FLAT_TABLE_ERROR_LOG_FILE = 'media_flat_table_create_update_error.log';

    const TOPIC_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'erp.treatment.flat.table.create.update';

    const QUEUE_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'queue.erp.treatment.flat.table.create.update';

    const ERR_TOPIC_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'error.erp.treatment.flat.table.create.update';

    const ERR_QUEUE_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'queue.error.erp.treatment.flat.table.create.update';

    const TREATMENT_FLAT_TABLE_ERROR_LOG_FILE = 'treatment_flat_table_create_update_error.log';

    const TOPIC_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'erp.media.treatment.flat.table.create.update';

    const QUEUE_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'queue.erp.media.treatment.flat.table.create.update';

    const ERR_TOPIC_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'error.erp.media.treatment.flat.table.create.update';

    const ERR_QUEUE_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'queue.error.erp.media.treatment.flat.table.create.update';

    const MEDIA_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE = 'media_treatment_flat_table_create_update_error.log';

    const TOPIC_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'erp.frame.treatment.flat.table.create.update';

    const QUEUE_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'queue.erp.frame.treatment.flat.table.create.update';

    const ERR_TOPIC_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'error.erp.frame.treatment.flat.table.create.update';

    const ERR_QUEUE_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE = 'queue.error.erp.frame.treatment.flat.table.create.update';

    const FRAME_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE = 'frame_treatment_flat_table_create_update_error.log';

    /**
     * Constants for crate order from Syspro to Magento
     */
    const TOPIC_ORDER_CREATE_UPDATE = 'erp.order.create.update';
    const QUEUE_ORDER_CREATE_UPDATE = 'queue.erp.order.create.update';
    const ORDER_CREATE_ERROR_LOG_FILE = 'order_create_update_error.log';
    const ERR_TOPIC_ORDER_CREATE_UPDATE = 'error.erp.order.create.update';
    const ERR_QUEUE_ORDER_CREATE_UPDATE = 'queue.error.erp.order.create.update';

    /**
     * Constants for create invoice/shipment from Syspro to Magento
     */
    const TOPIC_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE   = 'erp.invoice.shipment.create.update';
    const QUEUE_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE   = 'queue.erp.invoice.shipment.create.update';
    const ERR_TOPIC_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE = 'error.erp.invoice.shipment.create.update';
    const ERR_QUEUE_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE = 'queue.error.erp.invoice.shipment.create.update';
    const ORDER_INVOICE_SHIPMENT_CREATE_ERROR_LOG_FILE = 'invoice_shipment_create_update.log';
    const QUEUE_INVOICE_SUCCESS_FAILURE = 'queue.invoice.success.failure';
    const TOPIC_INVOICE_SUCCESS_FAILURE = 'invoice.success.failure';
    const ERR_QUEUE_INVOICE_SUCCESS_FAILURE = 'queue.error.invoice.success.failure';
    const ERR_TOPIC_INVOICE_SUCCESS_FAILURE = 'error.invoice.success.failure';
    const QUEUE_CREDIT_MEMO_SUCCESS_FAILURE = 'queue.creditmemo.success.failure';
    const TOPIC_CREDIT_MEMO_SUCCESS_FAILURE = 'creditmemo.success.failure';

    /**
     * Constants for update company from Syspro to Magento
     */
    const TOPIC_COMPANY_UPDATE          = 'erp.company.update';
    const QUEUE_COMPANY_UPDATE          = 'queue.erp.company.update';
    const COMPANY_UPDATE_ERROR_LOG_FILE = 'company_update.log';
    const CRM_PRICE_MULTIPLIER_LOG = 'crm_price_multiplier.log';
    const ERR_TOPIC_COMPANY_UPDATE = 'error.erp.company.update';
    const ERR_QUEUE_COMPANY_UPDATE = 'queue.error.erp.company.update';

    /**
     * Constants for update company from Magento to Syspro
     */
    const TOPIC_MAGENTO_COMPANY_UPDATE = 'magento.company.update';
    const QUEUE_MAGENTO_COMPANY_UPDATE = 'queue.magento.company.update';
    const ERR_TOPIC_MAGENTO_COMPANY_UPDATE = 'error.magento.company.update';
    const ERR_QUEUE_MAGENTO_COMPANY_UPDATE = 'queue.error.magento.company.update';

    /**
     * Constant log message file
     */
    const RABBITMQ_MESSAGE_LOG_FILE = 'rabbitmq_queue_message.log';

    /**
     * Constant log message file
     */
    const RABBITMQ_PUBLISHED_MESSAGE_LOG_FILE = 'rabbitmq_queue_published_message.log';

    /**
     * Log File Path
     */
    const LOG_FILE_PATH = '/var/log/';

    /**
     * Enable emails
     */
    const XML_PATH_ENABLE_EMAIL_NOTIFICATION = 'rabbitmq/general/enable_email_notification';

    /**
     *Enable logging
     */
    const XML_PATH_ENABLE_LOGGING = 'rabbitmq/general/enable_logging_in_file';

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'rabbitmq/general/recipient_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'rabbitmq/general/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'rabbitmq/general/email_template';

	/**
     * Enable incoming messages logging
     */
    const XML_PATH_ENABLED_INCOMING_MESSAGES_LOGGING = 'rabbitmq/general/enable_logging_for_incoming_messages';

	/**
     * Enable outgoing messages logging
     */
    const XML_PATH_ENABLED_OUTGOING_MESSAGES_LOGGING = 'rabbitmq/general/enable_logging_for_outgoing_messages';

    /**
     * Enable order sync detailed log
     */
    const XML_ENABLE_ORDER_SYNC_DETAILED_LOG = 'rabbitmq/general/enable_order_sync_detailed_log';

    /**
     * Order sync detailed log file name
     */
    const XML_ENABLE_ORDER_SYNC_DETAILED_LOG_FILE = 'order_sync_detailed.log';

    /**
     * COnfig relative path for product images
     */
    const XML_PATH_PRODUCT_IMAGES_PATH = 'rabbitmq/product/images_path';

    /**
     * Constant for customer-id
     */
    const CUSTOMER_ID_ATTR_CODE = 'customer_id';

    public array $validPaymentMethods = [
        'authnetcim',
        'authnetcim_ach',
    ];

    /**
     * Data constructor.
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param DataObject $dataObject
     * @param StoreManagerInterface $storeManagerInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        private readonly TransportBuilder $transportBuilder,
        private readonly DataObject $dataObject,
        private readonly StoreManagerInterface $storeManagerInterface,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly WebsiteRepositoryInterface $websiteRepository,
        private readonly \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        private readonly Json $jsonSerializer,
        private readonly DriverInterface $fileSystem
    )
    {
        parent::__construct($context);
    }

    public function cleanString($text) {
        $text= str_replace('u200b', '', (string) $text);
        $utf8 = [
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            // UTF-8 hyphen to "normal" hyphen
            '/–/'           =>   '-',
            // Literally a single quote
            '/[’‘‹›‚]/u'    =>   ' ',
            // Double quote
            '/[“”«»„]/u'    =>   ' ',
            // nonbreaking space (equiv. to 0x160)
            '/ /'           =>   ' ',
        ];
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }

    /**
     * @param $errorMessage
     * @param $title
     * @param $message
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendErrorEmail($errorMessage, $title, $message)
    {
        if (!$this->isEmailNotificationEnabled()) {
            return true;
        }

        try {
            $messageArray = [];
            $storeUrl = $this->storeManagerInterface->getStore()->getBaseUrl();
            $messageArray['error'] = "Error: " . $errorMessage;
            $messageArray['title'] = "Error occurred while: " . $title;
            $messageArray['subject'] = "RabbitMQ Error $storeUrl :" . $title;
            $message = stripslashes(stripslashes((string) $message));
            $message = $this->cleanString($message);
            $messageArray['message'] = $message;
            $this->dataObject->setData($messageArray);
            $storeScope = ScopeInterface::SCOPE_STORE;
            $emails = str_replace(
                " ",
                "",
                (string) $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope)
            );
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => FrontNameResolver::AREA_CODE,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $this->dataObject])
                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo(explode(",", $emails))
                ->getTransport();
            $transport->sendMessage();
        } catch (MailException $e) {
            $logger = $this->getRabbiMqLogger(self::RABBITMQ_MESSAGE_LOG_FILE);
            $logger->info($e->getMessage());
        }
    }

    /**
     * @param $logFileName
     * @return Logger
     */
    public function getRabbiMqLogger($logFileName)
    {
        $logDirectory = BP . self::LOG_FILE_PATH;
        $filesystem = $this->fileSystem;
        $logger = $this->createLogger($logDirectory, $logFileName, $filesystem);
        $handler = new BaseHandler($filesystem, $logDirectory . $logFileName);
        $logger->pushHandler($handler);
        return $logger;
    }

    /**
     * Create a logger instance.
     *
     * @param string $logDirectory
     * @param string $logFileName
     * @return LoggerInterface
     */
    private function createLogger($logDirectory, $logFileName, DriverInterface $filesystem)
    {
        $logFile = $logDirectory . $logFileName;
        $monologLogger = new MonologLogger('custom_logger');
        $handler = new BaseHandler($filesystem, $logFile);
        $monologLogger->pushHandler($handler);
        return $monologLogger;
    }

    /**
     * Log messages in log file (ERP to Magento)
     *
     * @param $message
     */
    public function logRabbitMqMessage($message)
    {
		if ($this->isLoggingEnabledForIncomingMessages()) {
            $logger = $this->getRabbiMqLogger(self::RABBITMQ_MESSAGE_LOG_FILE);
            $logger->info($message->getProperties()['topic_name'] . "::" . $message->getBody());
        }
    }

    /**
     * Publish messages from Magento to ERP
	 * @param $topic
     * @param $message
     */
    public function logRabbitMqPublishedMessage($topic, $message)
    {
		if ($this->isLoggingEnabledForOutgoingMessages()) {
            $logger = $this->getRabbiMqLogger(self::RABBITMQ_PUBLISHED_MESSAGE_LOG_FILE);
            $logger->info($topic . "::" . $message);
        }
    }

    /**
     * @return mixed
     */
    public function isLoggingEnabled()
    {
        return $this->scopeConfig->getValue(
            Data::XML_PATH_ENABLE_LOGGING,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isCustomLogEnabled()
    {
        return $this->scopeConfig->getValue(
            Data::CUSTOM_LOG_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isOrderSyncDetailedLoggingEnabled()
    {
        return $this->scopeConfig->getValue(
            Data::XML_ENABLE_ORDER_SYNC_DETAILED_LOG,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Publish messages from Magento to ERP
     * @param $topic
     * @param $message
     */
    public function logOrderSyncDetailed($message)
    {
        if ($this->isOrderSyncDetailedLoggingEnabled()) {
            $logger = $this->getRabbiMqLogger(self::XML_ENABLE_ORDER_SYNC_DETAILED_LOG_FILE);
            $logger->info($message);
        }
    }


    /**
     * @return mixed
     */
    public function isEmailNotificationEnabled()
    {
        return $this->scopeConfig->getValue(
            Data::XML_PATH_ENABLE_EMAIL_NOTIFICATION,
            ScopeInterface::SCOPE_STORE
        );
    }


	 /**
     * Method to check incoming messages logging enabled
     *
     * @return mixed
     */
    public function isLoggingEnabledForIncomingMessages()
    {
        return $this->scopeConfig->getValue(
            Data::XML_PATH_ENABLED_INCOMING_MESSAGES_LOGGING,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Method to check outgoing messages logging enabled
     *
     * @return mixed
     */
    public function isLoggingEnabledForOutgoingMessages()
    {
        return $this->scopeConfig->getValue(
            Data::XML_PATH_ENABLED_OUTGOING_MESSAGES_LOGGING,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * @param $errorMessage
     * @param $title
     * @param $message
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendInfoEmail($errorMessage, $title, $message)
    {

        try {
            $storeId = $this->storeManagerInterface->getStore()->getId();
            $storeUrl = $this->storeManagerInterface->getStore()->getBaseUrl();
            $messageArray = [];
            $messageArray['error'] = $errorMessage;
            $messageArray['title'] = $title;
            $messageArray['subject'] = "RabbitMQ Message Published $storeUrl";
            $messageArray['message'] = $message;
            $this->dataObject->setData($messageArray);
            $storeScope = ScopeInterface::SCOPE_STORE;
            $emails = str_replace(
                " ",
                "",
                (string) $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope)
            );
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => FrontNameResolver::AREA_CODE,
                        'store' => $storeId,
                    ]
                )
                ->setTemplateVars(['data' => $this->dataObject])
                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo(explode(",", $emails))
                ->getTransport();
            $transport->sendMessage();
        } catch (MailException $e) {
            $logger = $this->getRabbiMqLogger(self::RABBITMQ_MESSAGE_LOG_FILE);
            $logger->info($e->getMessage());
        }
    }

    /**
     * Log order error messages
     *
     * @param $message
     * @param $logMessage
     * @param $erroMessage
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logRabbitmqOrderErrorMessages($message, $logMessage, $erroMessage)
    {
        $logger = $this->getRabbiMqLogger(
            self::MAGENTO_ORDER_PUBLISH_ERROR_LOG_FILE
        );
        $logger->debug($logMessage." :: ".$message);
        $this->sendErrorEmail(
            $erroMessage,
            __('message failed to send to syspro '),
            $message
        );
    }

    /**
     * Validate customer info
     *
     * @param $attributeCode
     * @param $attributeValue
     * @return mixed
     */
    public function getCustomerByAttribute($attributeCode, $attributeValue)
    {
        $customerId = '';
        try {
            if (self::CUSTOMER_ID_ATTR_CODE == $attributeCode) {
                $customer = $this->customerRepository->getById($attributeValue);
                $customerId = $customer->getId();
            } else {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter($attributeCode, $attributeValue, 'eq')->create();
                $customerData = $this->customerRepository->getList($searchCriteria)->getItems();
                if ($customerData) {
                    foreach ($customerData as $customer) {
                        $customerId = $customer->getId();
                        break;
                    }
                }
            }
        } catch (\Exception) {
            //
        }

        return $customerId;
    }
    /**
     * Method used to get the customer from repository.
     *
     * @param $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerById($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Get website code for the passed store-id
     *
     * @param null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteCodeByStoreId($storeId = null)
    {
        $websiteCode = '';
        if (!empty($storeId)) {
            $store = $this->storeRepository->getById($storeId);
            if ($store->getId()) {
                $websiteCode = $this->getWebsiteCode($store->getWebsiteId());
            }
        }

        return $websiteCode;
    }

    /**
     * Get website code for the passed website-id
     *
     * @param null $websiteId
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteCode($websiteId = null)
    {
        $websiteCode = '';
        if (!empty($websiteId)) {
            $website     = $this->websiteRepository->getById($websiteId);
            $websiteCode = $website->getCode();
        }
        return $websiteCode;
    }

    /**
     * Publish message of every error queue
     *
     * @param null $websiteId
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function publishErrMessage($queue, $message)
    {
        $this->publisher->publish($queue, $message);
        return true;
    }
}
