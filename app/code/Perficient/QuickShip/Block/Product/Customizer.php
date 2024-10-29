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
namespace Perficient\QuickShip\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Header;

/**
 * Block hiding customizer button
 */
class Customizer extends Template
{
    /**
     * @const string
     */
    const QUICK_SHIP_FIELD = 'is_quick_ship';
    /**
     * @const string
     */
    const PRODUCT_CUSTOMIZER_FIELD = 'product_customizer';

    /**
     * Customizer constructor.
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     * @param Header $header
     */
    public function __construct(
        Context $context,
        protected Registry $registry,
        private readonly RequestInterface $request,
        private readonly Header $header,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function fromQuickSearch()
    {
        $query_params = [];
        $product = $this->registry->registry('product');
        if (!$product) {
            return false;
        }

        $isCustomizeAvailable = $product->getData(self::PRODUCT_CUSTOMIZER_FIELD);
        $isQuickShip = $product->getData(self::QUICK_SHIP_FIELD);

        $httpReferer = $this->header->getHttpReferer();
        $url = \Laminas\Uri\UriFactory::factory($httpReferer);
        $query_str = $url->getQuery();

        if($query_str) {
            $query_params = $url->getQueryAsArray();
        }

        $isQuickShipProduct = $this->request->getParam('quick_ship_product');
        $fromQuickShip = 0;
        if(isset($isQuickShip) && $isQuickShip) {
            $fromQuickShip = 1;
        }

        if(!$isCustomizeAvailable || !$isQuickShip || !$fromQuickShip) {
            return false;
        }

        return true;
    }
}
