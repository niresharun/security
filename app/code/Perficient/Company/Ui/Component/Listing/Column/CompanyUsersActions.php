<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */

namespace Perficient\Company\Ui\Component\Listing\Column;

use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Company\Ui\Component\Listing\Column\CompanyUsersActions as CoreCompanyUsersActions;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\RedirectInterface;
use Perficient\Company\Helper\Data;

/**
 * Class CompanyUsersActions.
 */
class CompanyUsersActions extends CoreCompanyUsersActions
{
    const MAGENTO_TITLE = 'Company Users';
    const COMPANY_EMPLOYEE = 'Customer Employee';
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const COMPANY_ADMINISTRATOR = "Company Administrator";
    const RESOURCE_TYPE = 'emp';
    /**
     * Url interface.
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    private string $customerStatusActive = 'Active';
    /**
     * @var \Magento\Company\Api\RoleManagementInterface
     */
    private $roleManagement;
    /**
     * @var \Magento\Company\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * CompanyUsersActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param Session $currentCustomerSession
     * @param RedirectInterface $redirectInterface
     */
    public function __construct(
        ContextInterface                             $context,
        UiComponentFactory                           $uiComponentFactory,
        UrlInterface                                 $urlBuilder,
        \Magento\Company\Api\RoleManagementInterface $roleManagement,
        \Magento\Company\Api\AuthorizationInterface  $authorization,
        private readonly RoleInfo                    $roleInfo,
        private readonly Escaper                     $escaper,
        private readonly Session                     $currentCustomerSession,
        private readonly RedirectInterface           $redirectInterface,
        private readonly Data                        $customHelper,
        array                                        $components = [],
        array                                        $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $urlBuilder, $roleManagement, $authorization, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->roleManagement = $roleManagement;
        $this->authorization = $authorization;
    }

    /**
     * Prepare Data Source.
     */
    public function prepareDataSource(array $dataSource): array
    {
        $checkForUserTypeLink = $this->checkForUserTypeLink();
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if ($this->authorization->isAllowed('Magento_Company::users_edit') && isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => &$item) {
                $currentLoggedInUserId = $this->currentCustomerSession->getCustomerData()->getId();
                if ($currentUserRole[0] != self::COMPANY_ADMINISTRATOR) {
                    if ($item['user_actual_parent_id'] != $currentLoggedInUserId) {
                        unset($dataSource['data']['items'][$key]);
                    }
                }
                $getUrl = $this->urlBuilder->getUrl('company/customer/get');
                $provider = 'company_users_listing.company_users_listing_data_source';
                if ($currentUserRole[0] == self::COMPANY_ADMINISTRATOR) {
                    $item[$this->getData('name')]['edit'] = [
                        'href' => '#',
                        'label' => __('Edit'),
                        'hidden' => false,
                        'type' => 'edit-user',
                        'options' => [
                            'getUrl' => $getUrl,
                            'getUserUrl' => $getUrl . '?customer_id=' . $item['entity_id'],
                            'saveUrl' => $this->urlBuilder->getUrl('company/customer/manage'),
                            'id' => $item['entity_id'],
                            'gridProvider' => $provider,
                            'adminUserRoleId' => $this->roleManagement->getCompanyAdminRoleId(),
                        ],
                    ];
                    $item[$this->getData('name')]['delete'] = [
                        'href' => '#',
                        'label' => __('Delete'),
                        'hidden' => false,
                        'id' => $item['entity_id'],
                        'type' => 'delete-user',
                        'options' => [
                            'setInactiveUrl' => $this->urlBuilder->getUrl('company/customer/delete'),
                            'deleteUrl' => $this->urlBuilder->getUrl('company/customer/permanentDelete'),
                            'id' => $item['entity_id'],
                            'gridProvider' => $provider,
                            'inactiveClass' => $this->getSetInactiveButtonClass($item),
                        ],
                    ];
                }
                if ((isset($currentUserRole) && is_array($currentUserRole)
                        && isset($currentUserRole[0])
                        && !empty($currentUserRole[0]))
                    && ($currentUserRole[0] == self::COMPANY_EMPLOYEE)
                    && ($item['role_name'] == self::CUSTOMER_CUSTOMER)) {
                    $item[$this->getData('name')]['edit'] = [
                        'href' => '#',
                        'label' => __('Edit'),
                        'hidden' => false,
                        'type' => 'edit-user',
                        'options' => [
                            'getUrl' => $getUrl,
                            'getUserUrl' => $getUrl . '?customer_id=' . $item['entity_id'],
                            'saveUrl' => $this->urlBuilder->getUrl('company/customer/manage'),
                            'id' => $item['entity_id'],
                            'gridProvider' => $provider,
                            'adminUserRoleId' => $this->roleManagement->getCompanyAdminRoleId(),
                        ],
                    ];
                    $item[$this->getData('name')]['delete'] = [
                        'href' => '#',
                        'label' => __('Delete'),
                        'hidden' => false,
                        'id' => $item['entity_id'],
                        'type' => 'delete-user',
                        'options' => [
                            'setInactiveUrl' => $this->urlBuilder->getUrl('company/customer/delete'),
                            'deleteUrl' => $this->urlBuilder->getUrl('company/customer/permanentDelete'),
                            'id' => $item['entity_id'],
                            'gridProvider' => $provider,
                            'inactiveClass' => $this->getSetInactiveButtonClass($item),
                        ],
                    ];
                }


            }
        }
        if ((isset($currentUserRole)
                && is_array($currentUserRole)
                && isset($currentUserRole[0])
                && !empty($currentUserRole[0]))
            && $currentUserRole[0] != self::COMPANY_ADMINISTRATOR) {
            foreach ($dataSource['data']['items'] as $key => $value) {
                if (is_numeric($dataSource['data']['items'][$key]['price_multiplier'])) {
                    if ($dataSource['data']['items'][$key]['price_multiplier'] == '0') {
                        $dataSource['data']['items'][$key]['price_multiplier'] = '0.00';
                    }
                    $dataSource['data']['items'][$key]['price_multiplier'] = $dataSource['data']['items'][$key]['price_multiplier'] . 'X';
                }
                if ($value['role_name'] == self::COMPANY_EMPLOYEE) {
                    $dataSource['data']['items'][$key]['role_name'] = Data::LABEL_COMPANY_EMPLOYEE;
                }
                if ($value['role_name'] == self::CUSTOMER_CUSTOMER) {
                    $dataSource['data']['items'][$key]['role_name'] = Data::LABEL_CUSTOMER_CUSTOMER;
                }
            }
            $dataSource['data']['items'] = array_values(array_filter($dataSource['data']['items']));
            $dataSource['data']['totalRecords'] = count($dataSource['data']['items']);
        } else {
            foreach ($dataSource['data']['items'] as $key => $value) {
                if (is_numeric($dataSource['data']['items'][$key]['price_multiplier'])) {
                    if ($dataSource['data']['items'][$key]['price_multiplier'] == '0') {
                        $dataSource['data']['items'][$key]['price_multiplier'] = '0.00';
                    }
                    $dataSource['data']['items'][$key]['price_multiplier'] = $dataSource['data']['items'][$key]['price_multiplier'] . 'X';
                }

                if ($value['role_name'] == self::COMPANY_EMPLOYEE) {
                    $dataSource['data']['items'][$key]['role_name'] = Data::LABEL_COMPANY_EMPLOYEE;
                }
                if ($value['role_name'] == self::CUSTOMER_CUSTOMER) {
                    $dataSource['data']['items'][$key]['role_name'] = Data::LABEL_CUSTOMER_CUSTOMER;
                }
//                if ($checkForUserTypeLink === false) {
//                    if($value['role_name'] == self::COMPANY_EMPLOYEE){
//                        unset($dataSource['data']['items'][$key]);
//                    }
//					 if($value['role_name'] == self::COMPANY_ADMINISTRATOR){
//                        unset($dataSource['data']['items'][$key]);
//                    }
//                } else {
//                    if($value['role_name'] == self::CUSTOMER_CUSTOMER){
//                        unset($dataSource['data']['items'][$key]);
//                    }
//
//                }
            }

            $dataSource['data']['items'] = array_values(array_filter($dataSource['data']['items']));
            $dataSource['data']['totalRecords'] = count($dataSource['data']['items']);
        }

        return $dataSource;
    }


    /**
     * Get set inactive button class.
     */
    private function getSetInactiveButtonClass(array $userData): string
    {
        return ($this->isShowSetInactiveButton($userData)) ? '' : '_hidden';
    }

    /**
     * Is show set inactive button.
     */
    private function isShowSetInactiveButton(array $userData): bool
    {
        return (!empty($userData['status']) && $userData['status']->getText() == $this->customerStatusActive);
    }

    public function checkForUserTypeLink(): bool|int
    {
        $getRefererUrl = $this->redirectInterface->getRedirectUrl();

        return strpos((string)$getRefererUrl, self::RESOURCE_TYPE);
    }
}
