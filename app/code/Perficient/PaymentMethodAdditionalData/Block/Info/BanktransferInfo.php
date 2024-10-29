<?php

namespace Perficient\PaymentMethodAdditionalData\Block\Info;

/**
 * Class BanktransferInfo
 * @package Perficient\PaymentMethodAdditionalData\Block\Info
 */
class BanktransferInfo extends \Magento\Sales\Block\Adminhtml\Order\View
{
    protected $_bankTransfer;

    /**
     * BanktransferInfo constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\OfflinePayments\Model\Banktransfer $bankTransfer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\OfflinePayments\Model\Banktransfer $bankTransfer,
        array $data = []
    ) {
        $this->_reorderHelper = $reorderHelper;
        $this->_coreRegistry = $registry;
        $this->_salesConfig = $salesConfig;
        $this->_bankTransfer = $bankTransfer;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }
}
