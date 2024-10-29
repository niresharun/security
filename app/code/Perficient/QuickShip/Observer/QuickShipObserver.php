<?php
/**
 * Inventory for Quick Ship
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);
namespace Perficient\QuickShip\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\SessionFactory;
use Psr\Log\LoggerInterface;
use Perficient\MyCatalog\Helper\Data as MyCatalogHelper ;


/**
 * Observer to set Quick Ship flag
 */
class QuickShipObserver implements ObserverInterface
{
    
    const QUERY_PARAM     = 'q';
    /**
     * @const string
     */
    const QUICK_SHIP_FIELD = 'is_quick_ship';
    /**
     * @const string
     */
    const MODULE_NAME_CATALOG_SEARCH = 'catalogsearch';
    /**
     * @const string
     */
    const CONTROLLER_NAME_CATALOG_SEARCH_ADVANCE = 'advanced';
    /**
     * @const string
     */
    const CONTROLLER_NAME_CATALOG_PRODUCT_VIEW = 'product';

    /**
     * @const string
     */
    const ACTION_NAME_CATALOG_SEARCH_ADVANCE = 'result';
    /**
     * @const string
     */
    const ACTION_NAME_CATALOG_PRODUCT_VIEW = 'view';

    /**
     * Constant for quick ship search field.
     */
    const QUICK_SHIP_PARAM = 'is_quick_ship';

    /**
     * QuickShipObserver constructor.
     * @param SessionFactory $catalogSession
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param Http $httpRequest
     */
    public function __construct(
        protected SessionFactory $catalogSession,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
        protected Http $httpRequest,
        protected MyCatalogHelper $myCatalogHelper,
        array $data = []
    ) {
    }

    /**
     * Observer Execute function
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Http $request */
        $controller = $this->request->getControllerName();
        $action = $this->request->getActionName();
        $module = $this->request->getModuleName();

        if($this->request->isXmlHttpRequest() || empty($controller) || empty($action)) {
            return;
        }
        $currentParams =  $this->httpRequest->getParams();
       if(!empty($currentParams)
           && is_array($currentParams)
           && array_key_exists('proceed',$currentParams)){
           $this->myCatalogHelper->validateCustomer();
       }
        $catalogSession = $this->catalogSession->create();
        $searchTerm = $this->request->getParam(self::QUERY_PARAM, '');

        if ($module == self::MODULE_NAME_CATALOG_SEARCH
            && $controller == self::CONTROLLER_NAME_CATALOG_SEARCH_ADVANCE
            && $action == self::ACTION_NAME_CATALOG_SEARCH_ADVANCE
            && $this->request->getParam(self::QUICK_SHIP_FIELD) == 1) {
            $catalogSession->setData('from_quick_ship', 1);
            return;
        } elseif ($module == self::MODULE_NAME_CATALOG_SEARCH
            && $controller == self::ACTION_NAME_CATALOG_SEARCH_ADVANCE
            && $searchTerm == self::QUICK_SHIP_PARAM) {
            $catalogSession->setData('from_quick_ship', 1);
            return;
        } else if($controller == self::CONTROLLER_NAME_CATALOG_PRODUCT_VIEW
            && $action == self::ACTION_NAME_CATALOG_PRODUCT_VIEW
            && $catalogSession->getFromQuickShip() == 1) {
            return;
        } else if($module == "page_cache" && $controller == "block" && $catalogSession->getFromQuickShip() == 1) {
            return;
        } else if($module == "productimize" && $controller == "index" && $action == "ImgRender" && $catalogSession->getFromQuickShip() == 1) {
            return;
        } else {
            $catalogSession->setFromQuickShip(null);
            return;
        }
    }
}
