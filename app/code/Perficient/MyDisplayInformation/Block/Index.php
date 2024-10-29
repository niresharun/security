<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */

declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Block;

use Magento\Customer\Model\Session;
use Magento\Directory\Block\Data;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template\Context;
use Perficient\MyDisplayInformation\Model\ResourceModel\MyDisplayInformation\CollectionFactory;

/**
 * Class Index
 * @package Perficient\MyDisplayInformation\Block
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * Index constructor.
     */
    public function __construct(
        protected Context $context,
        private readonly Data $directoryBlock,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly FilterBuilder $filterBuilder,
        private readonly Session $customerSession,
        protected CollectionFactory $myDisplayInformation,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCountries()
    {
        $country = $this->directoryBlock->getCountryHtmlSelect();
        return $country;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        $region = $this->directoryBlock->getRegionSelect();
        return $region;
    }

    /**
     * @return string
     */
    public function getCountryAction()
    {
        return $this->getUrl('mydisplayinformation/index/country', ['_secure' => true]);
    }

    /**
     * @return array
     */
    public function getCustomerMyDisplayInformationData()
    {
        $collection = $this->myDisplayInformation->create()
            ->addFieldToSelect ('*')
            ->addFieldToFilter('user_id',$this->customerSession->getCustomer()->getId())->getFirstItem();

        $result = is_countable($collection->getData()) ? count($collection->getData()) : 0;
        if ($result > 0) {
            return $collection->getData();
        } else {
            $newRecord = [];
            $newRecord['mydisplayinformation_id'] = '';
            $newRecord['company_name'] = '';
            $newRecord['contact_name'] = '';
            $newRecord['phone_number'] = '';
            $newRecord['street_line_1'] = '';
            $newRecord['street_line_2'] = '';
            $newRecord['city'] = '';
            $newRecord['state'] = '';
            $newRecord['zip'] = '';
            $newRecord['country'] = '';
            $newRecord['mobile_number'] = '';
            $newRecord['fax_number'] = '';
            $newRecord['email_address'] = '';
            $newRecord['welcome_message_on_the_homepage'] = '';
            $newRecord['user_id'] = $this->customerSession->getCustomer()->getId();
            return $newRecord;
        }
    }

}
