<?php
/**
 * Log Company Change Information
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

declare(strict_types=1);

namespace Perficient\Reports\Plugin\Company;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Model\CompanyContext;
use Magento\Framework\App\RequestInterface;
use Perficient\Reports\Helper\SaveCompany;

/**
 * Class CompanyEdit
 * @package Perficient\Reports\Plugin\Company
 */
class CompanyEdit
{
    /**
     * CompanyEdit constructor.
     * @param RequestInterface $request
     * @param CompanyContext $companyContext
     * @param CompanyManagementInterface $companyManagement
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly SaveCompany $saveCompany,
        private readonly CompanyContext $companyContext,
        private readonly CompanyManagementInterface $companyManagement,
        array $data = []
    ) {
    }

    /**
     * @param $subject
     */
    public function beforeExecute($subject) {

        $customerId = $this->companyContext->getCustomerId();
        $company = $this->companyManagement->getByCustomerId($customerId);

        if(!$company) {
            return;
        }

        $this->saveCompany->logCompanyChangeInfo($company->getId());
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute($subject, $result) {

        $this->saveCompany->commitCompanySaveLog();
        return $result;
    }
}
