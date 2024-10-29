<?php

namespace Wendover\FindYourRep\Controller\Rep;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Wendover\FindYourRep\Model\ResourceModel\Rep\CollectionFactory as RepCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RepData extends \Magento\Framework\App\Action\Action
{
    const NO_REP_TEXT = 'find_your_rep/general/no_rep_text';

    /**
     * @param Context $context
     * @param PageFactory $_pageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     */
    public function __construct(
        Context                               $context,
        private readonly PageFactory          $_pageFactory,
        private readonly RepCollection        $repCollection,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly RequestInterface     $request
    )
    {
        return parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $params = $this->request->getParams();
        $params['postalcode'] = ltrim((string)$params['postalcode'], '0');
        $region = $params['region'];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $collection = $this->repCollection->create()->addFieldToSelect('*')
            ->addFieldToFilter('type', ['eq' => $params['type']])
            ->addFieldToFilter('postal_code', ['eq' => $params['postalcode']]);
        $htmlNoRepContent = "<span class='no-rep-text'>" . $this->getNoRepText() . "</span>";
        $success = false;
        if ($region == 'us') {
            $htmlContent = "<h3 class='contact-info-title'>Contact Information</h3>";
            if ($collection->getSize() > 0) {
                foreach ($collection as $item) {
                    $htmlContent .= "<div><p class='rep-name'><strong>" . $item->getData('firstname') . ' ' . $item->getData('lastname') . "</strong></p> ";
                    $htmlContent .= "<p class='email'>" . $item->getData('email') . "</p>";
                    $htmlContent .= "<p class='phone1'>" . '<span>Phone 1: </span>' . $item->getData('phone1') . "</p>";
                    $htmlContent .= ($item->getData('phone2') != '') ?
                        "<p class='phone2'>" . '<span>Phone 2: </span>' . $item->getData('phone2') . "</p>" : '';
                    $htmlContent .= "<p class='notes'>" . $item->getData('notes') . "</p></div>";
                }
                $success = true;
            } else {
                $htmlContent =  $htmlNoRepContent;
            }
        } else {
            $htmlContent =  $htmlNoRepContent;
        }
        return $resultJson->setData([
            'html' => $htmlContent,
            'success' => $success
        ]);
    }

    /**
     * @return mixed
     */
    public function getNoRepText()
    {
        return $this->scopeConfig->getValue(
            self::NO_REP_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
