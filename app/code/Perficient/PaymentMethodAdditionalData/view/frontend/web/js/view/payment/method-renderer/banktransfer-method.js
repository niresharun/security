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
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'jquery'
    ],
    function (ko, Component, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Perficient_PaymentMethodAdditionalData/payment/banktransfer'
            },
            /*
            initObservable: function () {
            this._super()
            .observe([
            'allbank',
            'activebankowner'
            ]);
            return this;
            },*/
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'bankowner': $('#banktransfer_bankowner').val()
                    }
                };
            },
            /**
             * Get value of instruction field.
             * @returns {String}
             */
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }

        });
    }
);