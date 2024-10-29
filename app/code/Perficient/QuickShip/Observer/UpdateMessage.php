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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;

/**
 * Observer which shows the notification message
 */
class UpdateMessage implements ObserverInterface
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @const string Success message when config when quick ship product added as a regular product
     */
    const QUICK_SHIP_ADD_SUCCESS_MESSAGE = 'quickship/general/add_to_cart_success_message';

    /**
     * UpdateMessage constructor.
     * @param ManagerInterface $messageManager
     * @param SessionManagerInterface $coreSession
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     */
    public function __construct(
        protected ManagerInterface $messageManager,
        protected SessionManagerInterface $coreSession,
        protected ScopeConfigInterface $scopeConfig,
        Session $checkoutSession
    ) {
        $this->quote = $checkoutSession->getQuote();
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer) {

        if($this->coreSession->getAddedRegularProduct()) {
            $this->coreSession->setAddedRegularProduct(null);

            $messageCollection = $this->messageManager->getMessages();
            $lastMessage = $messageCollection->getLastAddedMessage();
            $productData = $lastMessage->getData();
            $productName = !empty($productData['product_name'])?$productData['product_name']:__('Product');

            $customMessage = $this->scopeConfig->getValue(self::QUICK_SHIP_ADD_SUCCESS_MESSAGE,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

            $this->quote->setHasError(true);
            if(strlen(trim((string) $customMessage)) > 0) {
                $customMessage = str_replace('%product_name%', $productName, (string) $customMessage);
                $this->messageManager->addNoticeMessage($customMessage);
            }
        }
    }
}
