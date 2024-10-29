<?php
declare(strict_types=1);

namespace Wendover\Checkout\Plugin;

use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Wishlist\Model\ItemCarrier as CoreItemCarrier;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\Wishlist;
use Perficient\Checkout\Model\Quote as PerficientQuote;
use Magento\Quote\Model\Quote as CoreQuote;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Checkout\Model\Session as CheckoutSession;


class ItemCarrier extends CoreItemCarrier
{
    protected $customerSession;
    protected $quantityProcessor;
    protected $cart;
    protected $logger;
    protected $helper;
    protected $cartHelper;
    protected $urlBuilder;
    protected $messageManager;
    protected $redirector;

    public function __construct(
        Session $customerSession,
        LocaleQuantityProcessor $quantityProcessor,
        Cart $cart,
        Logger $logger,
        WishlistHelper $helper,
        CartHelper $cartHelper,
        UrlInterface $urlBuilder,
        MessageManager $messageManager,
        RedirectInterface $redirector,
        protected PerficientQuote $perficientQuote,
        protected CoreQuote $coreQuote,
        protected CheckoutSession $checkoutSession

    ) {
        $this->customerSession = $customerSession;
        $this->quantityProcessor = $quantityProcessor;
        $this->cart = $cart;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->urlBuilder = $urlBuilder;
        $this->messageManager = $messageManager;
        $this->redirector = $redirector;
        parent::__construct($customerSession, $quantityProcessor, $cart, $logger, $helper, $cartHelper, $urlBuilder, $messageManager, $redirector);
    }

    public function moveAllToCart(Wishlist $wishlist, $qtys)
    {
        $isOwner = $wishlist->isOwner($this->customerSession->getCustomerId());

        $messages = [];
        $addedProducts = [];
        $notSalable = [];

        $cart = $this->cart;
        $collection = $wishlist->getItemCollection()->setVisibilityFilter();
        foreach ($collection as $item) {
            /** @var $item \Magento\Wishlist\Model\Item */
            try {
                $quoteId = $this->checkoutSession->getQuote()->getId();
                $itemsInCart = $this->perficientQuote->getQuoteItemCounts($quoteId);
                $maxItemsInCart = $this->perficientQuote->getMaxItemsInCart();
                $maxItemsMessage = $this->perficientQuote->getMaxItemsInCartMessage();
                if (!empty($maxItemsInCart) && !empty($maxItemsMessage)
                    && $itemsInCart && $itemsInCart >= $maxItemsInCart) {
                    $this->coreQuote->setHasError(true);
                    throw new \Exception($maxItemsMessage);
                }

                $disableAddToCart = $item->getProduct()->getDisableAddToCart();
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->quantityProcessor->process($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }
                $item->getProduct()->setDisableAddToCart($disableAddToCart);
                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedProducts[] = $item->getProduct();
                }
            } catch (LocalizedException $e) {
                if ($e instanceof ProductException) {
                    $notSalable[] = $item;
                } else {
                    $messages[] = __('%1 for "%2".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }

                $cartItem = $cart->getQuote()->getItemByProduct($item->getProduct());
                if ($cartItem) {
                    $cart->getQuote()->deleteItem($cartItem);
                }
                break;
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $messages[] = __('You have exceeded the limit for maximum number of items in your cart.');
                break;
            }
        }

        if ($isOwner) {
            $indexUrl = $this->helper->getListUrl($wishlist->getId());
        } else {
            $indexUrl = $this->urlBuilder->getUrl('wishlist/shared', ['code' => $wishlist->getSharingCode()]);
        }
        if ($this->cartHelper->getShouldRedirectToCart()) {
            $redirectUrl = $this->cartHelper->getCartUrl();
        } elseif ($this->redirector->getRefererUrl()) {
            $redirectUrl = $this->redirector->getRefererUrl();
        } else {
            $redirectUrl = $indexUrl;
        }

        if ($notSalable) {
            $products = [];
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __(
                'We couldn\'t add the following product(s) to the shopping cart: %1.',
                join(', ', $products)
            );
        }

        if ($messages) {
            foreach ($messages as $message) {
                $this->messageManager->addErrorMessage($message);
            }
            $redirectUrl = $indexUrl;
        }

        if ($addedProducts) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t update the Wish List right now.'));
                $redirectUrl = $indexUrl;
            }

            $products = [];
            foreach ($addedProducts as $product) {
                /** @var $product \Magento\Catalog\Model\Product */
                $products[] = '"' . $product->getName() . '"';
            }

            $this->messageManager->addSuccessMessage(
                __('%1 product(s) have been added to shopping cart: %2.', count($addedProducts), join(', ', $products))
            );

            // save cart and collect totals
            $cart->save()->getQuote()->collectTotals();
        }
        $this->helper->calculate();
        return $redirectUrl;
    }
}
