<?php
/**
 * This module is used to disable base configurations settings
 *
 * @category: Magento
 * @package: Perficient/Base
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Hiral Jain <hiral.jain@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Base
 */
declare(strict_types=1);

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class DisableCronAndPerformance
 * @package Perficient\Base\Setup\Patch\Data
 */
class DisableCronAndPerformance implements DataPatchInterface
{
    /**
     * Constant for theme path.
     */
    final const THEME_DEFAULT = 'default';

    /**
     * DisableCronAndPerformance constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigInterface $resourceConfig
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly ConfigInterface $resourceConfig,
        private readonly StoreRepositoryInterface $storeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->updateCoreConfig();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get Default Store Id
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreId(): int|string
    {
        $storeId = '';
        $store = $this->storeRepository->get('admin');
        if ($store) {
            $storeId = $store->getId();
        }
        return $storeId;
    }



    /**
     * Method used to update the core config settings.
     *
     * @throws \Exception
     */
    private function updateCoreConfig()
    {
        try {

            // system/cron_scheduler/disabled_crons

            $this->resourceConfig->saveConfig(
                'system/cron_scheduler/disabled_crons',
                'ddg_automation_abandonedcarts,ddg_automation_campaign,ddg_automation_catalog_sync,ddg_automation_cleaner,ddg_automation_customer_subscriber_guest_sync,ddg_automation_email_templates,ddg_automation_importer,ddg_automation_integration_insights,ddg_automation_reviews_and_wishlist,ddg_automation_order_sync,ddg_automation_status,ddg_automation_sync_negotiable_quotes,staging_synchronize_entities_period,staging_remove_updates,staging_apply_version,negotiable_quote_send_emails,captcha_delete_expired_images,captcha_delete_old_attempts,magento_giftcardaccount_update_states,magento_giftcardaccount_generage_codes_pool,currency_rates_update,magento_salesarchive_archive_orders,magento_reward_expire_points,magento_reward_balance_warning_notification,paypal_fetch_settlement_reports,delta_feed_submission,full_feed_submission',
                self::THEME_DEFAULT,
                $this->getStoreId()
            );

            // General -> Advance Reporting

            $this->resourceConfig->saveConfig(
                'analytics/subscription/enabled',
                0,
                self::THEME_DEFAULT,
                $this->getStoreId()
            );

            // Catalog -> Catalog-> Catalog Search -> Enable Search suggestion
            $this->resourceConfig->saveConfig(
                'catalog/search/search_suggestion_enabled',
                0,
                self::THEME_DEFAULT,
                $this->getStoreId()
            );

            // Catalog -> Catalog-> Catalog Search -> Enable Search recommendations
            $this->resourceConfig->saveConfig(
                'catalog/search/search_recommendations_enabled',
                0,
                self::THEME_DEFAULT,
                $this->getStoreId()
            );

            // Catalog ->Email to friend ->Enabled
            $this->resourceConfig->saveConfig(
                'sendfriend/email/enabled',
                0,
                self::THEME_DEFAULT,
                $this->getStoreId()
            );

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
