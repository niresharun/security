<?php
/**
 * Rabbitmq product create update
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
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
use Perficient\Rabbitmq\Api\BaseCostRepositoryInterface;
use Perficient\Rabbitmq\Api\Data\BaseCostInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\MediaInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\MediaTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\TreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\FrameTreatmentRepositoryInterface;
use Perficient\Rabbitmq\Api\MediaRepositoryInterface;
use Perficient\Rabbitmq\Api\MediaTreatmentRepositoryInterface;
use Perficient\Rabbitmq\Api\TreatmentRepositoryInterface;
use Perficient\Rabbitmq\Helper\Data;
use Perficient\Rabbitmq\Model\BaseCostRepository as BaseCostRepositoryFetch;
use Perficient\Rabbitmq\Model\FrameTreatmentRepository as FrameTreatmentRepositoryFetch;
use Perficient\Rabbitmq\Model\MediaRepository as MediaRepositoryFetch;
use Perficient\Rabbitmq\Model\MediaTreatmentRepository as MediaTreatmentRepositoryFetch;
use Perficient\Rabbitmq\Model\TreatmentRepository as TreatmentRepositoryFetch;

/**
 * Class MediaTreatmentCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class MediaTreatmentCreateUpdate extends AbstractModel
{
    const TABLE_BASE_COST = 'base_cost';
    const TABLE_FRAME_TREATMENT = 'frame_treatment';
    const TABLE_MEDIA = 'media';
    const TABLE_MEDIA_TREATMENT = 'media_treatment';
    const TABLE_TREATMENT = 'treatment';

    /**
     * @var MessageArray
     */
    private $messageArray;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * MediaTreatmentCreateUpdate constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $jsonSerializer
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
     * @param BaseCostInterfaceFactory $baseCostInterfaceFactory
     * @param FrameTreatmentInterfaceFactory $frameTreatmentInterfaceFactory
     * @param MediaInterfaceFactory $mediaInterfaceFactory
     * @param MediaTreatmentInterfaceFactory $mediaTreatmentInterfaceFactory
     * @param TreatmentInterfaceFactory $treatmentInterfaceFactory
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
        private readonly BaseCostInterfaceFactory $baseCostInterfaceFactory,
        private readonly BaseCostRepositoryInterface $baseCostRepositoryInterface,
        private readonly BaseCostRepositoryFetch $baseCostRepository,
        private readonly FrameTreatmentInterfaceFactory $frameTreatmentInterfaceFactory,
        private readonly FrameTreatmentRepositoryInterface $frameTreatmentRepositoryInterface,
        private readonly FrameTreatmentRepositoryFetch $frameTreatmentRepository,
        private readonly MediaInterfaceFactory $mediaInterfaceFactory,
        private readonly MediaRepositoryInterface $mediaRepositoryInterface,
        private readonly MediaRepositoryFetch $mediaRepository,
        private readonly MediaTreatmentInterfaceFactory $mediaTreatmentInterfaceFactory,
        private readonly MediaTreatmentRepositoryInterface $mediaTreatmentRepositoryInterface,
        private readonly MediaTreatmentRepositoryFetch $mediaTreatmentRepository,
        private readonly TreatmentInterfaceFactory $treatmentInterfaceFactory,
        private readonly TreatmentRepositoryInterface $treatmentRepositoryInterface,
        private readonly TreatmentRepositoryFetch $treatmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly FilterBuilder $filterBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param $message
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createUpdateMediaTreatment($message)
    {
        $mediaArray = [];
        $frameTreatmentArray = [];
        $mediaTreatmentArray = [];
        $treatmentArray = [];

        try {
            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::MEDIA_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Media Treatment Create Update'),
                $message->getBody()
            );
        }
        try {
            if (isset($this->messageArray)
                && !empty($this->messageArray)
                && is_array($this->messageArray)
                && $this->messageArray['product']['flat_tables']) {
                try {
                    if ($this->messageArray['product']['flat_tables']['media'] &&
                        $this->messageArray['product']['flat_tables']['media']['fields']) {

                        $filtersMedia = [
                            $this->filterBuilder
                                ->setField('main_table.sku')
                                ->setValue($this->messageArray['product']['flat_tables']['media']['fields']['sku'])
                                ->setConditionType('eq')
                                ->create()
                        ];

                        // Prepare search criteria builder.
                        $searchCriteriaMedia = $this->searchCriteriaBuilder
                            ->addFilters($filtersMedia)
                            ->create();
                        // Get the list.
                        $mediaRepository = $this->mediaRepository->getList($searchCriteriaMedia);
                        if (is_countable($mediaRepository->getItems()) ? count($mediaRepository->getItems()) : 0) {
                            foreach ($mediaRepository->getItems() as $item) {
                                $mediaArray['media_id'] = $item->getMediaId();
                            }
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['sku'])) {
                            $mediaArray['sku'] = $this->messageArray['product']['flat_tables']['media']['fields']['sku'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['base_cost_media'])) {
                            $mediaArray['base_cost_media'] = $this->messageArray['product']['flat_tables']['media']['fields']['base_cost_media'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['display_name'])) {
                            $mediaArray['display_name'] = $this->messageArray['product']['flat_tables']['media']['fields']['display_name'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['display_to_customer'])) {
                            $mediaArray['display_to_customer'] = $this->messageArray['product']['flat_tables']['media']['fields']['display_to_customer'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['min_image_size_short'])) {
                            $mediaArray['min_image_size_short'] = $this->messageArray['product']['flat_tables']['media']['fields']['min_image_size_short'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['min_image_size_long'])) {
                            $mediaArray['min_image_size_long'] = $this->messageArray['product']['flat_tables']['media']['fields']['min_image_size_long'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['max_image_size_short'])) {
                            $mediaArray['max_image_size_short'] = $this->messageArray['product']['flat_tables']['media']['fields']['max_image_size_short'];
                        }

                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['max_image_size_long'])) {
                            $mediaArray['max_image_size_long'] = $this->messageArray['product']['flat_tables']['media']['fields']['max_image_size_long'];
                        }
                        if (!isset($mediaArray['display_to_customer'])) {
                            $mediaArray['display_to_customer'] = false;
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media']['fields']['status'])) {
                            $mediaArray['status'] = $this->messageArray['product']['flat_tables']['media']['fields']['status'];
                        }
                        $mediaArray['updated_at'] = date("Y-m-d H:i:s");
                        //  $logger->info(print_r($mediaArray, true));
                        $objMediaModel = $this->mediaInterfaceFactory->create();
                        $objMediaModel->setData($mediaArray);
                        $this->mediaRepositoryInterface->save($objMediaModel);
                    }


                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::MEDIA_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Media Treatment Create Update'),
                        $message->getBody()
                    );
                }

                try {
                    if ($this->messageArray['product']['flat_tables']['frame_treatment'] &&
                        $this->messageArray['product']['flat_tables']['frame_treatment']['fields']) {

                        $filtersFrameTreatment = [
                            $this->filterBuilder
                                ->setField('main_table.treatment_sku')
                                ->setValue($this->messageArray['product']['flat_tables']['frame_treatment']['fields']['treatment_sku'])
                                ->setConditionType('eq')
                                ->create()
                        ];
                        $filtersFrameTreatment = [
                            $this->filterBuilder
                                ->setField('main_table.frame_type')
                                ->setValue($this->messageArray['product']['flat_tables']['frame_treatment']['fields']['frame_type'])
                                ->setConditionType('eq')
                                ->create()
                        ];

                        // Prepare search criteria builder.
                        $searchCriteriaFrameTreatment = $this->searchCriteriaBuilder
                            ->addFilters($filtersFrameTreatment)
                            ->create();
                        // Get the list.
                        $frameTreatmentRepository = $this->frameTreatmentRepository->getList($searchCriteriaFrameTreatment);
                        if (is_countable($frameTreatmentRepository->getItems()) ? count($frameTreatmentRepository->getItems()) : 0) {
                            foreach ($frameTreatmentRepository->getItems() as $item) {
                                $frameTreatmentArray['frame_treatment_id'] = $item->getFrameTreatmentId();
                            }
                        }
                        if (isset($this->messageArray['product']['flat_tables']['frame_treatment']['fields']['treatment_sku'])) {
                            $frameTreatmentArray['treatment_sku'] = $this->messageArray['product']['flat_tables']['frame_treatment']['fields']['treatment_sku'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['frame_treatment']['fields']['frame_type'])) {
                            $frameTreatmentArray['frame_type'] = $this->messageArray['product']['flat_tables']['frame_treatment']['fields']['frame_type'];
                        }

                        if (isset($this->messageArray['product']['flat_tables']['frame_treatment']['fields']['status'])) {
                            $frameTreatmentArray['status'] = $this->messageArray['product']['flat_tables']['frame_treatment']['fields']['status'];
                        }

                        $frameTreatmentArray['updated_at'] = date("Y-m-d H:i:s");

                        $objFrameTreatmentModel = $this->frameTreatmentInterfaceFactory->create();
                        $objFrameTreatmentModel->setData($frameTreatmentArray);
                        $this->frameTreatmentRepositoryInterface->save($objFrameTreatmentModel);
                    }


                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::MEDIA_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Media Treatment Create Update'),
                        $message->getBody()
                    );
                }

                try {
                    if ($this->messageArray['product']['flat_tables']['media_treatment'] &&
                        $this->messageArray['product']['flat_tables']['media_treatment']['fields']) {

                        $filtersMediaTreatment = [
                            $this->filterBuilder
                                ->setField('main_table.media_sku')
                                ->setValue($this->messageArray['product']['flat_tables']['media_treatment']['fields']['media_sku'])
                                ->setConditionType('eq')
                                ->create()
                        ];
                        $filtersMediaTreatment = [
                            $this->filterBuilder
                                ->setField('main_table.treatment_sku')
                                ->setValue($this->messageArray['product']['flat_tables']['media_treatment']['fields']['treatment_sku'])
                                ->setConditionType('eq')
                                ->create()
                        ];

                        // Prepare search criteria builder.
                        $searchCriteriaMediaTreatment = $this->searchCriteriaBuilder
                            ->addFilters($filtersMediaTreatment)
                            ->create();
                        // Get the list.
                        $mediaTreatmentRepository = $this->mediaTreatmentRepository->getList($searchCriteriaMediaTreatment);
                        if (is_countable($mediaTreatmentRepository->getItems()) ? count($mediaTreatmentRepository->getItems()) : 0) {
                            foreach ($mediaTreatmentRepository->getItems() as $item) {
                                $mediaTreatmentArray['media_treatment_id'] = $item->getMediaTreatmentId();
                            }
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media_treatment']['fields']['media_sku'])) {
                            $mediaTreatmentArray['media_sku'] = $this->messageArray['product']['flat_tables']['media_treatment']['fields']['media_sku'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media_treatment']['fields']['treatment_sku'])) {
                            $mediaTreatmentArray['treatment_sku'] = $this->messageArray['product']['flat_tables']['media_treatment']['fields']['treatment_sku'];
                        }

                        if (isset($this->messageArray['product']['flat_tables']['media_treatment']['fields']['display_to_customer'])) {
                            $mediaTreatmentArray['display_to_customer'] = $this->messageArray['product']['flat_tables']['media_treatment']['fields']['display_to_customer'];
                        }
                        if (!isset($mediaTreatmentArray['display_to_customer'])) {
                            $mediaTreatmentArray['display_to_customer'] = false;
                        }
                        if (isset($this->messageArray['product']['flat_tables']['media_treatment']['fields']['status'])) {
                            $mediaTreatmentArray['status'] = $this->messageArray['product']['flat_tables']['media_treatment']['fields']['status'];
                        }
                        $mediaTreatmentArray['updated_at'] = date("Y-m-d H:i:s");
                        $objMediaTreatmentModel = $this->mediaTreatmentInterfaceFactory->create();
                        $objMediaTreatmentModel->setData($mediaTreatmentArray);
                        $this->mediaTreatmentRepositoryInterface->save($objMediaTreatmentModel);
                    }


                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::MEDIA_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Media Treatment Create Update'),
                        $message->getBody()
                    );
                }

                try {
                    if ($this->messageArray['product']['flat_tables']['treatment'] &&
                        $this->messageArray['product']['flat_tables']['treatment']['fields']) {

                        $filtersTreatment = [
                            $this->filterBuilder
                                ->setField('main_table.treatment_sku')
                                ->setValue($this->messageArray['product']['flat_tables']['treatment']['fields']['treatment_sku'])
                                ->setConditionType('eq')
                                ->create()
                        ];


                        // Prepare search criteria builder.
                        $searchCriteriaTreatment = $this->searchCriteriaBuilder
                            ->addFilters($filtersTreatment)
                            ->create();
                        // Get the list.
                        $treatmentRepository = $this->treatmentRepository->getList($searchCriteriaTreatment);
                        if (is_countable($treatmentRepository->getItems()) ? count($treatmentRepository->getItems()) : 0) {
                            foreach ($treatmentRepository->getItems() as $item) {
                                $treatmentArray['treatment_id'] = $item->getTreatmentId();
                            }
                        }


                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['treatment_sku'])) {
                            $treatmentArray['treatment_sku'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['treatment_sku'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['base_cost_treatment'])) {
                            $treatmentArray['base_cost_treatment'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['base_cost_treatment'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['display_name'])) {
                            $treatmentArray['display_name'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['display_name'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['min_glass_size_short'])) {
                            $treatmentArray['min_glass_size_short'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['min_glass_size_short'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['min_glass_size_long'])) {
                            $treatmentArray['min_glass_size_long'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['min_glass_size_long'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['max_glass_size_short'])) {
                            $treatmentArray['max_glass_size_short'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['max_glass_size_short'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['max_glass_size_long'])) {
                            $treatmentArray['max_glass_size_long'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['max_glass_size_long'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['min_rabbet_depth'])) {
                            $treatmentArray['min_rabbet_depth'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['min_rabbet_depth'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['requires_top_mat'])) {
                            $treatmentArray['requires_top_mat'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['requires_top_mat'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['requires_bottom_mat'])) {
                            $treatmentArray['requires_bottom_mat'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['requires_bottom_mat'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['requires_liner'])) {
                            $treatmentArray['requires_liner'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['requires_liner'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['image_edge_treatment'])) {
                            $treatmentArray['image_edge_treatment'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['image_edge_treatment'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_left'])) {
                            $treatmentArray['new_top_mat_size_left'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_left'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_top'])) {
                            $treatmentArray['new_top_mat_size_top'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_top'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_right'])) {
                            $treatmentArray['new_top_mat_size_right'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_right'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_bottom'])) {
                            $treatmentArray['new_top_mat_size_bottom'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_top_mat_size_bottom'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_left'])) {
                            $treatmentArray['new_bottom_mat_size_left'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_left'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_top'])) {
                            $treatmentArray['new_bottom_mat_size_top'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_top'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_right'])) {
                            $treatmentArray['new_bottom_mat_size_right'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_right'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_bottom'])) {
                            $treatmentArray['new_bottom_mat_size_bottom'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['new_bottom_mat_size_bottom'];
                        }
                        if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['liner_rabbet_depth_check'])) {
                            $treatmentArray['liner_rabbet_depth_check'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['liner_rabbet_depth_check'];
                        }
                      if (isset($this->messageArray['product']['flat_tables']['treatment']['fields']['status'])) {
                            $treatmentArray['status'] = $this->messageArray['product']['flat_tables']['treatment']['fields']['status'];
                        }
                        $treatmentArray['updated_at'] = date("Y-m-d H:i:s");
                        $objTreatmentModel = $this->treatmentInterfaceFactory->create();
                        $objTreatmentModel->setData($treatmentArray);
                        $this->treatmentRepositoryInterface->save($objTreatmentModel);
                    }
                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::MEDIA_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Media Treatment Create Update'),
                        $message->getBody()
                    );
                }
            }
        } catch (\LogicException|\Exception $e) {
			if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::MEDIA_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_TREATMENT_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Media Treatment Create Update'),
                $message->getBody()
            );
        }
    }
}
