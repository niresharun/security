<?php
/**
 * AvaTax Configuration
 *
 * @category : PHP
 * @package  : Perficient_AvaTax
 * @copyright: Copyright © 2020 Magento. All rights reserved.
 * @license  : Perficient, Inc.
 * @author   : Archana Lohakare <archana.lohakare@perficient.com>
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
class AvataxConfiguration implements DataPatchInterface
{
    /**
     * @var array Core Config Path Array
     */
    private array $coreConfigPaths = [
        'tax/avatax/enabled' => 1,
        'tax/avatax/tax_mode' => 2
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
