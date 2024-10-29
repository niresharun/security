<?php
/**
 * Customer update parent on Company change.
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Customer
 */
declare(strict_types=1);

namespace Perficient\Customer\Plugin\Adminhtml\Index;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\Adminhtml\Index\Save;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Psr\Log\LoggerInterface;

/**
 * Class SavePlugin
 * @package Perficient\Customer\Plugin\Adminhtml\Index
 */
class SavePlugin
{
    /**
     * SavePlugin constructor.
     */
    public function __construct(
        private readonly CompanyManagementInterface  $companyManagement,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly LoggerInterface             $logger
    )
    {

    }

    /**
     * @param Save $subject
     * @param $result
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputMismatchException
     */
    public function afterExecute(Save $subject, $result): mixed
    {
        $post = $subject->getRequest()->getPostValue();
        $company_id = $post['customer']['extension_attributes']['company_attributes']['company_id'];
        if (isset($company_id) && !empty($company_id)) {
            $currentCustomerId = $post['customer']['entity_id'];
            $company = $this->companyManagement->getByCustomerId($currentCustomerId);
            if (!empty($company) && $company->getStatus() == 1) {
                $superUserId = $company->getSuperUserId();
                $customer = $this->customerRepository->getById($currentCustomerId);
                if ($customer) {
                    try {
                        $customer->setCustomAttribute("user_actual_parent_id", $superUserId);
                        $this->customerRepository->save($customer);
                    } catch (LocalizedException $exception) {
                        $this->logger->error($exception);
                    }
                }
            }
        }

        return $result;
    }
}
