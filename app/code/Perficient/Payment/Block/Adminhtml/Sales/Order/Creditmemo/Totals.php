<?php
/**
 * Show Payment Method Fee in Admin Creditmemo
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

namespace Perficient\Payment\Block\Adminhtml\Sales\Order\Creditmemo;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Perficient\Payment\Helper\Data;
use Psr\Log\LoggerInterface;

class Totals extends Template
{
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
     * Get data (totals) source model
     *
     * @return DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return mixed
     */
    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        try {
            $source = $this->getSource();
            if ($source && (float)$source->getPaymentMethodFee()) {
                $paymentFeeLabel = $this->helper->getConfigLabel();
                $total = new DataObject(
                    [
                        'code' => Data::PAYMENT_FEE_CODE,
                        'strong' => false,
                        'value' => $source->getPaymentMethodFee(),
                        'label' => __($paymentFeeLabel),
                    ]
                );

                $this->getParentBlock()->addTotalBefore($total, 'grand_total');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
