<?php
/**
 * Display logic to represent Timeline
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Block\Adminhtml\Task\Timeline;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class Actions
 * @package Perficient\CronScheduler\Block\Adminhtml\Task\Timeline
 */
class Actions extends Template
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization = null;

    /**
     * @var string
     */
    protected $aclResource = "generate_schedule";

    /**
     * Class constructor
     * @param Context $context
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->authorization = $context->getAuthorization();
        $this->setTemplate('task/timeline/actions.phtml');
    }

    /**
     * Is allowed to generate schedule
     * @return boolean
     */
    public function isAllowed()
    {
        return $this->authorization->isAllowed('Perficient_CronScheduler::' . $this->aclResource);
    }

    /**
     * Get the url to generate schedule
     * @return string the url
     */
    public function getGenerateScheduleUrl()
    {
        return $this->getUrl("*/job/generateSchedule", ["redirect" => "cronscheduler_task_timeline"]);
    }
}
