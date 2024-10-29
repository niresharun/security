<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */
declare(strict_types=1);

namespace Perficient\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\UrlInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\Checkout\Model\Session;
use Perficient\QuickShip\Helper\Data as QuickShipHelper;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Data
 * @package Perficient\CrmConnector\Helper
 */
class Data extends AbstractHelper
{
    final const PLP = 'category';
    final const PDP = 'product';
    final const STORE_ID = 1;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * Data constructor.
     * @param Context $context
     * @param Http $request
     * @param RedirectInterface $redirect
     * @param UrlFinderInterface $getUrlTypeObj
     * @param UrlInterface $urlInterface
     * @param Session $checkoutSession
     * @param Cart $cart
     * @param SerializerInterface $serializer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        private readonly Context             $context,
        private readonly Http                $request,
        private readonly RedirectInterface   $redirect,
        private readonly UrlFinderInterface  $getUrlTypeObj,
        private readonly UrlInterface        $urlInterface,
        Session                              $checkoutSession,
        private readonly Cart                $cart,
        private readonly SerializerInterface $serializer
    )
    {
        parent::__construct($context);
        $this->quote = $checkoutSession->getQuote();
    }

    /**
     * @return string
     */
    public function pageRequestType()
    {
        $baseUrl = $this->urlInterface->getBaseUrl();
        $urlWithoutQueryString = strtok($this->redirect->getRedirectUrl(), '?');
        $urlPath = str_replace($baseUrl, '', $urlWithoutQueryString);
        $actualUrls = $this->getUrlTypeObj->findOneByData(
            [
                'request_path' => $urlPath,
                'store_id' => self::STORE_ID
            ]
        );
        return $expectedType = $actualUrls->getEntityType();
    }

    /**
     * @return string
     */
    public function getPlpUrl()
    {
        return $this->redirect->getRedirectUrl();
    }

    public function isQuickShipCart()
    {
        return $this->quote->getData(QuickShipHelper::QUICK_SHIP_ATTRIBUTE);
    }

    /**
     * @param null $itemID
     * @return mixed
     */
    public function getCartSideMark($itemID)
    {
        $sidemark = '';
        if (!empty($itemID)) {
            $itemOption = $this->cart->getQuote();
            if (!empty($itemOption->getItemById($itemID))) {
                if (!empty($itemOption->getItemById($itemID)->getBuyRequest())) {
                    $pzData = $itemOption->getItemById($itemID)->getBuyRequest()->getData();
                    if (isset($pzData['pz_cart_properties']) && !empty($pzData['pz_cart_properties'])) {
                        $pzArray = $this->serializer->unserialize($pzData['pz_cart_properties']);
                        if (!empty($pzArray) && isset($pzArray['Side Mark']) && !empty($pzArray['Side Mark'])) {
                            $sidemark = $pzArray['Side Mark'];
                        }
                    }
                }
            }
        }
        return $sidemark;
    }
}
