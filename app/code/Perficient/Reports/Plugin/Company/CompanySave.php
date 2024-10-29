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

use Magento\Framework\App\RequestInterface;
use Perficient\Reports\Helper\SaveCompany;

/**
 * Class CompanySave
 * @package Perficient\Reports\Plugin\Company
 */
class CompanySave
{
    /**
     * CompanySave constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly SaveCompany $saveCompany,
        array $data = []
    ) {
    }

    /**
     * @param $subject
     */
    public function beforeExecute($subject) {

        $companyId = $this->request->getParam('id') ?: null;
        if(!$companyId) {
            return;
        }

        $this->saveCompany->logCompanyChangeInfo($companyId);
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
