<?php
/**
 * Block to get customer price multiplier value
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: price multiplier custom customer attribute values in session
 */
declare(strict_types=1);
namespace Perficient\PriceMultiplier\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Perficient\PriceMultiplier\Model\ProductPrice;


/**
 * Class FinalPriceBox
 * @package Perficient\PriceMultiplier\Block
 */
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     * FinalPriceBox constructor.
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param SalableResolverInterface $salableResolver
     * @param MinimalPriceCalculatorInterface $minimalPriceCalculator
     * @param Session $customerSession
     */
    public function __construct(
        Context                         $context,
        SaleableInterface               $saleableItem,
        PriceInterface                  $price,
        RendererPool                    $rendererPool,
        SalableResolverInterface        $salableResolver,
        MinimalPriceCalculatorInterface $minimalPriceCalculator,
        private readonly Session        $customerSession,
        protected ProductPrice          $productPrice,
        protected HttpContext           $httpContext,
        array                           $data = []
    ) {
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data, $salableResolver, $minimalPriceCalculator);
    }

    public function isCustomerLoggedIn(): bool
    {
        //return $this->customerSession->isLoggedIn();
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getPLPPrice($id) 
    {
        return $this->productPrice->getItemPrice([$id]);
    }
}
