<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Perficient\Company\Controller\Users;

use Magento\Company\Controller\Users\Index as ParentIndex;
use Magento\Framework\App\Action\Context;
use Magento\Company\Model\CompanyContext;
use Psr\Log\LoggerInterface;
use Perficient\Company\Helper\Data as CompanyHelper;

/**
 * Class Index.
 */
class Index extends ParentIndex
{
    const USER_TYPE = 'emp';
    protected $_request;

    public function __construct(
        Context                 $context,
        CompanyContext          $companyContext,
        LoggerInterface         $logger,
        protected CompanyHelper $companyHelper
    )
    {
        parent::__construct($context, $companyContext, $logger);
    }

    public function isAllowed()
    {
        $isB2cCustomer = $this->companyHelper->isB2cCustomer();
        // user type is to check the current page whether it is manage customer or employee logins
        $resourceType = $this->_request->getParam('resource_type') ?: '';
        $userType = strpos((string)$resourceType, self::USER_TYPE);

        if ($isB2cCustomer && ($userType === false)) {
            return false;
        } else {
            return true;
        }
    }
}
