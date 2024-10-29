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
        'mage/url',
        'mage/translate',
        'priceBox',        
        'jquery/ui',
    ],
    function (
        Component,
        $,
        ko,
        urlBuilder,
        translate,
        priceBox
    ) {
        var main = {
            /**
             * Get Price Prefix
             *
             * @returns {string}
             */
            getPricePrefix: function () {
                return 'price-wrapper-';
            },

            /**
             * Get Lot Price Prefix
             *
             * @returns {string}
             */
            getStrikeOutPricePrefix: function () {
                return 'strikeout-price-wrapper-';
            },


            /**
             *  Get multiplier price for config - simple products
             * 
             */

            getMultipliedPrice: function (productIds) {

                let prices = [];
                var productId = $('input[name=product]').val();
                productIds.push(productId);

                var fetchUrl = urlBuilder.build('pricemultiplier/product/fetch')
                    , dataToPost = {
                    ids: productIds,
                    time: Date.now()
                };
                $.ajax(
                    {
                        url: fetchUrl,
                        type: 'get',
                        async: false,
                        data: dataToPost,
                        dataType: 'json',
                        context: $('body'),
                    }
                ).done(
                    function (response) {                       
                        if (response) {

                            _.each(response, function (item, key) {
                                if(key) {
                                    var value = item['unformatted_price'];

                                    prices[key] = {
                                        basePrice: {amount: parseFloat(value)},
                                        baseOldPrice: {amount: parseFloat(value)},
                                        oldPrice: {amount: parseFloat(value)},
                                        finalPrice: {amount: parseFloat(value)}
                                    };
                                }
                            });

                            var uPrice = prices[productId].finalPrice.amount;
                            var priceObj = {};
                            priceObj.finalPrice = {amount : uPrice};
                            priceObj.basePrice = {amount : uPrice};
                            priceObj.oldPrice = {amount : uPrice};
                            priceObj.baseOldPrice = {amount : uPrice};

                            var priceBoxes = $('[data-role=priceBox]');
                            priceBoxes.priceBox({'priceConfig':{'prices':priceObj}});

                            var config = $('#product_addtocart_form').data('mage-configurable').option('spConfig');
                            config.optionPrices = prices;                   
                        }
                    }
                ).always(
                    function () {
                        $('.price-loader').hide();
                        if(prices[productId].finalPrice.amount>0) {
                            $('.prices').show();
                        }
                        $('.action.tocart').prop('disabled', false);
                    }
                );
            },

            /**
             * Fetch Price
             *
             * @param   productIds
             * @param   type
             * @returns {boolean}
             */
            fetchPrice: function (productIds) {
                if (typeof productIds == "undefined" || productIds.length === 0) {
                    return false;
                }

                if (typeof window.storeCatalogUrl.baseUrl != 'undefined') {
                    urlBuilder.setBaseUrl(window.storeCatalogUrl.baseUrl);
                } else {
                    if (showAlertFlag == true) {
                        alert(translate('Unable to fetch required product data. Please refresh this page.'));
                        showAlertFlag = false;
                    }
                    return false;
                }

                var fetchUrl = urlBuilder.build('pricemultiplier/product/fetch')
                    , dataToPost = {
                    ids: productIds,
                };
                $.ajax(
                    {
                        url: fetchUrl,
                        type: 'get',
                        data: dataToPost,
                        dataType: 'json',
                        context: $('body'),
                        beforeSend: function () {
                            $('.price-loader').show();
                            $('.prices').hide();
                        },
                    }
                ).done(
                    function (response) {
                        if (response) {
                            main.setStoreCatalogResponses(response);
                            $('span.trigger-product-price').trigger('simpleProductPrice-update')
                        }
                    }
                ).always(
                    function () {
                        $('.price-loader').hide();
                        $('.prices').show();
                        $('.action.tocart').prop('disabled', false);
                    }
                );
            },

            /**
             * Set Store Catalog Responses
             *
             * @param response
             */
            setStoreCatalogResponses: function (response) {

                if (response) {
                    var pricePrefix = main.getPricePrefix(),
                        strikeOutPricePrefix = main.getStrikeOutPricePrefix();

                    $.each(
                        response, function (index, value) {

                            var $priceArea = $('.' + pricePrefix + index),
                                $strikeOutPriceArea = $('.' + strikeOutPricePrefix + index);

                            //Handle Pricing
                            if ($priceArea.length > 0) {
                                $priceArea.html(main.getPriceHtml(value, index));
                            }

                            //Handle Strike Out Price
                             if ($strikeOutPriceArea.length > 0) {
                                 $strikeOutPriceArea.html(main.getStrikeOutPriceHtml(value, index));
                             }

                        }
                    );

                }
            },

            /**
             * Get Price HTML
             *
             * @param   value
             * @returns {string}
             */
            getPriceHtml: function (value, index) {
                var priceHtml = '';
                if (typeof value.display_price !== 'undefined') {
                    priceHtml = value.display_price;
                }
                return priceHtml;
            },
            /**
             * Get Price HTML
             *
             * @param   value
             * @returns {string}
             */
            getStrikeOutPriceHtml: function (value, index) {
                var strikeOutPriceHtml = '';
                if (typeof value.strikeout_price !== 'undefined') {
                    strikeOutPriceHtml = value.strikeout_price;
                }
                return strikeOutPriceHtml;
            },

        };
        return main;
    }
);
