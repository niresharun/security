/**
 * PriceMultiplier module for multiplier price .
 *
 * @category:  JS
 * @package:   Perficient/PriceMultiplier
 * @copyright:
 * See COPYING.txt for license details.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords:  Module Perficient_PriceMultiplier
 */
define(
    [
        'uiComponent',
        'jquery',
        'ko',
        'Perficient_PriceMultiplier/js/utility',
        'mage/url'
    ],
    function (
        Component,
        $,
        ko,
        storeCatalogUtility,
        urlBuilder
    ) {
        var main = {
            spanTriggerProductPrice: 'span.trigger-product-price',
            fetchPrice: function () {
              	if($('.prices').length && $('.prices').attr('data-product-type')=='configurable') {
            		$('.prices').hide();
            		return false;
            	}
                var $priceAreasToPopulate = $(this.spanTriggerProductPrice);
                var pricedProductSkus = $.map(
                    $priceAreasToPopulate,
                    function (priceAreaToPopulate) {
                        return $(priceAreaToPopulate).attr('data-price-id');
                    }
                );
                storeCatalogUtility.fetchPrice(pricedProductSkus);
            },
            fetchWidgetPrice: function () {
                var productIdsNew = [];
                var $widgetBlock = $('.block.widget,.block-wishlist').not('.price-processed');
                var $productEle = $widgetBlock.find(this.spanTriggerProductPrice);
                $productEle.each(
                    function () {
                        productIdsNew.push($(this).attr('data-price-id'));
                    }
                );
                $widgetBlock.addClass('price-processed');
                /**
                 * @type {string}
                 */
                storeCatalogUtility.fetchPrice(productIdsNew);
            }
        }
        
        jQuery(function ($) {
            main.fetchPrice();
        });

        return main;
    }
);

