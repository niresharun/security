<?php
/**
 * Observer to remove customize button
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_QuickShip
 */

namespace Perficient\QuickShip\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Perficient\QuickShip\Helper\Data;
use Perficient\Company\Helper\Data as CompanyHelper;
use Magento\Framework\View\LayoutFactory;

/**
 * Class RemoveBlock
 * @package Perficient\QuickShip\Observer
 */
class RemoveBlock implements ObserverInterface
{
    /**
     * Constant for module catalogsearch
     */
    const MODULE_CATALOG_SEARCH = 'catalogsearch';

    /**
     * Constant for controller result
     */
    const CONTROLLER_RESULT = 'result';

    /**
     * Constant for quick ship search field.
     */
    const QUICK_SHIP_PARAM = 'is_quick_ship';


    /**
     * RemoveBlock constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        protected Data $helper,
        protected RequestInterface $request,
        protected CompanyHelper $companyHelper,
        protected LayoutFactory $layoutFactory
    ) {
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\Template $block */
        $layout = $this->layoutFactory->create();
        $block = $layout->getBlock('productimize.view.customize.button');
        if ($block) {
            if($this->helper->isFromQuickShip()) {
                $layout->unsetElement('productimize.view.customize.button');
            }
        }

        $titleBlock = $layout->getBlock('page.main.title');
        if($titleBlock) {
            if(!empty($this->request->getParam('q')) &&
                $this->request->getParam('q') == self::QUICK_SHIP_PARAM &&
                $this->request->getModuleName() == self::MODULE_CATALOG_SEARCH &&
                $this->request->getControllerName() == self::CONTROLLER_RESULT
            ){
                $layout->unsetElement('page.main.title');
            }
        }
        // remove manage customer navigation link if b2c customer
        $manageCustomerBlock = $layout->getBlock('customer-account-navigation-company-users-link');
        $isB2cCustomer = $this->companyHelper->isB2cCustomer();
        if($manageCustomerBlock && $isB2cCustomer) {
            $layout->unsetElement('customer-account-navigation-company-users-link');
        }
    }
}
