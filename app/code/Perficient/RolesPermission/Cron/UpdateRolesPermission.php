<?php
/**
 * This module is used update all company roles and permissions
 *
 * @category: Magento
 * @package: Perficient/RolesPermission
 * @copyright: Copyright  - 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sreedevi Selvaraj <sreedevi.selvaraj@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_RolesPermission
 */
declare(strict_types=1);

namespace Perficient\RolesPermission\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface as Logger;
use Perficient\RolesPermission\Model\CompanyRolesUpdate;

/**
 * Class UpdateRolesPermission
 * @package Perficient\RolesPermission\Cron
 */
class UpdateRolesPermission
{
    /**
     * Constant for cron enable/disable config value
     */
    const XML_PATH_CRON_ENABLED = 'rolespermissions/cron_settings/enabled';

    /**
     * UpdateRolesPermission constructor.
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly ScopeConfigInterface $scopeConfig,
        protected Logger $logger,
        protected CompanyRolesUpdate $companyRolesUpdate
    ) {
    }

    /**
     * Implementation of abstract function to import cron
     *
     * @return $this
     */
    public function execute()
    {
        $iscronEnable = $this->getConfigValue(SELF::XML_PATH_CRON_ENABLED);
        if(!$iscronEnable) {
            $message = __('Roles and Permissions cron service is disabled.');
            $this->logger->error($message);
            return $this;
        }
        $isSuccess = $this->companyRolesUpdate->updateCompanyPermissionByQuery();

        $this->logger->info('successfully updated');
        return $this;
    }

    /**
     * @param $xmlPath
     * @param null $storeId
     * @return mixed
     */
    protected function getConfigValue($xmlPath, $storeId = null)
    {
        return $this->scopeConfig->getValue($xmlPath, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
