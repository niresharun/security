<?php
/**
 * This module is used to add mailchimp configurations
 *
 * @category: Magento
 * @package: Perficient/MailChimp
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MailChimp
 */

namespace Perficient\MailChimp\Model\Api;

use Ebizmarts\MailChimp\Model\Api\Subscriber as ParentSubscriber;

/**
 * Class Subscriber
 * @package Perficient\MailChimp\Model\Api
 */
class Subscriber extends ParentSubscriber
{
    public function sendSubscribers($storeId, $listId)
    {

//get subscribers
//        $listId = $this->_helper->getGeneralList($storeId);
        $this->_interest = $this->_helper->getInterest($storeId);
        $collection = $this->_subscriberCollection->create();
        $collection->addFieldToFilter('subscriber_status', ['eq' => 1])
            ->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->getSelect()->joinLeft(
            ['m4m' => $this->_helper->getTableName('mailchimp_sync_ecommerce')],
            "m4m.related_id = main_table.subscriber_id and m4m.type = '" .
            \Ebizmarts\MailChimp\Helper\Data::IS_SUBSCRIBER .
            "' and m4m.mailchimp_store_id = '" . $listId . "'",
            ['m4m.*']
        );
        $collection->getSelect()->where("m4m.mailchimp_sync_delta IS null " .
            "OR m4m.mailchimp_sync_modified = 1");
        $collection->getSelect()->limit(self::BATCH_LIMIT);
        $subscriberArray = [];
        $date = $this->_helper->getDateMicrotime();
        $batchId = \Ebizmarts\MailChimp\Helper\Data::IS_SUBSCRIBER . '_' . $date;
        $counter = 0;
        /**
         * @var $subscriber \Magento\Newsletter\Model\Subscriber
         */
        foreach ($collection as $subscriber) {
            $data = $this->_buildSubscriberData($subscriber);
            $md5HashEmail = hash('md5', strtolower((string)$subscriber->getSubscriberEmail()));
            $subscriberJson = "";
            //enconde to JSON
            $subscriberJson = json_encode($data);
            if ($subscriberJson !== false) {
                if (!empty($subscriberJson)) {
                    if ($subscriber->getMailchimpSyncModified() == 1) {
                        $this->_helper->modifyCounter(\Ebizmarts\MailChimp\Helper\Data::SUB_MOD);
                    } else {
                        $this->_helper->modifyCounter(\Ebizmarts\MailChimp\Helper\Data::SUB_NEW);
                    }
                    $subscriberArray[$counter]['method'] = "PUT";
                    $subscriberArray[$counter]['path'] = "/lists/" . $listId . "/members/" . $md5HashEmail;
                    $subscriberArray[$counter]['operation_id'] = $batchId . '_' . $subscriber->getSubscriberId();
                    $subscriberArray[$counter]['body'] = $subscriberJson;
//update subscribers delta
                    $this->_updateSubscriber($listId, $subscriber->getId());
                }
                $counter++;
            } else {
                $errorMessage = json_last_error_msg();
                $this->_updateSubscriber(
                    $listId,
                    $subscriber->getId(),
                    $this->_helper->getGmtDate(),
                    $errorMessage,
                    0
                );
            }
        }
        return $subscriberArray;
    }
}
