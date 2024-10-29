<?php
/**
 * This module is used to add base configurations
 *
 * @category: PHP
 * @package: Perficient_Base
 * @copyright: Copyright © 2020 Magento. All rights reserved.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mukherjee <sandeep.mukherjee@perficient.com>
 * @keywords: Update Sales Representative Sender Email
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;

/**
 * Class UpdateSalesRepresentativeEmail
 * @package Perficient\Base\Setup\Patch\Data
 */
class UpdateSalesRepresentativeEmail implements DataPatchInterface
{
    /**
     * UpdateSalesRepresentativeEmail constructor.
     * @param ConfigInterface $resourceConfig
     */
    public function __construct(
        protected ConfigInterface $resourceConfig
    ) {
    }

    /**
     * Update Sales Representative Sender Email
     */
    public function apply(): void
    {
        $this->resourceConfig->saveConfig(
            'trans_email/ident_sales/email',
            'sales@wendoverart.com',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID
        );

        $this->resourceConfig->saveConfig(
            'trans_email/ident_support/email',
            'info@wendoverart.com',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            Store::DEFAULT_STORE_ID
        );
    }

    /**
     * The default magento OOB method used to get dependencies.
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Returns the aliases for the command.
     */
    public function getAliases(): array
    {
        return [];
    }
}
