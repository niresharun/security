<?php

namespace DCKAP\Productimize\Plugin;

use \Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\SessionFactory;
use \Perficient\Productimize\Model\ProductConfiguredPrice;
use \DCKAP\Productimize\Helper\Data;

class AddToCollectionPriceUpdate
{
    /**
     * @var QuoteOperations
     */
    protected $quoteOperations;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    protected $productimizeHelperData;

    protected $session;

    /**
     * CartPlugin constructor.
     * @param QuoteOperations $quoteOperations
     * @param CurrentQuote $currentQuote
     */
    public function __construct(
        RequestInterface $request,
        Data $productimizeHelperData,
        ProductConfiguredPrice $perficientPriceCalc,
        SessionFactory $session
    )
    {

        $this->request = $request;
        $this->productimizeHelperData = $productimizeHelperData;
        $this->perficientPriceCalc = $perficientPriceCalc;
        $this->session = $session;
    }

    /**
     * @param $subject
     * @param $result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterAddProduct($subject, $result)
    {
        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/fdstest.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('-----in DCKAP afterAddProduct Collection (all commented)-----');*/

        /*$checkoutSession = $this->session->create();
        $items = $checkoutSession->getQuote()->getAllItems();
        if ($items) {
            foreach ($items as $item) {
                $itemData = $item->getBuyRequest()->getData();
                if (isset($itemData['pz_cart_properties']) && (isset($itemData['edit_id']) && $itemData['edit_id'] == 1)) {
                    $changedPriceParams = $this->productimizeHelperData->getPriceParam($itemData['pz_cart_properties'], $item->getProduct()->getId(), "");
                    $checkoutPrice = $this->perficientPriceCalc->getCheckoutPrice($item->getProduct()->getId(), $changedPriceParams);
                    if ($checkoutPrice) {
                        $customisedPrice = $checkoutPrice;
                        $item->setPrice($customisedPrice);
                        $item->setCustomPrice($customisedPrice);
                        $item->setOriginalCustomPrice($customisedPrice);
                        $item->getProduct()->setIsSuperMode(true);
                    }
                }
            }
        }
        $checkoutSession->getQuote()->collectTotals()->save();*/
        //return $result;
    }
}