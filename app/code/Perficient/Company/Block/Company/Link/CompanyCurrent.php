<?php
/**
 * Override to hide link of as per ACL
 *
 * @category: Perficient's Modules
 * @package: Perficient\RolesPermission
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @keywords: Company template for roles permission
 */

namespace Perficient\Company\Block\Company\Link;

use Magento\Company\Block\Link\Current;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Company\Model\CompanyContext;
use Magento\Company\Api\AuthorizationInterface;

class CompanyCurrent extends Current
{
    /**
     * My Project url key
     */
    const MY_PROJECT_URL_PATH = 'wishlist/myprojects';

    /**
     * Customer Display Information url key
     */
    const CUSTOMER_DISPLAY_INFORMATION_URL_PATH = 'noroute_customer_display_info';

    /**
     * My Project Auth key
     */
    const AUTH_MY_PROJECT = 'Perficient_Company::myproject';

    /**
     * Customer Display Information AUTH key
     */
    const AUTH_COMPANY_DISPLAY_INFO = 'Perficient_Company::company_display_information';

    /**
     * @var \Magento\Company\Model\CompanyContext
     */
    private $companyContext;

    /**
     * Current constructor.
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CompanyContext $companyContext
     * @param AuthorizationInterface $authorization
     * @internal param CustomerRepositoryInterface $customerRepository
     * @internal param CompanyManagementInterface $companyRepository
     */
    public function __construct(
        Context                                 $context,
        DefaultPathInterface                    $defaultPath,
        CompanyContext                          $companyContext,
        private readonly AuthorizationInterface $authorization,
        array                                   $data = []
    )
    {
        parent::__construct($context, $defaultPath, $companyContext, $data);
        $this->companyContext = $companyContext;
    }

    protected function _toHtml(): string
    {
        if ($this->isVisible()) {
            if (str_contains((string)$this->getPath(),
                    self::MY_PROJECT_URL_PATH) && !$this->authorization->isAllowed(self::AUTH_MY_PROJECT)) {

                return '';
            } else {
                if (str_contains((string)$this->getPath(),
                        self::CUSTOMER_DISPLAY_INFORMATION_URL_PATH) && !$this->authorization->isAllowed(self::AUTH_COMPANY_DISPLAY_INFO)) {
                    return '';
                }
            }

            return parent::_toHtml();
        }
        return '';
    }
}
