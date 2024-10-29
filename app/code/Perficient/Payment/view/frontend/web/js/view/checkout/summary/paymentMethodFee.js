define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Perficient_Payment/checkout/summary/paymentMethodFee'
            },
            totals: quote.getTotals(),

            isDisplayed: function() {
                return this.getPureValue() != 0;
            },

            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    var paymentFeeSegment = totals.getSegment('payment_method_fee');
                    if (paymentFeeSegment) {
                        price = paymentFeeSegment.value;
                    }
                }
                return this.getFormattedPrice(price);
            },

            getPureValue: function() {
                var price = 0;
                if (this.totals()) {
                    var paymentFeeSegment = totals.getSegment('payment_method_fee');
                    if (paymentFeeSegment) {
                        price = parseFloat(paymentFeeSegment.value);
                    }
                }
                return price;
            }
        });
    }
);