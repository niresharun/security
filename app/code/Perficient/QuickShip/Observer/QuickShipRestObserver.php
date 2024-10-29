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
use Magento\Framework\Session\SessionManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Observer to set Quick Ship flag
 */
class QuickShipRestObserver implements ObserverInterface
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
     * @param SessionManagerInterface $coreSession
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly SessionManagerInterface $coreSession,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
        array $data = []
    ) {
    }

    /**
     * Observer Execute function
     */
    public function execute(Observer $observer)
    {
        /** @var Http $request */
        $controller = $this->request->getControllerName();
        $action = $this->request->getActionName();
        $module = $this->request->getModuleName();

        $this->logger->debug('Call to QuickShipRestObserver - Controller: ' . $controller . ' Action:' . $action);

        if($this->request->isXmlHttpRequest() || empty($controller) || empty($action)) {
            return;
        }

        $searchTerm = $this->request->getParam(self::QUERY_PARAM, '');
        $isQuickShip = $this->request->getParam(self::QUICK_SHIP_FIELD);
        if ($module == self::MODULE_NAME_CATALOG_SEARCH
            && $controller == self::ACTION_NAME_CATALOG_SEARCH_ADVANCE
            && ($searchTerm == self::QUICK_SHIP_PARAM || $isQuickShip))
        {
            return;
        } else if($controller == self::CONTROLLER_NAME_CATALOG_PRODUCT_VIEW
            && $action == self::ACTION_NAME_CATALOG_PRODUCT_VIEW
            && $this->coreSession->getFromQuickShip() == 1) {
            return;
        }

        $this->logger->debug('Reset to QuickShipRestObserver - Controller: ' . $controller . ' Action:' . $action);
        $this->coreSession->setFromQuickShip(null);
    }
}
