<?php
/**
 * This module is used to send request to Rabbitmq and display returning results
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright  - 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Swapnil Kene <swapnil.kene@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model\Cron\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Perficient\Rabbitmq\Model\Cron\Source\Frequency;

/**
 * Class Cron
 * @package Perficient\Bloomreach\Model\Cron\Backend
 */
class SurchargeQtyCron extends Value
{
    const CRON_STRING_PATH           = 'crontab/default/jobs/surcharge_qty_update/schedule/cron_expr';
    const CRON_MODEL_PATH            = 'crontab/default/jobs/surcharge_qty_update/run/model';
    const XML_PATH_CRON_ENABLED      = 'groups/cron_settings/fields/enabled/value';
    const XML_PATH_CRON_TIME         = 'groups/cron_settings/fields/time/value';
    const XML_PATH_CRON_WEEKDAY      = 'groups/cron_settings/fields/week_day/value';
    const XML_PATH_CRON_DAY_OF_MONTH = 'groups/cron_settings/fields/month_day/value';
    const XML_PATH_CRON_ENDTIME      = 'groups/cron_settings/fields/endtime/value';
    const XML_PATH_CRON_FREQUENCY    = 'groups/cron_settings/fields/frequency/value';

    private string $runModelPath = '';

    /**
     * Cron constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param string $prefix
     * @param string $suffix
     * @param string $type
     * @param string $cronGroup
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ResourceConfig $resourceConfig,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        private $prefix = '',
        private $suffix = '',
        private $type = '',
        private $cronGroup = '',
        array $data = []
    ) {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->type = $type;
        $this->cronGroup = $cronGroup;
        $this->resourceConfig = $resourceConfig;

        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Cron settings after save.
     *
     * @return Value
     * @throws LocalizedException
     */
    public function afterSave()
    {
        try {
            $cronExprString = $this->getCronExprString();

            $this->resourceConfig->saveConfig(
                self::CRON_STRING_PATH,
                $cronExprString
            );

            $this->resourceConfig->saveConfig(
                self::CRON_MODEL_PATH,
                $this->runModelPath
            );
        } catch (\Exception $e) {
            throw new LocalizedException(__("We can't save the Cron expression." . $e->getMessage()));
        }
        return parent::afterSave();
    }

    /**
     * Minute expression
     *
     * @param $time
     * @param null $endTime
     * @param null $minuteExp
     */
    public function minuteExpression($time, $endTime = null, $minuteExp = null): int|string
    {
        if ($minuteExp === null) {
            $minuteExpression = '*';
        } else {
            $minuteExpression = (int)$minuteExp;
        }

        if (!empty($time) && !empty($endTime)) {
            if ($time[0] == $endTime[0]) {
                $startTimeMinute =  (int)$time[1];
                $endTimeMinute =  (int)$endTime[1];
                if ($startTimeMinute == $endTimeMinute || $startTimeMinute > $endTimeMinute || $endTimeMinute == '0') {
                    $minuteExpression = $this->minuteCal($startTimeMinute, '60', $minuteExpression);
                } else {
                    $minuteExpression = $this->minuteCal($startTimeMinute, $endTimeMinute, $minuteExpression);
                }
            } else {
                if ($minuteExpression != '*') {
                    $minuteExpression = '*/'.$minuteExpression;
                }
            }
        } else {
            if ($minuteExpression != '*') {
                $minuteExpression = '*/'.$minuteExpression;
            }
        }
        return $minuteExpression;
    }

    /**
     * Calculate minute expression based on start and end minutes
     *
     * @param $str
     * @param null $end
     * @param $exp
     * @return string
     */
    public function minuteCal($str, $end, $exp)
    {
        if ($exp != '*') {
            $minDifference = $end - $str;
            if ($end == '60') {
                $end = '59';
            }
            if ($exp <= $minDifference) {
                $expression = $str."-".$end."/".$exp;
            } else {
                $minDifference = '60' - $str;
                if ($exp <= $minDifference) {
                    $expression = $str."-59"."/".$exp;
                } else {
                    $expression = $str."-59"."/1";
                }
            }
        } else {
            $expression = $str."-".$end."/".$exp;
        }
        return $expression;
    }

    /**
     * Hour expression
     *
     * @param $time
     * @param $endTime
     */
    public function hourExpression($time, $endTime): int|string
    {
        $hours = 0;
        $endTimeHour = 0;

        // Check start time exists or not
        if (!empty($time)) {
            $startTimeHour = (int)$time[0];
            if (!empty($endTime)) {
                $endTimeH = (int)$endTime[0];
                // Check end time exists or not
                if (!empty($endTime) && $endTimeH != '0') {
                    $endTimeHour = $endTimeH;
                } elseif ($endTimeH == $startTimeHour) {
                    $endTimeHour = $startTimeHour;
                } else {
                    $endTimeHour = '23';
                }
            }

            $hours = $startTimeHour . "-" . $endTimeHour;
            // If start and end hour both are same then we will consider start hour
            if ($startTimeHour == $endTimeHour || ($startTimeHour > $endTimeHour && $endTimeHour == 0)) {
                $hours = $startTimeHour;
            } elseif ($startTimeHour > 0 && $endTimeHour > 0 && $startTimeHour > $endTimeHour) {
                $hours = $startTimeHour . "-23" . ",0-" . $endTimeHour;
            }
        }

        if (!$hours) {
            $hours = '*';
        }

        return $hours;
    }

    /**
     * Get Cron Expr String
     *
     * @return string
     */
    private function getCronExprString()
    {
        $time       = $this->getData(self::XML_PATH_CRON_TIME);
        $endTime    = $this->getData(self::XML_PATH_CRON_ENDTIME);
        $frequency  = $this->getData(self::XML_PATH_CRON_FREQUENCY);
        $weekDay    = $this->getData(self::XML_PATH_CRON_WEEKDAY);
        $monthDay   = $this->getData(self::XML_PATH_CRON_DAY_OF_MONTH);

        $frequencyWeekly = Frequency::CRON_WEEKLY;
        $frequencyMonthly = Frequency::CRON_MONTHLY;

        $minutes =  (int)$time[1];
        $minuteFrequencies = [
            Frequency::CRON_EACH_1MINUTES,
            Frequency::CRON_EACH_5MINUTES,
            Frequency::CRON_EACH_10MINUTES,
            Frequency::CRON_EACH_15MINUTES,
            Frequency::CRON_EACH_30MINUTES
        ];

        $hourFrequencies = [
            Frequency::CRON_HOURLY,
            Frequency::CRON_EACH_2HOURS,
            Frequency::CRON_EACH_4HOURS,
            Frequency::CRON_EACH_6HOURS,
            Frequency::CRON_EACH_8HOURS,
            Frequency::CRON_EACH_12HOURS
        ];

        if (in_array($frequency, $minuteFrequencies)) {
            $minutes = $this->minuteExpression($time, $endTime, (int)$frequency);
            $hours = $this->hourExpression($time, $endTime);
        } elseif (in_array($frequency, $hourFrequencies)) {
            $hours = $this->hourExpression($time, $endTime);
            $hours = $hours.'/'.(int)$frequency;
        } else {
            $hours = (int)$time[0];
            if (!empty($endTime) && $endTime[0] != '00') {
                if ($endTime[0] != $time[0] && $endTime[0] > $time[0]) {
                    $hours = (int)$time[0] . "-" . (int)$endTime[0];
                }
            }
        }

        $cronExprArray = [
            $minutes,
            $hours,
            $frequency == $frequencyMonthly ? ($monthDay ?: '1') : '*',
            '*',
            $frequency == $frequencyWeekly ? ($weekDay ?: '1') : '*',
        ];
        $cronExprString = join(' ', $cronExprArray);

        return $cronExprString;
    }
}
