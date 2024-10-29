<?php
/**
 * New column in customer grid
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj<Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Customer\Ui\Component\Listing\Column;

use Magento\Company\Model\UserRoleManagement;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class UserRole
 * @package Perficient\Customer\Ui\Component\Listing\Column
 */
class UserRole extends Column
{
    /**
     * UserRole constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     */
    public function __construct(
        ContextInterface                      $context,
        UiComponentFactory                    $uiComponentFactory,
        protected CustomerRepositoryInterface $_customerRepository,
        protected SearchCriteriaBuilder       $_searchCriteria,
        private readonly UserRoleManagement   $roleManagement,
        array                                 $components = [],
        array                                 $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        // getting user role of the customer and display it in admin grid
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $customer = $this->_customerRepository->getById($item["entity_id"]);
                $customer_id = $customer->getId();
                $userRoles = $this->roleManagement->getRolesByUserId($customer_id);
                $userRoleName = null;
                foreach ($userRoles as $customerRole) {
                    $userRoleName = $customerRole->getRoleName();
                    break;
                }
                $item[$this->getData('name')] = $userRoleName;
            }
        }

        return $dataSource;
    }
}
