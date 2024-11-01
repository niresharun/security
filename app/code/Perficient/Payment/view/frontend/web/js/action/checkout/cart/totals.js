define(
    [
        'jquery',
        'ko',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/action/get-totals',
    ],
    function(
        $,
        ko,
        storage,
        urlBuilder,
        getTotalsAction
    ) {
        'use strict';

        return function (isLoading, payment) {
            var serviceUrl = urlBuilder.build('paymentmethodfee/checkout/totals');
            return storage.post(
                serviceUrl,
                JSON.stringify({payment: payment})
            ).done(
                function(response) {
                    if (!response.error) {
                        var deferred = $.Deferred();
                        isLoading(false);
                        getTotalsAction([], deferred);
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    //var error = JSON.parse(response.responseText);
                }
            ).always(
                function () {
                    isLoading(false);
                }
            );
        }
    }
);