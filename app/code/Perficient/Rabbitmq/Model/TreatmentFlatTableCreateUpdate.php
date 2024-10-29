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
use Perficient\Rabbitmq\Api\Data\TreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\TreatmentRepositoryInterface;
use Perficient\Rabbitmq\Helper\Data;
use Perficient\Rabbitmq\Model\TreatmentRepository as TreatmentRepositoryFetch;

/**
 * Class TreatmentFlatTableCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class TreatmentFlatTableCreateUpdate extends AbstractModel
{
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
     * TreatmentFlatTableCreateUpdate constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $jsonSerializer
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
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
        private readonly TreatmentInterfaceFactory $treatmentInterfaceFactory,
        private readonly TreatmentRepositoryInterface $treatmentRepositoryInterface,
        private readonly TreatmentRepositoryFetch $treatmentRepository,
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
    public function createUpdateTreatmentFlatTable($message)
    {
        try {
            $this->rabbitMqHelper->logRabbitMqMessage($message);
            $this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
            $this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            //$publishData = ['error' => $e->getMessage(), 'message' => $this->messageArray];
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Treatment Flat Table Create Update'),
                $message->getBody()
            );
        }
        try {
            if (isset($this->messageArray)
                && !empty($this->messageArray)
                && is_array($this->messageArray)
                && $this->messageArray['product']) {
                try {
                    if ($this->messageArray['product']['treatment'] &&
                        $this->messageArray['product']['treatment']['fields'] &&
                        is_array($this->messageArray['product']['treatment']['fields'])) {
                        $treatmentArrayRaw = $this->messageArray['product']['treatment']['fields'];
                        foreach($treatmentArrayRaw as $treatment){
                            $filtersTreatment = [
                                $this->filterBuilder
                                    ->setField('main_table.treatment_sku')
                                    ->setValue($treatment['treatment_sku'])
                                    ->setConditionType('eq')
                                    ->create()
                            ];
                            $searchCriteriaTreatment = $this->searchCriteriaBuilder
                                ->addFilters($filtersTreatment)
                                ->create();
                            $treatmentRepository = $this->treatmentRepository->getList($searchCriteriaTreatment);
                            if (is_countable($treatmentRepository->getItems()) ? count($treatmentRepository->getItems()) : 0) {
                                foreach ($treatmentRepository->getItems() as $item) {
                                    $treatmentArray['treatment_id'] = $item->getTreatmentId();
                                }
                            }
                            if (isset($treatment['treatment_sku'])) {
                                $treatmentArray['treatment_sku'] = $treatment['treatment_sku'];
                            }
                            if (isset($treatment['base_cost_treatment'])) {
                                $treatmentArray['base_cost_treatment'] = $treatment['base_cost_treatment'];
                            }
                            if (isset($treatment['display_name'])) {
                                $treatmentArray['display_name'] = $treatment['display_name'];
                            }
                            if (isset($treatment['min_glass_size_short'])) {
                                $treatmentArray['min_glass_size_short'] = $treatment['min_glass_size_short'];
                            }
                            if (isset($treatment['min_glass_size_long'])) {
                                $treatmentArray['min_glass_size_long'] = $treatment['min_glass_size_long'];
                            }
                            if (isset($treatment['max_glass_size_short'])) {
                                $treatmentArray['max_glass_size_short'] = $treatment['max_glass_size_short'];
                            }
                            if (isset($treatment['max_glass_size_long'])) {
                                $treatmentArray['max_glass_size_long'] = $treatment['max_glass_size_long'];
                            }
                            if (isset($treatment['min_rabbet_depth'])) {
                                $treatmentArray['min_rabbet_depth'] = $treatment['min_rabbet_depth'];
                            }
                            if (isset($treatment['requires_top_mat'])) {
                                $treatmentArray['requires_top_mat'] = $treatment['requires_top_mat'];
                            }
                            if (isset($treatment['requires_bottom_mat'])) {
                                $treatmentArray['requires_bottom_mat'] = $treatment['requires_bottom_mat'];
                            }
                            if (isset($treatment['requires_liner'])) {
                                $treatmentArray['requires_liner'] = $treatment['requires_liner'];
                            }
                            if (isset($treatment['image_edge_treatment'])) {
                                $treatmentArray['image_edge_treatment'] = $treatment['image_edge_treatment'];
                            }
                            if (isset($treatment['new_top_mat_size_left'])) {
                                $treatmentArray['new_top_mat_size_left'] = $treatment['new_top_mat_size_left'];
                            }
                            if (isset($treatment['new_top_mat_size_top'])) {
                                $treatmentArray['new_top_mat_size_top'] = $treatment['new_top_mat_size_top'];
                            }
                            if (isset($treatment['new_top_mat_size_right'])) {
                                $treatmentArray['new_top_mat_size_right'] = $treatment['new_top_mat_size_right'];
                            }
                            if (isset($treatment['new_top_mat_size_bottom'])) {
                                $treatmentArray['new_top_mat_size_bottom'] = $treatment['new_top_mat_size_bottom'];
                            }
                            if (isset($treatment['new_bottom_mat_size_left'])) {
                                $treatmentArray['new_bottom_mat_size_left'] = $treatment['new_bottom_mat_size_left'];
                            }
                            if (isset($treatment['new_bottom_mat_size_top'])) {
                                $treatmentArray['new_bottom_mat_size_top'] = $treatment['new_bottom_mat_size_top'];
                            }
                            if (isset($treatment['new_bottom_mat_size_right'])) {
                                $treatmentArray['new_bottom_mat_size_right'] = $treatment['new_bottom_mat_size_right'];
                            }
                            if (isset($treatment['new_bottom_mat_size_bottom'])) {
                                $treatmentArray['new_bottom_mat_size_bottom'] = $treatment['new_bottom_mat_size_bottom'];
                            }
                            if (isset($treatment['liner_depth_check'])) {
                                $treatmentArray['liner_depth_check'] = $treatment['liner_depth_check'];
                            }
                            if (isset($treatment['treatmentWeightPerSqFt_UpToThreshold'])) {
                                $treatmentArray['treatment_weight_per_sqFt_upto_threshold'] = $treatment['treatmentWeightPerSqFt_UpToThreshold'];
                            }
                            if (isset($treatment['treatmentWeightPerSqFt_OverThreshold'])) {
                                $treatmentArray['treatment_weight_per_sqFt_over_threshold'] = $treatment['treatmentWeightPerSqFt_OverThreshold'];
                            }
                            if (isset($treatment['status'])) {
                                $treatmentArray['status'] = $treatment['status'];
                            }
                            $treatmentArray['updated_at'] = date("Y-m-d H:i:s");
                            $objTreatmentModel = $this->treatmentInterfaceFactory->create();
                            $objTreatmentModel->setData($treatmentArray);
                            $this->treatmentRepositoryInterface->save($objTreatmentModel);
                        }

                    }
                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Treatment Flat Table Create Update'),
                        $message->getBody()
                    );
                }
            }
        } catch (\LogicException|\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::TREATMENT_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_TREATMENT_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Treatment Flat Table Create Update'),
                $message->getBody()
            );
        }
    }
}
