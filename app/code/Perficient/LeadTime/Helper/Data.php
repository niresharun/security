<?php
/**
 * This file is used to create CMS blocks for Lead Time Notifications.
 *
 * @category: Magento
 * @package: Perficient/LeadTime
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_LeadTime LeadTime CMS Block
 */
declare(strict_types=1);

namespace Perficient\LeadTime\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Cms\Block\Block as CmsBlock;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class Data
 * @package Perficient\LeadTime\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Lead Time CMS Block Constants.
     */
    final const CMS_BLOCK_LEAD_TIME_STANDARD = 'standard_lead_time';
    final const CMS_BLOCK_LEAD_TIME_QUICKSHIP = 'quick_ship_lead_time';

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Json $json
     */
    public function __construct(
        Context                          $context,
        private readonly Json            $json,
        private readonly CmsBlock        $cmsBlock,
        private readonly CheckoutSession $checkoutSession
    )
    {
        parent::__construct($context);
    }

    /**
     * Method used to get standard lead time message.
     *
     * @return mixed
     */
    public function getStandardLeadTimeMessage()
    {
        return $this->getBlockContent(self::CMS_BLOCK_LEAD_TIME_STANDARD);
    }

    /**
     * Method used to get standard lead time message.
     *
     * @return mixed
     */
    public function getQuickShipLeadTimeMessage()
    {
        return $this->getBlockContent(self::CMS_BLOCK_LEAD_TIME_QUICKSHIP);
    }

    /**
     * Method used to get the block content.
     *
     * @param $blockIdentifier
     * @return mixed
     */
    private function getBlockContent($blockIdentifier)
    {
        return $this->cmsBlock->setBlockId($blockIdentifier)->toHtml();
    }

    /**
     * Method used to get the lead time information from info-buy-request object.
     *
     * @param $infoBuyRequest
     *
     * @return string
     */
    public function getLeadTimeFromInfoBuyRequest($infoBuyRequest)
    {
        $infoRequest = $this->json->unserialize($infoBuyRequest->getValue());

        $leadTimeMessage = '';
        if (isset($infoRequest['lead_time'])) {
            $leadTimeMessage = $infoRequest['lead_time'];
        }

        return strip_tags((string)$leadTimeMessage);
    }

    /**
     * Method used to get the lead time information from options.
     *
     * @param $options
     * @return string
     */
    public function getLeadTime($options)
    {
        $leadTime = '';
        foreach ($options as $option) {
            // Select buy request options
            if ($option->getCode() == 'info_buyRequest') {
                $unserializedInfoBuyRequest = $this->json->unserialize($option->getValue());
                if (isset($unserializedInfoBuyRequest['lead_time'])) {
                    $leadTime = $unserializedInfoBuyRequest['lead_time'];
                }
                break;
            }
        }

        return strip_tags((string)$leadTime);
    }

    /**
     * get lead time from quote
     *
     * @return string
     * @throws \Exception
     */
    public function getLeadTimeFromQuote()
    {
        $quote = $this->checkoutSession->getQuote();
        if($quote->getLeadTime()) {
            $leadTimeMessageArray = explode("#html-body", $quote->getLeadTime());
            if ($leadTimeMessageArray && isset($leadTimeMessageArray['0'])) {
                $leadTime = $leadTimeMessageArray['0'];

                if (!empty($leadTime)) {
                    return strip_tags((string)$leadTime);
                }
            }
        }

        return null;
    }
}
