<?php
/**
 * This file is used to get the customer id from session factory
 *
 * @category: Magento
 * @package: Perficient/RolesPermission
 * @copyright: Copyright  - 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_RolesPermission
 */
declare(strict_types=1);
namespace Perficient\RolesPermission\Plugin\Customer\Model\Authorization;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Customer\Model\Authorization\CustomerSessionUserContext as ParentClass;

/**
 * Class CustomerSessionContextPlugin
 * @package Perficient\RolesPermission\Plugin\Customer\Model\Authorization
 */
class CustomerSessionContextPlugin
{
    /**
     * CustomerSessionContextPlugin constructor.
     * @param SessionFactory $sessionFactory
     */
    public function __construct(
        protected SessionFactory $sessionFactory,
        private readonly HttpRequest $httpRequest
    ) {
    }

    /**
     * @param $result
     * @return mixed
     */
    public function afterGetUserId(ParentClass $subject, $result)
    {
        $moduleName = $this->httpRequest->getModuleName();
        $controller = $this->httpRequest->getControllerName();
        $action = $this->httpRequest->getActionName();

        /* Added the check of module, controller and action
           as this was creating problem in registration
           where it was not creating company and assigning the
           customer to some random company  */
        if ($moduleName == 'company' && $controller == 'role' && $action == 'index'
            && $result == null) {
            $session = $this->sessionFactory->create();
            $result = $session->getId();
        }

        return $result;
    }
}

