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
use Perficient\Rabbitmq\Api\Data\MediaInterfaceFactory;
use Perficient\Rabbitmq\Api\MediaRepositoryInterface;
use Perficient\Rabbitmq\Helper\Data;
use Perficient\Rabbitmq\Model\MediaRepository as MediaRepositoryFetch;


/**
 * Class MediaFlatTableCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class MediaFlatTableCreateUpdate extends AbstractModel
{

    const TABLE_MEDIA = 'media';

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
     * @param MediaInterfaceFactory $mediaInterfaceFactory
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
        private readonly MediaInterfaceFactory $mediaInterfaceFactory,
        private readonly MediaRepositoryInterface $mediaRepositoryInterface,
        private readonly MediaRepositoryFetch $mediaRepository,
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
    public function createUpdateMediaFlatTable($message)
    {
        try {
            $this->rabbitMqHelper->logRabbitMqMessage($message);
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
            //$publishData = ['error' => $e->getMessage(), 'message' => $this->messageArray];
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Media Flat Table Create Update'),
                $message->getBody()
            );
        }
        try {
            if (isset($this->messageArray)
                && !empty($this->messageArray)
                && is_array($this->messageArray)
                && $this->messageArray['product']) {
                try {
                    if ($this->messageArray['product']['media'] &&
                        $this->messageArray['product']['media']['fields'] &&
                        is_array($this->messageArray['product']['media']['fields'])
                        ) {
                        $mediaArrayRaw = $this->messageArray['product']['media']['fields'];
                        foreach($mediaArrayRaw as $media){
                            $filtersMedia = [
                                $this->filterBuilder
                                    ->setField('main_table.sku')
                                    ->setValue($media['sku'])
                                    ->setConditionType('eq')
                                    ->create()
                            ];
                            $searchCriteriaMedia = $this->searchCriteriaBuilder
                                ->addFilters($filtersMedia)
                                ->create();
                            $mediaRepository = $this->mediaRepository->getList($searchCriteriaMedia);
                            if (is_countable($mediaRepository->getItems()) ? count($mediaRepository->getItems()) : 0) {
                                foreach ($mediaRepository->getItems() as $item) {
                                    $mediaArray['media_id'] = $item->getMediaId();
                                }
                            }
                            if (isset($media['sku'])) {
                                $mediaArray['sku'] = $media['sku'];
                            }
                            if (isset($media['base_cost_media'])) {
                                $mediaArray['base_cost_media'] = $media['base_cost_media'];
                            }
                            if (isset($media['display_name'])) {
                                $mediaArray['display_name'] = $media['display_name'];
                            }
                            if (isset($media['min_image_size_short'])) {
                                $mediaArray['min_image_size_short'] = $media['min_image_size_short'];
                            }
                            if (isset($media['min_image_size_long'])) {
                                $mediaArray['min_image_size_long'] = $media['min_image_size_long'];
                            }
                            if (isset($media['max_image_size_short'])) {
                                $mediaArray['max_image_size_short'] = $media['max_image_size_short'];
                            }
                            if (isset($media['max_image_size_long'])) {
                                $mediaArray['max_image_size_long'] = $media['max_image_size_long'];
                            }
                            if (isset($media['status'])) {
                                $mediaArray['status'] = $media['status'];
                            }

                            $mediaArray['updated_at'] = date("Y-m-d H:i:s");
                            $objMediaModel = $this->mediaInterfaceFactory->create();
                            $objMediaModel->setData($mediaArray);
                            $this->mediaRepositoryInterface->save($objMediaModel);
                        }

                    }
                } catch (\LogicException|\Exception $e) {
                    if ($this->rabbitMqHelper->isLoggingEnabled()) {
                        $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                            Data::MEDIA_FLAT_TABLE_ERROR_LOG_FILE
                        );
                        $logger->debug($e->getMessage() . "::" . $message->getBody());
                    }
                    $publishData = ['error' => $e->getMessage()];
                    $jsonData = $this->jsonSerializer->serialize($publishData);
                    $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
                    $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_FLAT_TABLE_CREATE_UPDATE, $jsonData);
                    $this->rabbitMqHelper->sendErrorEmail(
                        $e->getMessage(),
                        __('Media Flat Table Create Update'),
                        $message->getBody()
                    );
                }
            }
        } catch (\LogicException|\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::MEDIA_FLAT_TABLE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_MEDIA_FLAT_TABLE_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Media Flat Table Create Update'),
                $message->getBody()
            );
        }
    }
}
