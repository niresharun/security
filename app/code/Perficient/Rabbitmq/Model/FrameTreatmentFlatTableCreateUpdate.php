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
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\FrameTreatmentRepositoryInterface;
use Perficient\Rabbitmq\Helper\Data;
use Perficient\Rabbitmq\Model\FrameTreatmentRepository as FrameTreatmentRepositoryFetch;

/**
 * Class FrameTreatmentFlatTableCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class FrameTreatmentFlatTableCreateUpdate extends AbstractModel
{
    const TABLE_FRAME_TREATMENT = 'frame_treatment';

    /**
     * @var MessageArray
     */
    private $messageArray;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * FrameTreatmentFlatTableCreateUpdate constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $jsonSerializer
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
     * @param FrameTreatmentInterfaceFactory $frameTreatmentInterfaceFactory
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
        private readonly FrameTreatmentInterfaceFactory $frameTreatmentInterfaceFactory,
        private readonly FrameTreatmentRepositoryInterface $frameTreatmentRepositoryInterface,
        private readonly FrameTreatmentRepositoryFetch $frameTreatmentRepository,
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
    public function createUpdateFrameTreatmentFlatTable($message)
    {
        try {
            $this->rabbitMqHelper->logRabbitMqMessage($message);
            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::FRAME_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Frame Treatment Table Create Update'),
                $message->getBody()
            );
        }
        try {
            if (isset($this->messageArray)
                && !empty($this->messageArray)
                && is_array($this->messageArray)
                && $this->messageArray['product']) {
                try {
                    if ($this->messageArray['product']['frame_treatment'] &&
                        $this->messageArray['product']['frame_treatment']['fields'] &&
                        is_array($this->messageArray['product']['frame_treatment']['fields'])) {
                        $frameTreatmentArrayRaw = $this->messageArray['product']['frame_treatment']['fields'];
                        foreach ($frameTreatmentArrayRaw as $frameTreatment) {
                            $this->searchCriteriaBuilder->addFilters(
                                [
                                    $this->filterBuilder
                                        ->setField('main_table.treatment_sku')
                                        ->setValue($frameTreatment['treatment_sku'])
                                        ->setConditionType('eq')
                                        ->create(),
                                ]
                            );
                            $this->searchCriteriaBuilder->addFilters(
                                [
                                    $this->filterBuilder
                                        ->setField('main_table.frame_type')
                                        ->setValue($frameTreatment['frame_type'])
                                        ->setConditionType('eq')
                                        ->create(),
                                ]
                            );
                            $frameTreatmentRepository = $this->frameTreatmentRepository->getList($this->searchCriteriaBuilder->create());
                            if (is_countable($frameTreatmentRepository->getItems()) ? count($frameTreatmentRepository->getItems()) : 0) {
                                foreach ($frameTreatmentRepository->getItems() as $item) {
                                    $frameTreatmentArray['frame_treatment_id'] = $item->getFrameTreatmentId();
                                }
                            }
                            if (isset($frameTreatment['treatment_sku'])) {
                                $frameTreatmentArray['treatment_sku'] = $frameTreatment['treatment_sku'];
                            }
                            if (isset($frameTreatment['frame_type'])) {
                                $frameTreatmentArray['frame_type'] = $frameTreatment['frame_type'];
                            }
                            if (isset($frameTreatment['status'])) {
                                $frameTreatmentArray['status'] = $frameTreatment['status'];
                            }
                            $frameTreatmentArray['updated_at'] = date("Y-m-d H:i:s");
                            $objFrameTreatmentModel = $this->frameTreatmentInterfaceFactory->create();
                            $objFrameTreatmentModel->setData($frameTreatmentArray);
                            $this->frameTreatmentRepositoryInterface->save($objFrameTreatmentModel);
                        }
                    }
                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::FRAME_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Frame Treatment Table Create Update'),
                        $message->getBody()
                    );
                }
            }
        } catch (\LogicException|\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::FRAME_TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_FRAME_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Frame Treatment Table Update'),
                $message->getBody()
            );
        }
    }
}
