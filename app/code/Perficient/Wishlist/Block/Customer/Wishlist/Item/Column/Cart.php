<?php
/**
 * overide for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani<Manish.Bhojwani@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Block\Customer\Wishlist\Item\Column;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\ConfigInterface;
use Perficient\Productimize\Model\ProductConfiguredPrice;

/**
 * Wishlist block customer item cart column
 *
 * @api
 * @since 100.0.2
 */
class Cart extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column\Cart
{
    /**
     * @var View
     */
    private $productView;

    /**
     * @param ConfigInterface|null $config
     * @param UrlBuilder|null $urlBuilder
     * @param View|null $productView
     * @param ProductConfiguredPrice $productConfiguredPrice
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context  $context,
        \Magento\Framework\App\Http\Context     $httpContext,
        private readonly ProductConfiguredPrice $productConfiguredPrice,
        array                                   $data = [],
        ?ConfigInterface                        $config = null,
        ?UrlBuilder                             $urlBuilder = null,
        ?View                                   $productView = null
    )
    {
        $this->productView = $productView ?: ObjectManager::getInstance()->get(View::class);
        parent::__construct($context, $httpContext, $data, $config, $urlBuilder, $productView);
    }

    /**
     * Return configurator product price
     *
     * @param $price
     *
     * @return array
     */
    public function getConfigratorItemPrice($price)
    {
        return $this->productConfiguredPrice->getConfigratorItemPrice($price);
    }
}
