<?php
/**
 * Overide to hide link of company roles permission
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords: Company template for roles permission
 */

namespace Perficient\RolesPermission\Block\Company\Link;

use Magento\Company\Block\Link\Current as CompanyCurrent;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Company\Model\CompanyContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Perficient\RolesPermission\Helper\Data;

class Current extends CompanyCurrent
{
    /**
     * Role permission url key
     */
    const ROLES_URL_PATH = 'company/role';

    /**
     * @var \Magento\Company\Model\CompanyContext
     */
    private $companyContext;

    /**
     * Current constructor.
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CompanyContext $companyContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyManagementInterface $companyRepository
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CompanyContext $companyContext,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyRepository,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $companyContext, $data);
        $this->companyContext = $companyContext;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _toHtml()
    {
        if ($this->isVisible()) {
            $customerId = $this->companyContext->getCustomerId();
            if ($customerId) {
                try {
                    $company = $this->companyRepository->getByCustomerId($customerId);
                    $customer = $this->customerRepository->getById($customerId);
                    if ($company->getCompanyEmail() != Data::COMPANY_EMAIL
                        && $customer->getEmail() != Data::WENDOVER_COMPANY_ADMIN_EMAIL) {
                        if(str_contains((string) $this->getPath(), SELF::ROLES_URL_PATH)){
                            return '';
                        }
                    }
                } catch (NoSuchEntityException $e) {
                    throw new NoSuchEntityException(
                        __($e->getMessage())
                    );
                }
            }
            return parent::_toHtml();
        }
        return '';
    }

}
