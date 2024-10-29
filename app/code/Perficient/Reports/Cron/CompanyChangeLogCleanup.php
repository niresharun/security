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

namespace Perficient\Reports\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;
use Psr\Log\LoggerInterface as Logger;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent as PerficientLoggingEventResourceModel;
use Perficient\Reports\Model\PerficientLoggingEvent as PerficientLoggingEventModel;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges as PerficientLoggingEventChangesResourceModel;
use Perficient\Reports\Model\PerficientLoggingEventChanges as PerficientLoggingEventChangesModel;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent\Collection as PerficientLoggingEventCollection;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges\Collection as PerficientLoggingEventChangesCollection;


/**
 * Class CompanyChangeLogCleanup
 * @package Perficient\Reports\Cron
 */
class CompanyChangeLogCleanup
{

    /**
     * @const string
     */
    const CRON_ENABLE_SETTING  = 'company_reports/company_changes_cron/cron_settings/enabled';

    /**
     * @param Manager $moduleManager
     * @param ScopeConfigInterface $scopeConfig
     * @param string $process
     */
    public function __construct(
        protected Manager $moduleManager,
        protected Logger $logger,
        private readonly PerficientLoggingEventCollection $perficientLoggingEventCollection,
        private readonly PerficientLoggingEventChangesCollection $perficientLoggingEventChangesCollection,
        private readonly PerficientLoggingEventResourceModel $perficientLoggingEventResourceModel,
        private readonly PerficientLoggingEventModel $perficientLoggingEventModel,
        private readonly PerficientLoggingEventChangesResourceModel $perficientLoggingEventChangesResourceModel,
        private readonly PerficientLoggingEventChangesModel $perficientLoggingEventChangesModel,
        protected ScopeConfigInterface $scopeConfig,
        public $process = ''
    ) {
    }

    /**
     * Implementation of abstract function to import cron
     * @return $this
     */
    public function execute()
    {
        try {

            // Check if Cron is enabled
            if (!$this->isModuleEnabled()) {
                $this->logger->error(__('%1 cron service is disabled.', $this->process));
                return $this;
            }

            try {

                $this->perficientLoggingEventCollection->addFieldToFilter(['time','status'], [['lt' => date('Y-m-d', strtotime('-180 days'))],['eq' => 'pending']]);
                $logData = $this->perficientLoggingEventCollection->load();

                if((is_countable($logData->getItems()) ? count($logData->getItems()) : 0) <= 0) {
                    $this->logger->error(__('%1: No Company change log to delete', $this->process));
                    return $this;
                }

                /**
                 * @var PerficientLoggingEventModel $item
                 */
                foreach($logData->getItems() as $key => $item){

                    $this->perficientLoggingEventResourceModel->beginTransaction();

                    $logId =  $item->getId();
                    $this->perficientLoggingEventModel->setId($logId);
                    $this->perficientLoggingEventResourceModel->delete($this->perficientLoggingEventModel);

                    $this->perficientLoggingEventChangesCollection->addFilter('event_id', $logId);
                    $logChangesData = $this->perficientLoggingEventChangesCollection->load()->getFirstItem();

                    /** @var PerficientLoggingEventModel $logChangesData */
                    $eventChangeId = $logChangesData->getId();
                    if(!$eventChangeId) {
                        $this->perficientLoggingEventResourceModel->rollBack();
                        continue;
                    }

                    $this->perficientLoggingEventChangesModel->setId($eventChangeId);
                    $this->perficientLoggingEventChangesResourceModel->delete($this->perficientLoggingEventChangesModel);

                    $this->perficientLoggingEventResourceModel->commit();
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled() {
        $isModuleEnabled = false;
        if ($this->moduleManager->isEnabled('Perficient_Reports') && $this->getCronEnabledStatus()) {
            $isModuleEnabled = true;
        }

        return $isModuleEnabled;
    }

    /**
     * @return bool
     */
    protected function getCronEnabledStatus() {
        $isCronEnabled = $this->scopeConfig->getValue(self::CRON_ENABLE_SETTING,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        return $isCronEnabled?true:false;
    }
}
