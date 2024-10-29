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

namespace Perficient\Company\Plugin\Adminhtml\Index;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\RoleRepositoryInterface as MagentoRolesFactory;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Company\Block\Company\CompanyProfile;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Controller\Adminhtml\Index\Save as ParentClass;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Escaper;
use Magento\Framework\App\RequestInterface;

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

    public function beforeExecute(ParentClass $subject)
    {
        $originalRequestData = $subject->getRequest()->getPostValue(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER);
        if (isset($originalRequestData['entity_id']) && !empty($originalRequestData['entity_id'])) {
            $adminId = $originalRequestData['entity_id'];
            $company = $this->companyRepository->getByCustomerId($adminId);
            $isSubscribedParam = (array)$subject->getRequest()->getParam('subscription_status');
            if ($company && isset($isSubscribedParam[1]) && $isSubscribedParam[1] == self::IS_SUBSCRIBED) {
                $company->setNewsletter(self::SUBSCRIBER_ACTIVE)->save();
            } else if($company)  {
                $company->setNewsletter(self::SUBSCRIBER_INACTIVE)->save();
            }
        }
    }
}
