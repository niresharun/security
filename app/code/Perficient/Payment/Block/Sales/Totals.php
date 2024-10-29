<?php
/**
 * Show Payment Method Fee in Frontend My Account Order
 * @category: Magento
 * @package: Perficient/Payment
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar <Amin.Akhtar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Payment
 */
declare(strict_types=1);

namespace Perficient\Payment\Block\Sales;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Perficient\Payment\Helper\Data;
use Psr\Log\LoggerInterface;

class Totals extends Template
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var DataObject
     */
    protected $source;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Totals constructor.
     * @param Template\Context $context
     * @param Data $helper
     * @param LoggerInterface $loggerInterface
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        LoggerInterface $loggerInterface,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->logger = $loggerInterface;
        parent::__construct($context, $data);
    }

    /**
     * Check if we need display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        try {
            $parent = $this->getParentBlock();
            if ($parent) {
                $this->source = $parent->getSource();
                if ($this->source && (float)$this->source->getPaymentMethodFee()) {
                    $paymentFeeLabel = $this->helper->getConfigLabel();
                    $total = new DataObject(
                        [
                            'code' => Data::PAYMENT_FEE_CODE,
                            'strong' => false,
                            'value' => $this->source->getPaymentMethodFee(),
                            'label' => __($paymentFeeLabel),
                        ]
                    );

                    $parent->addTotal($total, 'payment_method_fee');
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
