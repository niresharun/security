<?php
/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Tahir Aziz <tahir.aziz@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_Company
 */

namespace Perficient\Company\Plugin\Wishlist\Block\Customer;

use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Customer\Model\SessionFactory;

/**
 * Plugin for Wishlist Sidebar class.
 */
class SidebarPlugin
{
    public function __construct(
        private readonly SessionFactory $customerSession
    )
    {
    }

    /**
     * Around to html.
     *
     * @param Sidebar $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundToHtml(Sidebar $subject, \Closure $proceed): string
    {
        return $this->isLoggedInCustomer() ? $proceed() : '';
    }

    private function isLoggedInCustomer(): bool
    {
        $session = $this->customerSession->create();

        return $session->isLoggedIn();
    }
}
