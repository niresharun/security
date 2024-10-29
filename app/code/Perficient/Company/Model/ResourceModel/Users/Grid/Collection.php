<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Divya Sree <divya.sree@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Model\ResourceModel\Users\Grid;

use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Locale\ResolverInterface;

/**
 * Class Collection.
 */
class Collection extends \Magento\Company\Model\ResourceModel\Users\Grid\Collection
{

    const MAGENTO_TITLE = 'Company Users';
    const COMPANY_EMPLOYEE = 'Customer Employee';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const COMPANY_ADMINISTRATOR = "Company Administrator";
    const RESOURCE_TYPE = 'emp';
    const COOKIE_NAME = 'customerOrEmpTotalRecords';

    /**
     * Collection constructor.
     * @param RoleInfo $roleInfo
     * @param RedirectInterface $redirectInterface
     * @param Http $request
     * @param State $state
     * @param Session $customerSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     * @param string $mainTable
     * @param string $resourceModel
     */

    public function __construct(
        private readonly RoleInfo               $roleInfo,
        private readonly RedirectInterface      $redirectInterface,
        EntityFactory                           $entityFactory,
        Logger                                  $logger,
        private readonly Http                   $request,
        protected State                         $state,
        protected Session                       $customerSession,
        private readonly CookieMetadataFactory  $cookieMetadataFactory,
        private readonly CookieManagerInterface $cookieManager,
        FetchStrategy                           $fetchStrategy,
        EventManager                            $eventManager,
        ResolverInterface                       $resolver,
                                                $mainTable = 'customer_grid_flat',
                                                $resourceModel = \Magento\Customer\Model\ResourceModel\Customer::class
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resolver, $mainTable, $resourceModel);
    }

    /**
     * @return $this|\Magento\Company\Model\ResourceModel\Users\Grid\Collection
     */
    protected function _initSelect(): static|\Magento\Company\Model\ResourceModel\Users\Grid\Collection
    {
        parent::_initSelect();
        $this->customerRole();
        return $this;
    }

    /**
     * Customer Role
     */
    public function customerRole(): void
    {
        $checkForUserTypeLink = $this->checkForUserTypeLink();
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        if ((isset($currentUserRole)
                && is_array($currentUserRole)
                && isset($currentUserRole[0])
                && !empty($currentUserRole[0]))
            && $currentUserRole[0] != self::COMPANY_ADMINISTRATOR) {
        } else {
            if ($checkForUserTypeLink === false) {
                $this->addFieldToFilter('role.role_name', ['nin' => [self::COMPANY_EMPLOYEE, self::COMPANY_ADMINISTRATOR]]);

            } else {
                $this->addFieldToFilter('role.role_name', ['nin' => self::CUSTOMER_CUSTOMER]);
            }
        }
    }

    public function checkForUserTypeLink(): bool|int
    {
        $getRefererUrl = $this->redirectInterface->getRedirectUrl();

        return strpos((string)$getRefererUrl, self::RESOURCE_TYPE);
    }

    protected function _renderLimit()
    {
        if ($this->_pageSize) {
            $this->_select->limitPage($this->getCurPage(), $this->_pageSize);
        }

        /**
         * Fixes for ticket WENDOVER-506 pagination not showing
         */
        $areaCode = $this->state->getAreaCode();
        $route = $this->request->getRouteName();
        $controller = $this->request->getControllerName();
        $action = $this->request->getActionName();
        if (!empty($this->getSize()) && $areaCode == Area::AREA_FRONTEND
            && $route != 'company' && $controller != 'users' && $action != 'index') {
            return $this;
        } else {
            $this->setCustomerOrEmpTotalRecords($this->getSize());
        }

        return $this;
    }

    /**
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function setCustomerOrEmpTotalRecords($value): void
    {
        /**
         * Start Fixes for ticket WENDOVER-506 pagination not showing
         * set cookie to get totalRecord in paging.js
         */
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->customerSession->getCookiePath())
            ->setDomain($this->customerSession->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $value,
            $metadata
        );
    }
}
