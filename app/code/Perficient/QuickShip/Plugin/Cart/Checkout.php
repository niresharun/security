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

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Perficient\QuickShip\Helper\Data as PerficientOrderQuickShipHelper;

/**
 * Validate cart for quick ship product before checkout
 */
class Checkout
{
    /**
     * Plugin constructor.
     *
     * @param ManagerInterface $messageManager
     * @param UrlInterface $url
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        protected PerficientOrderQuickShipHelper $perficientQuickShipHelper,
        protected ManagerInterface $messageManager,
        protected UrlInterface $url,
        protected RedirectFactory $redirectFactory
    ) {
    }

    /**
     * @param \Magento\Checkout\Controller\Index\Index $subject
     */
    public function beforeExecute($subject)
    {
        if (true === $this->perficientQuickShipHelper->validateQuickShipCart()) {
            $this->messageManager->addNoticeMessage($this->perficientQuickShipHelper->getCartItemRemovedMessage());

            $redirectionUrl = $this->url->getUrl('checkout/cart');
            $redirectResult = $this->redirectFactory->create();
            $redirectResult->setUrl($redirectionUrl);

            return [$redirectResult];
        }
    }
}
