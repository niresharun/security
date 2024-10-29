<?php
/**
 * Rabbitmq mass consumer
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\MessageQueue\CallbackInvoker;
use Magento\Framework\MessageQueue\ConnectionLostException;
use Magento\Framework\MessageQueue\ConsumerConfigurationInterface;
use Magento\Framework\MessageQueue\ConsumerInterface;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\MessageController;
use Magento\Framework\MessageQueue\MessageLockException;
use Magento\Framework\MessageQueue\QueueInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\Rabbitmq\Exception\RabbitmqException;
use Perficient\Rabbitmq\Helper\Data as RabbitMqHelper;
use Perficient\Rabbitmq\Model\BaseCostCreateUpdateFactory;
use Perficient\Rabbitmq\Model\MediaTreatmentCreateUpdateFactory;
use Perficient\Rabbitmq\Model\MediaFlatTableCreateUpdateFactory;
use Perficient\Rabbitmq\Model\TreatmentFlatTableCreateUpdateFactory;
use Perficient\Rabbitmq\Model\FrameTreatmentFlatTableCreateUpdateFactory;
use Perficient\Rabbitmq\Model\MediaTreatmentFlatTableCreateUpdateFactory;
use Perficient\Rabbitmq\Model\ProductCreateUpdateFactory;
use Perficient\Rabbitmq\Model\ProductInventoryUpdateFactory;
use Perficient\Rabbitmq\Model\OrderCreateUpdateFactory;
use Perficient\Rabbitmq\Model\InvoiceShipmentCreateUpdateFactory;
use Perficient\Rabbitmq\Model\CompanyUpdateFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Consumer used to process OperationInterface messages.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassConsumer implements ConsumerInterface
{
    /**
     * MassConsumer constructor.
     * @param CallbackInvoker $invoker
     * @param ResourceConnection $resource
     * @param MessageController $messageController
     * @param ConsumerConfigurationInterface $configuration
     * @param LoggerInterface $logger
     * @param FrameTreatmentFlatTableCreateUpdateFactory $frameTreatmentFlatTableCreateUpdateFactory
     * @param MediaTreatmentFlatTableCreateUpdateFactory $mediaTreatmentFlatTableCreateUpdateFactory
     * @param Json $jsonSerializer
     */
    public function __construct(
        private readonly CallbackInvoker $invoker,
        private readonly ResourceConnection $resource,
        private readonly MessageController $messageController,
        private readonly ConsumerConfigurationInterface $configuration,
        private readonly LoggerInterface $logger,
        private readonly RabbitMqHelper $rabbitMqHelper,
        private readonly ProductInventoryUpdateFactory $productInventoryUpdateFactory,
        private readonly ProductCreateUpdateFactory $productCreateUpdateFactory,
        private readonly BaseCostCreateUpdateFactory $baseCostCreateUpdateFactory,
        private readonly MediaTreatmentCreateUpdateFactory $mediaTreatmentCreateUpdateFactory,
        private readonly MediaFlatTableCreateUpdateFactory $mediaFlatTableCreateUpdateFactory,
        private readonly TreatmentFlatTableCreateUpdateFactory $treatmentFlatTableCreateUpdateFactory,
        private readonly FrameTreatmentFlatTableCreateUpdateFactory $frameTreatmentFlatTableCreateUpdateFactory,
        private readonly MediaTreatmentFlatTableCreateUpdateFactory $mediaTreatmentFlatTableCreateUpdateFactory,
        private readonly OrderCreateUpdateFactory $orderCreateUpdateFactory,
        private readonly InvoiceShipmentCreateUpdateFactory $invoiceShipmentCreateUpdateFactory,
        private readonly CompanyUpdateFactory $companyUpdateFactory,
        private readonly Json $jsonSerializer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function process($maxNumberOfMessages = null)
    {

        $queue = $this->configuration->getQueue();
        if (!isset($maxNumberOfMessages)) {
            $queue->subscribe($this->getTransactionCallback($queue));
        } else {
            $this->invoker->invoke($queue, $maxNumberOfMessages, $this->getTransactionCallback($queue));
        }
    }

    /**
     * Get transaction callback. This handles the case of async.
     *
     * @param QueueInterface $queue
     * @return \Closure
     */
    private function getTransactionCallback(QueueInterface $queue)
    {
        return function (EnvelopeInterface $message) use ($queue) {

            $lock = null;
            $topicName = $message->getProperties()['topic_name'];

            try {
                $allowedTopics = $this->configuration->getTopicNames();
                $result = true;
                if (in_array($topicName, $allowedTopics)) {
                    if ($topicName == RabbitMqHelper::TOPIC_PRODUCT_INVENTORY_UPDATE) {
                        $productInventoryUpdateFactory = $this->productInventoryUpdateFactory->create();
                        $productInventoryUpdateFactory->updateProductInventory($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_PRODUCT_CREATE_UPDATE) {
                        $productFactory = $this->productCreateUpdateFactory->create();
                        $productFactory->createUpdateProduct($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_BASE_COST_CREATE_UPDATE) {
                        $baseCostFactory = $this->baseCostCreateUpdateFactory->create();
                        $baseCostFactory->createUpdateBaseCost($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_MEDIA_TREATMENT_CREATE_UPDATE) {
                        $baseCostFactory = $this->mediaTreatmentCreateUpdateFactory->create();
                        $baseCostFactory->createUpdateMediaTreatment($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_ORDER_CREATE_UPDATE) {
                        //Temp disabled to disable syspro ordr sync
                        $orderFactory = $this->orderCreateUpdateFactory->create();
                        $orderFactory->createUpdateOrder($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_ORDER_INVOICE_SHIPMENT_CREATE_UPDATE) {
                        //$invoiceShipmentFactory = $this->invoiceShipmentCreateUpdateFactory->create();
                        //$invoiceShipmentFactory->createInvoiceAndShipment($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_MEDIA_FLAT_TABLE_CREATE_UPDATE) {
                        $createUpdateMediaFlatTable = $this->mediaFlatTableCreateUpdateFactory->create();
                        $createUpdateMediaFlatTable->createUpdateMediaFlatTable($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_TREATMENT_FLAT_TABLE_CREATE_UPDATE) {
                        $createUpdateMediaFlatTable = $this->treatmentFlatTableCreateUpdateFactory->create();
                        $createUpdateMediaFlatTable->createUpdateTreatmentFlatTable($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE) {
                        $createUpdateMediaFlatTable = $this->mediaTreatmentFlatTableCreateUpdateFactory->create();
                        $createUpdateMediaFlatTable->createUpdateMediaTreatmentFlatTable($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE) {
                        $createUpdateMediaFlatTable = $this->frameTreatmentFlatTableCreateUpdateFactory->create();
                        $createUpdateMediaFlatTable->createUpdateFrameTreatmentFlatTable($message);
                    } elseif ($topicName == RabbitMqHelper::TOPIC_COMPANY_UPDATE) {
                        $companyUpdateFactory = $this->companyUpdateFactory->create();
                        $companyUpdateFactory->updateCompany($message);
                    }
                } else {
                    $queue->reject($message);
                    return;
                }

                if ($result === false) {
                    $queue->reject($message); // if get error in message process
                    return;
                }
                $queue->acknowledge($message); // send acknowledge to queue
            } catch (MessageLockException $exception) {
                $this->logger->debug($exception->getMessage());
                $queue->acknowledge($message);
                $this->rabbitMqHelper->sendErrorEmail(
                    $exception->getMessage(),
                    $topicName,
                    $this->jsonSerializer->serialize($message->getBody())
                );
            } catch (ConnectionLostException|\Exception $e) {
                $this->logger->debug($e->getMessage());
                $this->rabbitMqHelper->sendErrorEmail(
                    $e->getMessage(),
                    $topicName,
                    $this->jsonSerializer->serialize($message->getBody())
                );
            } catch (NotFoundException $e) {
                $this->logger->warning($e->getMessage());
                $queue->acknowledge($message);
                $this->rabbitMqHelper->sendErrorEmail(
                    $e->getMessage(),
                    $topicName,
                    $this->jsonSerializer->serialize($message->getBody())
                );
            } catch (RabbitmqException $exception) {
                $this->logger->debug($exception->getMessage());
                $this->rabbitMqHelper->sendErrorEmail(
                    $exception->getMessage(),
                    $topicName,
                    $this->jsonSerializer->serialize($message->getBody())
                );
            }
        };
    }
}
