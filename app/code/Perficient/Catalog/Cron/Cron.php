<?php
/**
 * This module is used to bulk image upload
 *
 * @category:  Magento
 * @package:   Perficient/Catalog
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * @license:   Magento Enterprise Edition (MEE) license
 * @author:    Vijayashanthi M
 * @project:   Wendover
 * @keywords:  Module Perficient_Catalog
 * See COPYING.txt for license details.
 */

namespace Perficient\Catalog\Cron;

use \Magento\Framework\Controller\ResultFactory;

/**
 * Class Result
 *
 * @package Perficient\Catalog\Cron
 */
class Cron
{

    const ADMIN_RESOURCE = 'Magento_Catalog::catalog';
    const XML_PATH_CRON_EXPRESSION = 'perficient_bulk_upload/cron_setup/bulk_upload_enable';

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        protected \Perficient\Catalog\Block\GetData                  $getData,
        protected \Psr\Log\LoggerInterface                           $logger)
    {
    }

    /**
     * Execute Method
     *
     * @return  void
     */
    public function execute()
    {
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $checkEnable = $this->scopeConfig->getValue(self::XML_PATH_CRON_EXPRESSION, $storeScope);
            if (!$checkEnable) {
                return false;
            }
            $this->getData->coreLogic();
            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}

