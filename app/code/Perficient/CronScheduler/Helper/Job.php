<?php
/**
 * Provide details for the Cron jobs
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Helper;

use Magento\Cron\Model\ConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Job Helper
 */
class Job extends AbstractHelper
{
    const XML_PATH_DISABLED_CRONS = 'system/cron_scheduler/disabled_crons';
    const STATUS_DISABLED = 'disabled';
    const STATUS_ENABLED = 'enabled';

    /**
     * Class constructor
     * @param Context $context
     * @param ConfigInterface $cronConfig
     */
    public function __construct(
        Context $context,
        protected ConfigInterface $cronConfig
    ) {
        parent::__construct($context);
    }

    /**
     * Get the job data
     * @return array
     */
    public function getJobData()
    {
        $data = [];
        $configJobs = $this->cronConfig->getJobs();
        foreach ($configJobs as $group => $jobs) {
            foreach ($jobs as $code => $job) {
                $job['code'] = $code;
                $job['group'] = $group;
                if (!isset($job['config_schedule'])) {
                    if (isset($job['schedule'])) {
                        $job['config_schedule'] = $job['schedule'];
                    } else {
                        $job['config_schedule'] = "";
                    }
                }
                if (!isset($job['status'])) {
                    $disabledCrons = $this->getDisableCrons();
                    $job['status'] = in_array($code, $disabledCrons) ? self::STATUS_DISABLED : self::STATUS_ENABLED;
                }
                $data[$code] = $job;
            }
        }
        return $data;
    }

    /**
     * Explodes a string and trims all values for whitespace in the ends.
     * @param bool $removeEmptyValues
     * @return array
     */
    public function getDisableCrons($removeEmptyValues = true)
    {
        $deliminator = ',';
        $string = $this->scopeConfig->getValue(
            self::XML_PATH_DISABLED_CRONS
        );
        $explodedValues = explode($deliminator, (string) $string);
        $result = array_map('trim', $explodedValues);
        if ($removeEmptyValues) {
            $temp = [];
            foreach ($result as $value) {
                if ($value !== '') {
                    $temp[] = $value;
                }
            }
            $result = $temp;
        }
        return $result;
    }
}
