<?php
/**
 * To Unset the customer Id from session before creating new company account.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Ankita Bodhankar <ankita.bodhankar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Plugin\Company\Controller\Account;

use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class CreatePost
 * @package Perficient\Company\Plugin\Company\Controller\Account
 */
class CreatePost
{
    /**
     * CreatePost constructor.
     */
    public function __construct(
        private readonly CustomerSession $customerSession
    )
    {
    }

    /**
     * To Unset the customerId,Customer GroupId and Customer Emulator from session before creating new company account from frontend.
     */
    public function beforeExecute(): void
    {
        /* WENDOVER-488 - Customers Missing Role - Wrong Companies
           Added this plugin to unset the customer Id,Customer Group Id and Customer Emulator
           from session as this was creating problem in registration where it was not creating company
           and assigning the customer to some random company  */

        $this->customerSession->unsCustomerId();
        $this->customerSession->unsCustomerGroupId();
        $this->customerSession->unsIsCustomerEmulated();
    }
}
