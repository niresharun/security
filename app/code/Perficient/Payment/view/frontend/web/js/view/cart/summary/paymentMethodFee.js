/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Perficient_Payment/cart/summary/paymentMethodFee'
            },
            totals: quote.getTotals(),

            isDisplayed: function() {
                return this.getPureValue() != 0;
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
            },

            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);