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

namespace Perficient\RequisitionList\Plugin\Action;

use Magento\Framework\Controller\Result\Redirect;
use Magento\RequisitionList\Model\Action\RequestValidator as ParentRequestValidator;
use Magento\Store\Model\StoreManagerInterface;
use Perficient\RequisitionList\Block\Requisition\View\Item;

/**
 * Class RequestValidator
 * @package Perficient\RequisitionList\Plugin\Action
 */
class RequestValidator
{
    /**
     * RequestValidator constructor.
     * @param StoreManagerInterface $storemanager
     * @param Redirect $resultRedirect
     */
    public function __construct(
        private readonly ParentRequestValidator $requestValidator,
        private readonly Item $requisitionListHelper,
        private readonly StoreManagerInterface $storemanager,
        private readonly Redirect $resultRedirect
    ) {
    }

    /**
     * @param $result
     * @return Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetResult(ParentRequestValidator $subject, $result)
    {
        $currentUserRole = $this->requisitionListHelper->getCurrentUserRole();
        if (isset($currentUserRole[0]) && $currentUserRole[0] == \Perficient\Company\Helper\Data::CUSTOMER_CUSTOMER) {
            $redirectUrl = $this->storemanager->getStore()->getBaseUrl();
            $this->resultRedirect->setPath($redirectUrl);
            return $this->resultRedirect;
        }
    }
}
