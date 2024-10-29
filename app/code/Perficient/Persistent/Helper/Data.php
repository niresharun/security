<?php
/**
 * Added for check whether the customer loggedin or not
 * @category: Magento
 * @package: Perficient/Persistent
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Persistent
 */
declare(strict_types=1);

namespace Perficient\Persistent\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Perficient\Persistent\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Data constructor.
     */
    public function __construct(
        protected Context $context,
        protected Session $customerSession
    ) {
        parent::__construct($context);
    }
    public function isCustomerLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }
}
