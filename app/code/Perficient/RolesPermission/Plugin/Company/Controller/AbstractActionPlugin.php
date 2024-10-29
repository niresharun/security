<?php
/**
 * Plugin to disallow access of company roles permission
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@Perficient.com>
 * @keywords: Company template for roles permission
 */
declare(strict_types=1);

namespace Perficient\RolesPermission\Plugin\Company\Controller;

use Magento\Company\Controller\AbstractAction;
use Perficient\RolesPermission\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Model\CompanyContext;
use Psr\Log\LoggerInterface;

class AbstractActionPlugin
{
    /**
     * Role Controller
     */
    const ROLE_CONTROLLER = 'role';

    /**
     * AbstractActionPlugin constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyManagementInterface $companyRepository
     * @param CompanyContext $companyContext
     * @param LoggerInterface $logger
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyRepository,
        private readonly CompanyContext $companyContext,
        private readonly LoggerInterface $logger,
        private readonly ResultFactory $resultFactory
    ) {
    }

    /**
     * @param AbstractAction $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterDispatch(AbstractAction $subject, $result)
    {
        if ($subject->getRequest()->getControllerName() == SELF::ROLE_CONTROLLER) {
            $customerId = $this->companyContext->getCustomerId();
            if ($customerId) {
                try {
                    $company = $this->companyRepository->getByCustomerId($customerId);
                    $customer = $this->customerRepository->getById($customerId);
                    if ($company->getCompanyEmail() != Data::COMPANY_EMAIL
                    && $customer->getEmail() != Data::WENDOVER_COMPANY_ADMIN_EMAIL) {
                        $resultRedirect = $this->resultFactory->create(
                            ResultFactory::TYPE_REDIRECT
                        );
                        $result = $resultRedirect->setUrl('company/accessdenied');
                    }
                } catch (NoSuchEntityException $e) {
                    $this->logger->critical($e);
                }
            }
        }
        return $result;
    }

}
