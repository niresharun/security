define([
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/action/select-payment-method'
],function (_, wrapper, checkoutData, selectShippingMethodAction) {
    'use strict';

    return function (checkoutDataResolver) {

        var resolveShippingRates = wrapper.wrap(
            checkoutDataResolver.resolveShippingRates,
            function (originalResolveShippingRates, ratesData) {
                // if (ratesData.length > 1 ) {
                //     for (const prop in ratesData) {
                //         if (ratesData[prop].carrier_code === "flatrate") {
                //             delete ratesData[prop];
                //             ratesData.length = 1;
                //         }
                //     }
                // }

                if (ratesData.length === 1) {
                    //set shipping rate if we have only one available shipping rate
                    selectShippingMethodAction(ratesData[0]);

                    return;
                }
                return originalResolveShippingRates(ratesData);
            }
        );

        return _.extend(checkoutDataResolver, {
            resolveShippingRates: resolveShippingRates
        });
    };
});
