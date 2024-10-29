<?php

namespace DCKAP\Productimize\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Psr\Log\LoggerInterface;
use DCKAP\Productimize\Model\ProductimizeCalculation;

class Option extends Action
{
    protected $_resultPageFactory;
    protected $_resultJsonFactory;
    protected $_logger;
    protected $_calc;

    public function __construct(Context $context, PageFactory $resultPageFactory, JsonFactory $resultJsonFactory, LoggerInterface $logger, ProductimizeCalculation $calc)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_logger = $logger;
        $this->_calc = $calc;
    }

    public function execute()
    {
        try {
            $resultJson = $this->_resultJsonFactory->create();
            $type = $this->getRequest()->getParam("type");
            $finalArray = [];

            if ($type == "size") {
                $selectedMediumOption = $this->getRequest()->getParam("selectedMedium");
                $selectedTreatmentOption = $this->getRequest()->getParam("selectedTreatment");
                $productId = $this->getRequest()->getParam("productId");
                $finalArray = $this->_calc->getSizeCalculation($selectedMediumOption, $selectedTreatmentOption, $productId);
            } elseif ($type == "frame") {
                $frameParams = $this->getRequest()->getParam('payload');
                $finalArray = $this->_calc->getFrameCalculation($frameParams);
            } elseif ($type == "liner") {
                $linerParams = $this->getRequest()->getParam('payload');
                $finalArray = $this->_calc->getLinerCalculation($linerParams);
            } elseif ($type == "topmat") {
                $topmatParams = $this->getRequest()->getParam('payload');
                $finalArray = $this->_calc->getTopMatCalculation($topmatParams);
            } elseif ($type == "bottommat") {
                $bottommatParams = $this->getRequest()->getParam('payload');
                $finalArray = $this->_calc->getBottomMatCalculation($bottommatParams);
            } elseif ($type == "price") {
                $priceParams = $this->getRequest()->getParam('payload');
                $finalPrice = $this->_calc->getCustomisedPrice($priceParams);
                $finalArray = $finalPrice;
            } elseif ($type == "pdp_page_price") {
                $priceParams = $this->getRequest()->getParam('payload');
                $finalPrice = $this->_calc->getPDPPagePriceAndRestrictCustomizeButtonStatus($priceParams);
                $finalArray = $finalPrice;
            }
            $result['status'] = true;
            $result['content'] = $finalArray;
            return $resultJson->setData($result);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
            $result['status'] = __('error');
            $result['info'] = $e->getMessage();
            return $resultJson->setData($result);
        }

    }
}
