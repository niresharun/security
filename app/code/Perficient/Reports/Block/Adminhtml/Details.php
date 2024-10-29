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

namespace Perficient\Reports\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\User\Model\UserFactory;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent;

/**
 * Report Block
 */
class Details extends Container
{
    /**
     * Store current event
     *
     * @var PerficientLoggingEvent
     */
    protected $_currentChangeLog = null;

    /**
     * Store current event user
     *
     * @var \Magento\User\Model\User
     */
    protected $_eventUser = null;

    /**
     * Serializer Instance
     *
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param Registry $_coreRegistry
     * @param UserFactory $_userFactory
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        protected Registry $_coreRegistry,
        protected UserFactory $_userFactory,
        array $data = [],
        Json $json = null
    ) {
        parent::__construct($context, $data);
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Add back button
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->buttonList->add(
            'back',
            [
                'label' => __('Back'),
                'onclick' => "setLocation('" . $this->_urlBuilder->getUrl('customreports/*/changereport') . "')",
                'class' => 'back'
            ]
        );
    }

    /**
     * Header text getter
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->getCurrentEvent()) {
            return __('Change Log Entry #%1', $this->getCurrentEvent()->getId());
        }
        return __('Change Log Entry Details');
    }

    /**
     * Get current event
     *
     * @return PerficientLoggingEvent|null
     */
    public function getCurrentEvent()
    {
        if (null === $this->_currentChangeLog) {
            $this->_currentChangeLog = $this->_coreRegistry->registry('current_change');
        }
        return $this->_currentChangeLog;
    }

    /**
     * Convert x_forwarded_ip to string
     */
    public function getEventXForwardedIp(): string|bool
    {
        if ($this->getCurrentEvent()) {
            $xForwarderFor = long2ip((int)$this->getCurrentEvent()->getXForwardedIp());
            if ($xForwarderFor && $xForwarderFor != '0.0.0.0') {
                return $xForwarderFor;
            }
        }
        return false;
    }

    /**
     * Convert ip to string
     */
    public function getEventIp(): string|bool
    {
        if ($this->getCurrentEvent()) {
            return long2ip((int)$this->getCurrentEvent()->getIp());
        }
        return false;
    }

    /**
     * Get current event user
     *
     * @return \Magento\User\Model\User|null
     */
    public function getEventUser()
    {
        if (null === $this->_eventUser) {
            $this->_eventUser = $this->_userFactory->create()->load($this->getUserId());
        }
        return $this->_eventUser;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        if ($this->getCurrentEvent()) {
            if(str_contains( (string) $this->getCurrentEvent()->getFullaction(), 'frontend')) {
                return 'frontend';
            }
        }
        return 'adminhtml';
    }
}
