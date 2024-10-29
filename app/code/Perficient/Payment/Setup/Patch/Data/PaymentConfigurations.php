<?php
/**
 * Customer Specific Payment Methods
 * @category: Magento
 * @package: Perficient/Payment
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Payment
 */
declare(strict_types=1);

namespace Perficient\Payment\Setup\Patch\Data;

use Exception;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class PaymentConfigurations
 * @package Perficient\Payment\Setup\Patch\Data
 */
class PaymentConfigurations implements DataPatchInterface
{
    protected array $configData = [
        'payment/authnetcim/active' => '1',
        'payment/authnetcim/title' => 'Pay Now with Credit Card',
        'payment/authnetcim/test' => '1',
        'payment/authnetcim/acceptjs' => '1',
        'payment/authnetcim/payment_action' => 'authorize',
        'payment/authnetcim/order_status' => 'processing',
        'payment/authnetcim/validation_mode' => 'testMode',
        'payment/authnetcim/show_branding' => '1',
        'payment/authnetcim/cctypes' => 'AE,VI,MC,DI',
        'payment/authnetcim/useccv' => '1',
        'payment/authnetcim/allow_unsaved' => '1',
        'payment/authnetcim/allowspecific' => '1',
        'payment/authnetcim/specificcountry' => 'CA,US',
        'payment/authnetcim/sort_order' => '222',
        'payment/authnetcim/require_ccv' => '1',
        'payment/authnetcim/send_shipping_address' => '1',
        'payment/authnetcim/send_line_items' => '1',
        'payment/authnetcim/reauthorize_partial_invoice' => '1',
        'payment/authnetcim/savecard_opt_out' => '1',
        'payment/authnetcim/verify_ssl' => '1',
        'payment/authnetcim_ach/active' => '1',
        'payment/authnetcim_ach/title' => 'Pay Now with eCheck',
        'payment/authnetcim_ach/test' => '1',
        'payment/authnetcim_ach/payment_action' => 'authorize',
        'payment/authnetcim_ach/order_status' => 'processing',
        'payment/authnetcim_ach/validation_mode' => 'none',
        'payment/authnetcim_ach/show_branding' => '1',
        'payment/authnetcim_ach/allow_unsaved' => '1',
        'payment/authnetcim_ach/allowspecific' => '1',
        'payment/authnetcim_ach/specificcountry' => 'CA,US',
        'payment/authnetcim_ach/sort_order' => '333',
        'payment/authnetcim_ach/send_shipping_address' => '0',
        'payment/authnetcim_ach/send_line_items' => '0',
        'payment/authnetcim_ach/reauthorize_partial_invoice' => '0',
        'payment/authnetcim_ach/savecard_opt_out' => '0',
        'payment/authnetcim_ach/verify_ssl' => '0'
    ];

    /**
     * PaymentConfigurations constructor.
     */
    public function __construct(
        private readonly WriterInterface $configWriter,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * Apply the change for website configuration changes
     * {@inheritdoc}
     * @throws Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->paymentConfiguration();
        $this->moduleDataSetup->getConnection()->endSetup();
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
     * Enabling Requisition Lists
     * @throws Exception
     */
    private function paymentConfiguration()
    {
        try {
            foreach ($this->configData as $key => $value) {
                $this->configWriter->save(
                    $key,
                    $value,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    Store::DEFAULT_STORE_ID
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
