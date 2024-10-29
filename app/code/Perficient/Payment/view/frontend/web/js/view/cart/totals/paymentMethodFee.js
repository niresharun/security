
/*global define*/
define(
    [
        'Perficient_Payment/js/view/cart/summary/paymentMethodFee'
    ],
    function (Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Perficient_Payment/cart/totals/paymentMethodFee'
            },
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return this.getPureValue() != 0;
            }
        });
    }
);
