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
declare(strict_types=1);

namespace Perficient\Company\Plugin\Manage;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\RoleRepositoryInterface as MagentoRolesFactory;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Company\Block\Company\CompanyProfile;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Escaper;
use Magento\Newsletter\Controller\Manage\Save as ParentSave;

/**
 * CompanyRepository plugin for saving purchase order company config
 */
class Save
{
    const IS_SUBSCRIBED = 1;
    const SUBSCRIBER_ACTIVE = 'yes';
    const SUBSCRIBER_INACTIVE = 'no';
    const COMPANY_ADMINISTRATOR = 'Company Administrator';

    /**
     * Save constructor.
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Session $currentCustomerSession
     * @param CompanyProfile $companyProfile
     * @param CompanyManagementInterface $companyRepository
     */
    public function __construct(
        private readonly RoleInfo                   $roleInfo,
        private readonly Escaper                    $escaper,
        private readonly MagentoRolesFactory        $magentoRolesFactory,
        private readonly SearchCriteriaBuilder      $searchCriteriaBuilder,
        private readonly Session                    $currentCustomerSession,
        private readonly CompanyProfile             $companyProfile,
        private readonly CompanyManagementInterface $companyRepository
    )
    {
    }

    public function afterExecute(ParentSave $subject): void
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);
        if ($currentUserRole[0] == self::COMPANY_ADMINISTRATOR) {
            $adminId = $this->currentCustomerSession->getId();
            $company = $this->companyRepository->getByCustomerId($adminId);
            $isSubscribedParam = (boolean)$subject->getRequest()
                ->getParam('is_subscribed', false);
            if ($isSubscribedParam == self::IS_SUBSCRIBED) {
                $company->setNewsletter(self::SUBSCRIBER_ACTIVE)->save();
            } else {
                $company->setNewsletter(self::SUBSCRIBER_INACTIVE)->save();
            }
        }
    }
}
