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
namespace Perficient\QuickShip\Plugin\Cart;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Plugin For Remove Item
 */
class RemoveProduct
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @const string
     */
    const QUICK_SHIP_ATTRIBUTE = 'quick_ship';

    /**
     * Plugin constructor.
     *
     * @param Session $checkoutSession
     */
    public function __construct(
        protected Cart $cart,
        Session $checkoutSession,
        protected CartRepositoryInterface $quoteRepository
    ) {
        $this->quote = $checkoutSession->getQuote();
    }

    /**
     * @param Cart $subject
     * @param $result
     * @return array
     */
    public function afterSave($subject, $result)
    {
        $quote = $this->cart->getQuote();

        if(empty($this->quote)) {
            return $result;
        }

        $itemCount = is_countable($this->quote->getAllVisibleItems()) ? count($this->quote->getAllVisibleItems()) : 0;

        if($itemCount <= 0) {
            $quote->setData(self::QUICK_SHIP_ATTRIBUTE, null);
            $this->quoteRepository->save($quote);
        }
        return $result;
    }

}
