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
use Perficient\Rabbitmq\Helper\Data;
use Perficient\Rabbitmq\Model\BaseCostRepository as BaseCostRepositoryFetch;

/**
 * Class BaseCostCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class BaseCostCreateUpdate extends AbstractModel
{
    const TABLE_BASE_COST = 'base_cost';

    /**
     * @var MessageArray
     */
    private $messageArray;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * BaseCostCreateUpdate constructor.
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
     * Update product inventory
     *
     * @param $message
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createUpdateBaseCost($message)
    {
		$params = [];
        try {
        	$this->messageArray = $this->jsonSerializer->unserialize($message->getBody());
        	$this->messageArray = $this->jsonSerializer->unserialize($this->messageArray);
		} catch (\Exception $e) {
			if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::BASE_COST_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage());
                $logger->debug($message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->messageArray.'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_BASE_COST_CREATE_UPDATE,$jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Base Cost Create Update'),
                $message->getBody()
            );
		}
        try {
            if (isset($this->messageArray)
                && !empty($this->messageArray)
                && is_array($this->messageArray)
                && isset($this->messageArray['product']['table'])
                && !empty($this->messageArray['product']['table'])
                && $this->messageArray['product']['table'] == self::TABLE_BASE_COST
                && isset($this->messageArray['product']['fields'])
                && !empty($this->messageArray['product']['fields'])
                && is_array($this->messageArray['product']['fields'])
            ) {
     /*           $filters = [
                    $this->filterBuilder
                        ->setField('main_table.base_cost_media')
                        ->setValue($this->messageArray['product']['fields']['base_cost_media'])
                        ->setConditionType('eq')
                        ->create()
                ];

                $filters = [
                    $this->filterBuilder
                        ->setField('main_table.base_cost_treatment')
                        ->setValue($this->messageArray['product']['fields']['base_cost_treatment'])
                        ->setConditionType('eq')
                        ->create()
                ];

                $filters = [
                    $this->filterBuilder
                        ->setField('main_table.glass_size_short')
                        ->setValue($this->messageArray['product']['fields']['glass_size_short'])
                        ->setConditionType('eq')
                        ->create()
                ];
                $filters = [
                    $this->filterBuilder
                        ->setField('main_table.glass_size_long')
                        ->setValue($this->messageArray['product']['fields']['glass_size_long'])
                        ->setConditionType('eq')
                        ->create()
                ];
                // Prepare search criteria builder.
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilters($filters)
                    ->create();*/

                $this->searchCriteriaBuilder->addFilters(
                    [
                        $this->filterBuilder
                            ->setField('main_table.base_cost_media')
                            ->setValue($this->messageArray['product']['fields']['base_cost_media'])
                            ->setConditionType('eq')
                            ->create()
                    ]
                );

                $this->searchCriteriaBuilder->addFilters(
                    [
                        $this->filterBuilder
                            ->setField('main_table.base_cost_treatment')
                            ->setValue($this->messageArray['product']['fields']['base_cost_treatment'])
                            ->setConditionType('eq')
                            ->create()
                    ]
                );

                $this->searchCriteriaBuilder->addFilters(
                    [
                        $this->filterBuilder
                            ->setField('main_table.glass_size_short')
                            ->setValue($this->messageArray['product']['fields']['glass_size_short'])
                            ->setConditionType('eq')
                            ->create()
                    ]
                );

                $this->searchCriteriaBuilder->addFilters(
                    [
                        $this->filterBuilder
                            ->setField('main_table.glass_size_long')
                            ->setValue($this->messageArray['product']['fields']['glass_size_long'])
                            ->setConditionType('eq')
                            ->create()
                    ]
                );
                // Get the list.
                $baseCostRepository = $this->baseCostRepository->getList($this->searchCriteriaBuilder->create());
                if (count($baseCostRepository->getItems())) {
                    foreach ($baseCostRepository->getItems() as $item) {
                        $params['base_cost_id'] = $item->getBaseCostId();
                    }
                }
                $objModel = $this->baseCostInterfaceFactory->create();
                if(isset($this->messageArray['product']['fields']['base_cost_media'])){
                    $params['base_cost_media'] = $this->messageArray['product']['fields']['base_cost_media'];
                }
                if(isset($this->messageArray['product']['fields']['base_cost_treatment'])){
                    $params['base_cost_treatment'] = $this->messageArray['product']['fields']['base_cost_treatment'];
                }
                if(isset($this->messageArray['product']['fields']['glass_size_short'])){
                    $params['glass_size_short'] = $this->messageArray['product']['fields']['glass_size_short'];
                }
                if(isset($this->messageArray['product']['fields']['glass_size_long'])){
                    $params['glass_size_long'] = $this->messageArray['product']['fields']['glass_size_long'];
                }
                if(isset($this->messageArray['product']['fields']['base_cost'])){
                    $params['base_cost'] = $this->messageArray['product']['fields']['base_cost'];
                }
                if(isset($this->messageArray['product']['fields']['status'])){
                    $params['status'] = $this->messageArray['product']['fields']['status'];
                }
                   $params['updated_at'] = date("Y-m-d H:i:s");
                $objModel->setData($params);
                $this->baseCostRepositoryInterface->save($objModel);
            }
        } catch (\Exception $e) {
            if ($this->rabbitMqHelper->isLoggingEnabled()) {
                $logger = $this->rabbitMqHelper->getRabbiMqLogger(
                    Data::BASE_COST_UPDATE_ERROR_LOG_FILE
                );
                $logger->debug($e->getMessage() . "::" . $message->getBody());
            }
            $publishData = ['error' => $e->getMessage()];
            $jsonData = $this->jsonSerializer->serialize($publishData);
            $jsonData=rtrim((string) $jsonData,'}').', "Message" :"'.$this->jsonSerializer->unserialize($message->getBody()).'"}';
            $this->rabbitMqHelper->publishErrMessage(Data::ERR_TOPIC_BASE_COST_CREATE_UPDATE, $jsonData);
            $this->rabbitMqHelper->sendErrorEmail(
                $e->getMessage(),
                __('Base Cost Create Update'),
                $message->getBody()
            );
        }
    }
}
