define(
    [
        'uiComponent',
        'jquery',
        'ko'
    ],
    function(
        Component,
        $,
        ko
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Wendover_Checkout/shipping/fedexnotice'
            },

            initialize: function () {
                var self = this;
                this._super();
            }

        });
    }
);
