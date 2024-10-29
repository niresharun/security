<?php
/**
 * Added to handle product surcharge if minimum order amount not met by customer
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */

namespace Perficient\Order\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;

class CurrentQuote extends DataObject
{
    /**
     * QuoteOperations constructor.
     * @param Session $checkoutSession
     */
    public function __construct(
        protected Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        if (!$this->hasData('quote')) {
            $this->setData('quote', $this->checkoutSession->getQuote());
        }
        return $this->_getData('quote');
    }
}
