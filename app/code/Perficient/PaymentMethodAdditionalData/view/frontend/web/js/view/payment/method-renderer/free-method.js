/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */
/* @api */
/*browser:true*/
/*global define*/
define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'jquery'
], function (Component, quote, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perficient_PaymentMethodAdditionalData/payment/free'
        },
        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'bankowner': $('#free_bankowner').val()
                }
            };
        },

        /** Returns is method available */
        isAvailable: function () {
            return quote.totals()['grand_total'] <= 0;
        }
    });
});
