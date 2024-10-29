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

namespace Perficient\MyDisplayInformation\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Perficient\MyDisplayInformation\Controller\AbstractAction;
use Magento\Directory\Model\Country as CoreCountry;

/**
 * Class Country
 * @package Perficient\MyDisplayInformation\Controller\Index
 */
class Country extends AbstractAction
{
    /**
     * Country constructor.
     */
    public function __construct(
        private readonly Context $context,
        protected PageFactory $resultPageFactory,
        protected Session $customerSession,
        protected UrlInterface $url,
        private readonly JsonFactory $resultJsonFactory,
        private readonly RegionFactory $regionColFactory,
        private readonly RequestInterface $request,
        private readonly CoreCountry $core_country

    )
    {
        parent::__construct($resultPageFactory, $customerSession, $url);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Custom Front View'));
        $result = $this->resultJsonFactory->create();
        $regionCollection = $this->core_country->loadByCode($this->request->getParam('country'))->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray();
        $html = '';
        if (count($regions) > 0) {
            $html .= '<option selected="selected" value="">Please select a region, state or province.</option>';
            foreach ($regionCollection as $state) {
                $html .= '<option  value="' . $state->getName() . '">' . $state->getName() . '</option>';
            }
        }
        return $result->setData(['success' => true, 'value' => $html]);
    }
}
