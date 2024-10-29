<?php
/**
 * Rabbitmq product create update
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@Perficient.com>
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model;

use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\Rabbitmq\Api\Data\MediaTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\MediaTreatmentRepositoryInterface;
use Perficient\Rabbitmq\Helper\Data;
use Perficient\Rabbitmq\Model\MediaTreatmentRepository as MediaTreatmentRepositoryFetch;

/**
 * Class MediaTreatmentFlatTableCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class MediaTreatmentFlatTableCreateUpdate extends AbstractModel
{
    const TABLE_MEDIA_TREATMENT = 'media_treatment';
    
    /**
     * @var MessageArray
     */
    private $messageArray;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;
    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
     * MediaTreatmentFlatTableCreateUpdate constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $jsonSerializer
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
     * @param MediaTreatmentInterfaceFactory $mediaTreatmentInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly Json $jsonSerializer,
        private readonly Data $rabbitMqHelper,
        private readonly ProductInterfaceFactory $productFactory,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly Product $product,
        Filesystem $filesystem,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly MediaTreatmentInterfaceFactory $mediaTreatmentInterfaceFactory,
        private readonly MediaTreatmentRepositoryInterface $mediaTreatmentRepositoryInterface,
        private readonly MediaTreatmentRepositoryFetch $mediaTreatmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly FilterBuilder $filterBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $message
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createUpdateMediaTreatmentFlatTable($message)
    {
        try {
            $this->rabbitMqHelper->logRabbitMqMessage($message);
            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::MEDIA_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Media Treatment Flat Table Create Update'),
                $message->getBody()
            );
        }
        try {
            if (isset($this->messageArray)
                && !empty($this->messageArray)
                && is_array($this->messageArray)
                && $this->messageArray['product']) {
                try {
                    if ($this->messageArray['product']['media_treatment'] &&
                        $this->messageArray['product']['media_treatment']['fields']&&
                        is_array($this->messageArray['product']['media_treatment']['fields'])) {
                        $mediaTreatmentArrayRaw = $this->messageArray['product']['media_treatment']['fields'];
                        foreach($mediaTreatmentArrayRaw as $mediaTreatment ){
/*                            $filtersMediaTreatment = [
                                $this->filterBuilder
                                    ->setField('main_table.media_sku')
                                    ->setValue($mediaTreatment['media_sku'])
                                    ->setConditionType('eq')
                                    ->create()
                            ];
                            $filtersMediaTreatment = [
                                $this->filterBuilder
                                    ->setField('main_table.treatment_sku')
                                    ->setValue($mediaTreatment['treatment_sku'])
                                    ->setConditionType('eq')
                                    ->create()
                            ];
// Prepare search criteria builder.
                            $searchCriteriaMediaTreatment = $this->searchCriteriaBuilder
                                ->addFilters($filtersMediaTreatment)
                                ->create();
// Get the list.*/

                            $this->searchCriteriaBuilder->addFilters(
                                [
                                    $this->filterBuilder
                                        ->setField('main_table.media_sku')
                                        ->setValue($mediaTreatment['media_sku'])
                                        ->setConditionType('eq')
                                        ->create(),
                                ]
                            );
                            $this->searchCriteriaBuilder->addFilters(
                                [
                                    $this->filterBuilder
                                        ->setField('main_table.treatment_sku')
                                        ->setValue($mediaTreatment['treatment_sku'])
                                        ->setConditionType('eq')
                                        ->create(),
                                ]
                            );
                            $mediaTreatmentRepository = $this->mediaTreatmentRepository->getList($this->searchCriteriaBuilder->create());
                            if (is_countable($mediaTreatmentRepository->getItems()) ? count($mediaTreatmentRepository->getItems()) : 0) {
                                foreach ($mediaTreatmentRepository->getItems() as $item) {
                                    $mediaTreatmentArray['media_treatment_id'] = $item->getMediaTreatmentId();
                                }
                            }
                            if (isset($mediaTreatment['media_sku'])) {
                                $mediaTreatmentArray['media_sku'] = $mediaTreatment['media_sku'];
                            }
                            if (isset($mediaTreatment['treatment_sku'])) {
                                $mediaTreatmentArray['treatment_sku'] = $mediaTreatment['treatment_sku'];
                            }
                            if (isset($mediaTreatment['display_to_customer'])) {
                                $mediaTreatmentArray['display_to_customer'] = $mediaTreatment['display_to_customer'];
                            }
                            if (!isset($mediaTreatmentArray['display_to_customer'])) {
                                $mediaTreatmentArray['display_to_customer'] = false;
                            }
                            if (isset($mediaTreatment['status'])) {
                                $mediaTreatmentArray['status'] = $mediaTreatment['status'];
                            }
                            $mediaTreatmentArray['updated_at'] = date("Y-m-d H:i:s");
                            $objMediaTreatmentModel = $this->mediaTreatmentInterfaceFactory->create();
                            $objMediaTreatmentModel->setData($mediaTreatmentArray);
                            $this->mediaTreatmentRepositoryInterface->save($objMediaTreatmentModel);
                        }

                    }
                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::MEDIA_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Media Treatment Flat Table Create Update'),
                        $message->getBody()
                    );
                }
            }
        } catch (\LogicException|\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::MEDIA_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Media Treatment Flat Table Create Update'),
                $message->getBody()
            );
        }
    }
}
