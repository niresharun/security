/**
 * @api
 */
define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-shipping-information'
], function (quote, setShippingAction) {
    'use strict';

    return function (shippingMethod) {
        quote.shippingMethod(shippingMethod);
        if (shippingMethod !== null) {
            setShippingAction([]);
        }
    };
});
