<?php
/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_RequisitionList
 */
declare(strict_types=1);

namespace Perficient\RequisitionList\Plugin;

use Perficient\RequisitionList\Block\Requisition\View\Item;

/**
 * Class LinkTitle
 * @package Perficient\RequisitionList\Plugin
 */
class LinkTitle
{
    const TITLE_REQUISITION_LIST = "My Market Scans";

    /**
     * LinkTitle constructor.
     */
    public function __construct(
        private readonly Item $requisitionListHelper
    ) {
    }

    /**
     * @param $result
     * @return mixed
     */
    public function afterGetLinks(\Magento\Customer\Block\Account\Navigation $subject, $result)
    {
        $currentUserRole = $this->requisitionListHelper->getCurrentUserRole();
        if (isset($currentUserRole[0])) {
            $currentUserRoleText = html_entity_decode((string) $currentUserRole[0], ENT_QUOTES);
        }
        foreach ($result as $key => $item) {
            $currentLinkTitle = $item->getLabel();
            if (isset($currentUserRoleText) && $currentUserRoleText == \Perficient\Company\Helper\Data::CUSTOMER_CUSTOMER &&
                $currentLinkTitle == self::TITLE_REQUISITION_LIST) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}