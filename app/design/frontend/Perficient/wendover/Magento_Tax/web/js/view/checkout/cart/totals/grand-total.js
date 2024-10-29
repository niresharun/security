/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */

define([
    'Magento_Tax/js/view/checkout/summary/grand-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals'
], function (Component, quote, totals) {
    'use strict';

    return Component.extend({
        totals: quote.getTotals(),

        /**
         * @override
         */
        isDisplayed: function () {
            return true;
        },

        /**
         * Remove shipping cost from cart Total
         * @returns {*|String}
         */
        getValue: function () {
            var price = 0;
            if (this.totals()) {
                price = totals.getSegment('grand_total').value - totals.getSegment('shipping').value;
            }

            return this.getFormattedPrice(price);
        }
    });
});
