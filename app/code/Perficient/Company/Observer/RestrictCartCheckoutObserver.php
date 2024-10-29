<?php
/**
 * Customers Customer Restrict Add to Cart, Cart, Checkout.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perficient\Company\Helper\Data;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Perficient\Checkout\Model\Quote as PerficientQuote;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RestrictCartCheckoutObserver
 * @package Perficient\Company\Observer
 */
class RestrictCartCheckoutObserver implements ObserverInterface
{
    const HANDLE_CHECKOUT_CART_INDEX = "checkout_cart_index";
    const HANDLE_CHECKOUT_CHECKOUT_INDEX = "checkout_index_index";
    const CUSTOMERS_RESTRICTD_PAGES = [
        self::HANDLE_CHECKOUT_CART_INDEX,
        self::HANDLE_CHECKOUT_CHECKOUT_INDEX
    ];

    /**
     * cart limit exceed cms page url key
     */
    const CART_LIMIT_EXCEED_URL_KEY = 'cart-limit-exceeded';

    /**
     * RestrictCartCheckoutObserver Construct
     *
     * @param RedirectInterface $redirect
     * @param UrlInterface $url
     */
    public function __construct(
        protected RedirectInterface      $redirect,
        private readonly Data            $helper,
        private readonly CustomerSession $customerSession,
        protected CheckoutSession        $checkoutSession,
        protected PerficientQuote        $perficientQuote,
        protected UrlInterface           $url,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        try {
            /*
             * Start - If customer directly hit/go on cart and checkout page and have over max limit items in cart
             * then redirect to cms page
             */
            $quote = $this->checkoutSession->getQuote();
            if ($quote) {
                $itemsInCart = $quote->getItemsCount();
                $maxItemsInCart = $this->perficientQuote->getMaxItemsInCart();
                $maxItemsMessage = $this->perficientQuote->getMaxItemsInCartMessage();
                if (!empty($maxItemsInCart) && !empty($maxItemsMessage)
                    && $itemsInCart && $itemsInCart > $maxItemsInCart) {
                    $controller = $observer->getControllerAction();
                    $limitExceedCmsUrl = $this->url->getUrl(self::CART_LIMIT_EXCEED_URL_KEY);
                    $this->redirect->redirect($controller->getResponse(), $limitExceedCmsUrl);
                }
            }
            /*End*/

            if ($this->helper->isRestrictCartAndCheckout()) {
                if ($this->customerSession->isLoggedIn()) {

                    /** @var \Magento\Framework\App\RequestInterface $request */
                    $request = $observer->getEvent()->getRequest();
                    if (in_array($request->getFullActionName(), self::CUSTOMERS_RESTRICTD_PAGES)) {
                        // Restrict Cart/Checkout if price multiplier is 0x
                        $multiplier = $this->customerSession->getMultiplier() ?? 1;
                        if ($multiplier == 0) {
                            $controller = $observer->getControllerAction();
                            $this->redirect->redirect($controller->getResponse(), $this->helper->getRestrictedPageRedirectPath());
                        }

                        // Restrict Cart/Checkout for customer's customer
                        $currentUserRole = $this->helper->getCurrentUserRole();
                        $currentUserRole = htmlspecialchars_decode((string)$currentUserRole, ENT_QUOTES);
                        if (strcmp($currentUserRole, Data::CUSTOMER_CUSTOMER) == 0) {
                            $controller = $observer->getControllerAction();
                            $this->redirect->redirect($controller->getResponse(), $this->helper->getRestrictedPageRedirectPath());
                        }
                    }
                } else {
                    // Restrict Cart/Checkout for guest customer
                    $controller = $observer->getControllerAction();
                    $this->redirect->redirect($controller->getResponse(), $this->helper->getRestrictedPageRedirectPath());
                }
            }
        } catch (\Exception $e) {
            $this->psrLogger->critical($e);
        }
    }
}
