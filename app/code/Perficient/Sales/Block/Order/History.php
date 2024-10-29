<?php
/**
 * Modify Customer Account Sales Order Navigation
 * @category: Magento
 * @package: Perficient/Sales
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Sales
 */
declare(strict_types=1);

namespace Perficient\Sales\Block\Order;

use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use \Magento\Framework\App\RequestInterface;

/**
 * Sales order history block
 *
 * @api
 * @since 100.0.2
 */
class History extends \Magento\Sales\Block\Order\History
{
    /**
     * @var string
     */
    protected $_template = 'Perficient_QuickShip::order/history.phtml';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orders;

    /**
     * @var CollectionFactoryInterface
     */
    private $orderCollectionFactory;

    /**
     * ORDER_STATUS_COMPLETE
     */
    const ORDERSTATUS_COMPLETE = 'complete';

    /**
     * Canceled Order Status
     */
    const ORDERSTATUS_CANCELED = 'canceled';

    /**
     * Partial Shipped Order Status
     */
    const ORDERSTATUS_PARTIAL_SHIPPED = 'partially_shipped';

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory,
        \Magento\Customer\Model\Session $_customerSession,
        \Magento\Sales\Model\Order\Config $_orderConfig,
        private readonly RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $_orderCollectionFactory, $_customerSession, $_orderConfig, $data);
    }

    /**
     * Provide order collection factory
     *
     * @return CollectionFactoryInterface
     * @deprecated 100.1.1
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }

    /**
     * Get customer orders
     */
    public function getOrders(): bool|\Magento\Sales\Model\ResourceModel\Order\Collection
    {
        $params = $this->request->getParams();
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            if (!empty($params['order-status']) && ($params['order-status'] == self::ORDERSTATUS_CANCELED)) {
                $condition = ['in' => self::ORDERSTATUS_CANCELED];
            } else {
                $condition = [
                    ['nin' => [self::ORDERSTATUS_COMPLETE,self::ORDERSTATUS_CANCELED]],
                    ['in'  => self::ORDERSTATUS_PARTIAL_SHIPPED]
                ];
            }
            $this->orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'status',
                $condition
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
    }
}
