<?php

namespace Perficient\Productimize\Plugin\Model;

use Perficient\Order\Helper\Data as PerficientOrderHelper;

class QuoteSurcharge
{
   /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    public $redirect;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    public $orderRepository;

    private PerficientOrderHelper $perficientOrderHelper;

    /**
     * Editquoteupdate constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param PerficientOrderHelper $perficientOrderHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        PerficientOrderHelper $perficientOrderHelper
    )
    {
        $this->request = $request;
        $this->redirect = $redirect;
        $this->orderRepository = $orderRepository;
        $this->perficientOrderHelper = $perficientOrderHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param null $request
     * @param string $processMode
     * @return array
     */
    public function beforeAddProduct(
        \Magento\Quote\Model\Quote $subject,
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    )
    {
        $surchargeProductSku = $this->perficientOrderHelper->getSurchargeProductSku();
        if($product->getSku() == $surchargeProductSku) {
            $product->addCustomOption('additional_options', json_encode([]));
        }
        return [$product, $request, $processMode];
    }
}
