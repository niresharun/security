<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\RequisitionList\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\RequisitionList\Api\Data\RequisitionListInterface;
use Magento\RequisitionList\Model\RequisitionList\ItemsLoadHandler;

/**
 * Requisition List Model
 */
class RequisitionList extends \Magento\RequisitionList\Model\RequisitionList
{

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param ItemsLoadHandler $itemsLoadHandler
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        ItemsLoadHandler $itemsLoadHandler,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $itemsLoadHandler,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setTradeshow($tradeshow)
    {
        return $this->setData('tradeshow', $tradeshow);
    }

    /**
     * {@inheritdoc}
     */
    public function getTradeshow()
    {
        return $this->getData('tradeshow');
    }
}
