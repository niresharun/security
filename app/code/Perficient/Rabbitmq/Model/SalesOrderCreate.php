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

namespace Perficient\Rabbitmq\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Perficient\Rabbitmq\Helper\Data;
use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Class CustomerDataCreateUpdate
 * @package Perficient\Rabbitmq\Model
 */
class SalesOrderCreate extends AbstractModel
{
    /**
     * @var $isDataSubmitted
     */
    private $isDataSubmitted;

    /**
     * SalesOrderCreate constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param PublisherInterface $publisher
     */
    public function __construct(
        Context $context,
        Registry $registry,
        private readonly Data $rabbitMqHelper,
        private readonly PublisherInterface $publisher,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Send data (order, customer, customer address, etc.) from Magento to ERP
     * @param $topic
     * @param $message
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createSalesOrderDataFromMagentoToERP($topic, $message)
    {
        try {
            $this->rabbitMqHelper->logRabbitMqPublishedMessage($topic, $message);
            $this->publisher->publish($topic, $message);
            $this->isDataSubmitted = true;
        } catch (\Exception $e) {
            $this->isDataSubmitted = false;
            $erroMessage  = __('Published message to topic "%1" failed. Below is the error code', $topic);
            $erroMessage .= '<br /><br />' . $e->getMessage();
            $this->rabbitMqHelper->sendErrorEmail(
                $erroMessage,
                __('Published message to topic "%1" failed', $topic),
                $message
            );
        }

        return $this->isDataSubmitted;
    }
}
