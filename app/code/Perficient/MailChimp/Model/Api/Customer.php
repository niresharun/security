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

use Ebizmarts\MailChimp\Model\Api\Customer as ParentCustomer;
use Magento\Directory\Model\CountryFactory;

class Customer extends ParentCustomer
{
    public function sendCustomers($storeId)
    {
        $mailchimpStoreId = $this->_helper->getConfigValue(
            \Ebizmarts\MailChimp\Helper\Data::XML_MAILCHIMP_STORE,
            $storeId
        );
        $listId = $this->_helper->getConfigValue(\Ebizmarts\MailChimp\Helper\Data::XML_PATH_LIST, $storeId);
        $collection = $this->_collection->create();
        $collection->addFieldToFilter('store_id', ['eq' => $storeId]);
        $collection->getSelect()->joinLeft(
            ['m4m' => $this->_helper->getTableName('mailchimp_sync_ecommerce')],
            "m4m.related_id = e.entity_id and m4m.type = '" . \Ebizmarts\MailChimp\Helper\Data::IS_CUSTOMER .
            "' and m4m.mailchimp_store_id = '" . $mailchimpStoreId . "'",
            ['m4m.*']
        );
        $collection->getSelect()->where("m4m.mailchimp_sync_delta IS null " .
            "OR m4m.mailchimp_sync_modified = 1");
        $collection->getSelect()->limit(self::MAX);
        $counter = 0;
        $customerArray = [];
        $this->_helper->resetMapFields();
        foreach ($collection as $item) {
            $customer = $this->_customerFactory->create();
            $customer->getResource()->load($customer, $item->getId());
            $data = $this->_buildCustomerData($customer);
            $customerJson = '';
            $customerJson = json_encode($data);
            if ($customerJson !== false) {
                if (!empty($customerJson)) {
                    if ($item->getMailchimpSyncModified() == 1) {
                        $this->_helper->modifyCounter(\Ebizmarts\MailChimp\Helper\Data::CUS_MOD);
                    } else {
                        $this->_helper->modifyCounter(\Ebizmarts\MailChimp\Helper\Data::CUS_NEW);
                    }
                    $customerMailchimpId = hash('md5', strtolower((string)$customer->getEmail()));
                    $customerArray[$counter]['method'] = "PUT";
                    $customerArray[$counter]['path'] = "/ecommerce/stores/" . $mailchimpStoreId . "/customers/" .
                        $customerMailchimpId;
                    $customerArray[$counter]['operation_id'] = $this->_batchId . '_' . $customer->getId();
                    $customerArray[$counter]['body'] = $customerJson;
                    $counter++;
                    if (!$this->isSubscriber($customer)) {
                        $subscriberData = $this->buildSubscriberData($customer);
                        $subscriberJson = json_encode($subscriberData);
                        if ($subscriberJson !== false) {
                            $customerArray[$counter]['method'] = "PATCH";
                            $customerArray[$counter]['path'] = "/lists/" . $listId . "/members/" .
                                $customerMailchimpId;
                            $customerArray[$counter]['operation_id'] = $this->_batchId . '_' .
                                $customer->getId() . '_SUB';
                            $customerArray[$counter]['body'] = $subscriberJson;
                            $counter++;
                        }
                    }
//update customers delta
                    $this->_updateCustomer($mailchimpStoreId, $customer->getId());
                } else {
                    $this->_updateCustomer(
                        $mailchimpStoreId,
                        $customer->getId(),
                        $this->_helper->getGmtDate(),
                        'Customer with no data',
                        0
                    );
                }
            } else {
                $this->_updateCustomer(
                    $mailchimpStoreId,
                    $customer->getId(),
                    $this->_helper->getGmtDate(),
                    json_last_error_msg(),
                    0
                );
            }
        }
        return $customerArray;
    }

}
