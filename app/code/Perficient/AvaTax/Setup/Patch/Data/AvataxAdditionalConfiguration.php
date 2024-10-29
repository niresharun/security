<?php
/**
 * AvaTax Configuration
 *
 * @category : PHP
 * @package  : Perficient_AvaTax
 * @copyright: Copyright © 2021 Magento. All rights reserved.
 * @license  : Perficient, Inc.
 * @author   : Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords : Perficient AvaTax, Configuration
 */
declare(strict_types = 1);

namespace Perficient\AvaTax\Setup\Patch\Data;

use Exception;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Class AvataxConfiguration
 * @package Perficient\AvaTax\Setup\Patch\Data
 */
class AvataxAdditionalConfiguration implements DataPatchInterface
{

    /**
     * @var array Core Config Path Array
     */
    private array $coreConfigPaths = [
        'tax/avatax/customer_code_format' => 'syspro_customer_id',
        'tax/avatax/shipping_tax_code' => 'FR020100',
        'tax/avatax/address_validation_enabled' => 1,
        'tax/avatax/address_validation_user_has_choice' => 1,
        'tax/avatax/address_validation_countries_enabled' => 'CA,US',

    ];

    /**
     * Scope Id
     */
    final public const SCOPE_ID = '0';

    /**
     * Scope
     */
    final public const SCOPE = 'default';

    /**
     * AvataxConfiguration constructor.
     */
    public function __construct(
        private readonly ConfigInterface $resourceConfig
    ) {
    }

    /**
     * Avatax configuration changes
     * @return $this|void
     * @throws Exception
     */
    public function apply()
    {
        $this->AvataxConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Avatax configuration changes
     *
     * @throws Exception
     */
    private function AvataxConfiguration()
    {
        try {
            foreach ($this->coreConfigPaths as $configPath => $configValue) {
                $this->resourceConfig->saveConfig(
                    $configPath,
                    $configValue,
                    'default',
                    self::SCOPE_ID
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
