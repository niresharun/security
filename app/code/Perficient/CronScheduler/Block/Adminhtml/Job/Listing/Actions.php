<?php
/**
 * maintains acl and provide respective url for the schedule
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */

namespace Perficient\CronScheduler\Block\Adminhtml\Job\Listing;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class Actions
 * @package Perficient\CronScheduler\Block\Adminhtml\Job\Listing
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
        $this->authorization = $context->getAuthorization();
        $this->setTemplate('job/listing/actions.phtml');
        parent::__construct($context, $data);
    }

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
        return $this->getUrl("*/job/generateSchedule", ["redirect" => "cronscheduler_job_listing"]);
    }
}
