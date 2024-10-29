define(
    [
        'Perficient_Payment/js/view/checkout/summary/paymentMethodFee'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return this.getPureValue() != 0;
            }
        });
    }
);