<?php
/**
 * Inventory for Quick Ship
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);
namespace Perficient\QuickShip\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Header;

/**
 * Observer for add to cart
 */
class AddToCartObserver implements ObserverInterface
{
    /**
     * @const string
     */
    const QUICK_SHIP_FIELD = 'is_quick_ship';
    /**
     * @const string
     */
    const QUICK_SHIP_ATTRIBUTE = 'quick_ship';

    /**
     * @const string
     */
    const SOURCE_FLAG_ATTRIBUTE = 'source_flag';

    /**
     * @const int
     */
    const SOURCE_FLAG_MAGENTO = 1;

    /**
     * AddToCartObserver constructor.
     * @param Cart $cart
     * @param SessionManagerInterface $coreSession
     * @param QuoteRepository $quoteRepository
     * @param ManagerInterface $messageManager
     * @param Session $checkoutSession
     * @param RequestInterface $request
     * @param Header $header
     */
    public function __construct(
        private readonly Cart $cart,
        private readonly SessionManagerInterface $coreSession,
        private readonly QuoteRepository $quoteRepository,
        private readonly ManagerInterface $messageManager,
        private readonly Session $checkoutSession,
        protected RequestInterface $request,
        protected Header $header,
        array $data = []
    ) {
    }

    /**
     * Observer Execute function
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote= $this->cart->getQuote();
        $product = $observer->getProduct();

        if (empty($quote) || empty($product)) {
            return;
        }

        $itemCount = $quote->getItemsCount();
        $isQuickShipProduct = $product->getData(self::QUICK_SHIP_FIELD);
        $hasQuickShipInCart = $quote->getData(self::QUICK_SHIP_ATTRIBUTE);

        $isQuickShip = $this->request->getParam('quick_ship_product');
        $fromQuickShip = 0;
        if(isset($isQuickShip) && $isQuickShip) {
            $fromQuickShip = 1;
        }

        $addQuickShipToCart = true;
        if($hasQuickShipInCart && !$isQuickShipProduct) {
            return;
        }

        // Regular product
        if($itemCount>1 && !$hasQuickShipInCart) {
            $addQuickShipToCart = false;
            $this->coreSession->setAddedRegularProduct(1);
        } elseif(!$itemCount) {
            $addQuickShipToCart = true;
        }
        $quote->setData(self::SOURCE_FLAG_ATTRIBUTE, self::SOURCE_FLAG_MAGENTO);
        if ($addQuickShipToCart && $fromQuickShip && $isQuickShipProduct) {
            $quote->setData(self::QUICK_SHIP_ATTRIBUTE, 1);
            $this->quoteRepository->save($quote);
            $this->checkoutSession->clearQuote()->clearStorage();
        }
    }
}
