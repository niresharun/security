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
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Perficient_PaymentMethodAdditionalData/payment/checkmo'
        },
        getData: function () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'bankowner': $('#checkmo_bankowner').val()
                }
            };
        },

        /**
         * Returns send check to info.
         *
         * @return {*}
         */
        getMailingAddress: function () {
            return window.checkoutConfig.payment.checkmo.mailingAddress;
        },


        /**
         * Returns payable to info.
         *
         * @return {*}
         */
        getPayableTo: function () {
            return window.checkoutConfig.payment.checkmo.payableTo;
        },

        /**
         * Fix for ticket WENDOVER-454
         * To show custom message during checkout below payment method radio button
         * @returns {*}
         */
        getPaymentMethodFeeMessage: function () {
            var paymentFeeMessage = window.checkoutConfig.payment_fee_message;
            if (paymentFeeMessage) {
                var checkmoFeeMessage = paymentFeeMessage.checkmo;
                if (checkmoFeeMessage) {
                    return checkmoFeeMessage;
                }
            }

            return false;
        }
    });
});
