<?php
/**
 * Plugin to allow customer to reorder or not.
 *
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Plugin\Sales\Helper;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Reorder;
use Magento\Customer\Model\Session as CustomerSession;
use Perficient\Company\Helper\Data as CompanyHelper;

/**
 * Plugin For Reorder
 */
class ReorderPlugin
{
    /**
     * ReorderPlugin constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected CustomerSession $customerSession,
        protected CompanyHelper $companyHelper
    ) {
    }

    /**
     * @param Reorder $subject
     * @param $orderId
     * @return bool
     */
    public function aroundCanReorder(Reorder $subject, callable $proceed, $orderId)
    {
        /**
         * Load the order and check, if there is any dummy product in order then do not display the Reorder option.
         */
        $order = $this->orderRepository->get($orderId);
        if ($order->getHasDummyProduct()) {
            return false;
        }

        if ($this->customerSession->isLoggedIn()) {
            // Restrict Reorder if price multipler is 0x
            $multiplier = $this->customerSession->getMultiplier() ?? 1;
            if ($multiplier == 0) {
                return false;
            }

            // Restrict Reorder for Customer's Customer
            $currentUserRole = $this->companyHelper->getCurrentUserRole();
            $currentUserRole = $currentUserRole ? htmlspecialchars_decode((string) $currentUserRole, ENT_QUOTES) : '';
            if (strcmp($currentUserRole, (string) CompanyHelper::CUSTOMER_CUSTOMER) == 0) {
                return false;
            }
        }

        // Check the rest of the conditions.
        return $proceed($orderId);
    }

}