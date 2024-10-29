<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvarj <Sreedevi.Selvaraj@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Plugin\Adminhtml\Index;

use Magento\Backend\Model\Session as BackendSession;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Controller\Adminhtml\Index\Save as ParentClass;
use Magento\Framework\App\RequestInterface;

/**
 * CompanyController plugin for saving previous admin user role
 */
class SaveCompany
{
    /**
     * SaveCompany constructor.
     * @param CompanyManagementInterface $companyManagement
     */
    public function __construct(
        protected CompanyManagementInterface $companyManagement,
        protected BackendSession $session,
        protected RequestInterface $request
    ) {
    }

    public function beforeExecute(ParentClass $subject)
    {
        // Unset the taxvat field.
        $params = $this->request->getParams();
        if (isset($params['company_admin']) && isset($params['company_admin']['taxvat'])) {
            unset($params['company_admin']['taxvat']);
            $this->request->setParams($params);
        }

        //set previous admin customer id in session for making the user role as employee if company admin customer changed\
        $companyId = $this->request->getParam('id');
        $companyAdmin = $this->companyManagement->getAdminByCompanyId($companyId);
        if (!empty($companyAdmin)) {
            $this->session->setData('previousCompanyAdminId', $companyAdmin->getId());
        }
    }
}
